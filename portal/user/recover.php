<?php 

// ---------------------------------------------------------------------------------
//  FILENAME:      recover.php
//
//  DESCRIPTION:   Password Recovery script 
//
//  NOTES:         This sends an email with a guid link that lets users reset their
//					passwords if they lose or forget them
//
//  COPYRIGHTS:    Copyright (c) Watermark 2017
//                 All Rights Reserved                                     
//
//  HISTORY:
//
//    MM/DD/YY  WHO        NOTES
// ---------------------------------------------------------------------------------
//    03/14/17  RAM     Created this file
// ---------------------------------------------------------------------------------
 
// --------------------------------------------------------------------------    	  	
// Connect to the database.
// --------------------------------------------------------------------------  
require_once ('../includes/swdb_connect.php'); 
require_once ('../includes/utilityfunctions.php'); 
require_once ('../assets/libs/mailer/class.phpmailer.php');
require_once ('../assets/libs/mailer/class.smtp.php');

// --------------------------------------------------------------------------    	  	
// Store the user and pass in local variables.
// --------------------------------------------------------------------------  
$email = filter_var(trim($_POST["recover_email"]), FILTER_SANITIZE_STRING);

// --------------------------------------------------------------------------  
// Look the user up in the database by email address.
// --------------------------------------------------------------------------  

$query = "SELECT * FROM users WHERE email='$email'"; 
$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
$numrows = mysqli_num_rows($result);

// --------------------------------------------------------------------------  
// If the number of rows is 1 that means that this user was found in our 
// database.  So start a session and copy the userid into a session 
// variable so it can be used across other pages.  Then since the user 
// has logged in succesfully redirect them to the next page.
// --------------------------------------------------------------------------  		
if($numrows == 1)
{
	$newguid = GetGUID();
	$row = mysqli_fetch_array($result,  MYSQLI_ASSOC);

	$query = "UPDATE users SET validationGUID='$newguid' WHERE email = '$email'";
	$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
	$affected = mysqli_affected_rows($GLOBALS["___mysqli_ston"]);

	if($affected == 1){
		// --------------------------------------------------------------------------  
	    // Load the subject string.
	    // --------------------------------------------------------------------------  
	    $strSubject = "Project Oslo Password Recovery";
	    
	    // --------------------------------------------------------------------------
	    // Set the From/To Email and From/To Name fields.
	    // --------------------------------------------------------------------------  
	    $strFromName  	= "Project Oslo";
	    $strFromEmail 	= "no-reply@project-oslo.com";
	    $strToName  	= $row["fname"]." ".$row["lname"];
	    $strToEmail 	= $row["email"];
	    $strAltBody		= "To view this message, please use an HTML compatible email viewer."; 

	    // --------------------------------------------------------------------------  
	    // Grab today's date;
	    // --------------------------------------------------------------------------  
	    $strDate = date("F j, Y");
	    $date_made = date('Y-m-d');   
	                                    
	    // --------------------------------------------------------------------------   
	    // Load the email content from a file, and store it in a string 
	    // --------------------------------------------------------------------------  
	    //This is a portal user email creative
	    $strEmailBodyTemp = file_get_contents('../assets/email_creatives/password_recover.html');

	    // --------------------------------------------------------------------------  
	    // Now perform variable substitution on the email body.. so we are left with
	    // email body with data in it.
	    // --------------------------------------------------------------------------  
	    $original = array("%%email%%", "%%domain%%", "%%guid%%");
	    $replace  = array($row["email"], $_SERVER['HTTP_HOST'], $newguid);
	    $strEmailBody = str_replace($original, $replace, $strEmailBodyTemp);

	    // --------------------------------------------------------------------------  
	    // Define our headers
	    // --------------------------------------------------------------------------  
	    $headers  = 'MIME-Version: 1.0' . "\r\n";
	    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	    $headers .= 'From: ' . $strFromEmail;
	    
	    // --------------------------------------------------------------------------  
	    // Set the default timezone
	    // --------------------------------------------------------------------------  
	    date_default_timezone_set('America/Chicago');

	    // --------------------------------------------------------------------------           
	    // Instantiate the Mailer class.
	    // --------------------------------------------------------------------------  
	    $mail = new PHPMailer();

	    //$mail->IsSMTP();										// telling the class to use SMTP
	    $mail->IsHTML(true);									// telling the class to use HTML Email
	    $mail->SMTPDebug  = 1;                                  // enables SMTP debug information (for testing)
																//      1 = errors and messages
	                                                            //      2 = messages only
	    $mail->SMTPAuth   = true;                               // enable SMTP authentication
	    $mail->SMTPSecure = 'tls';								// secure transfer enabled REQUIRED use SSL
	    $mail->Host       = "smtp.gmail.com";				// sets the SMTP server
	    $mail->Port       = 587;								// set the SMTP port for the EMAIL server
	    $mail->Username   = "admin@watermarkdigital.com";             // SMTP account username
	    $mail->Password   = "W@termark1!";                      // SMTP account password
	    $mail->SetFrom($strFromEmail, $strFromName);			// From name and email address
	    $mail->AddReplyTo($strFromEmail, $strFromName);	        // Reply To name and email address
	    //$mail->AddBCC($partnerBCC);                             // BCC contact at partner site 	
	    $mail->Subject      = $strSubject;						// Subject
	    $mail->AltBody      = $strAltBody; 						// Alternate body for non HTML email clients
	    $mail->Body         = $strEmailBody;					// Email Body 

	    // --------------------------------------------------------------------------           
	    // This is whom the email is going to
	    // --------------------------------------------------------------------------     
	    $mail->AddAddress($strToEmail, $strToName);
	    
	    // --------------------------------------------------------------------------           
	    // This is the list of people on BCC list
	    // --------------------------------------------------------------------------         
	    //$list=array();
	    //foreach($list as $bccer)
	    //{
	//     $mail->AddBCC($bccer);
	    //}

	    // --------------------------------------------------------------------------           
	    // If we got an error message.
	    // --------------------------------------------------------------------------       
	    if(!$mail->Send()) 
	    {
	    	echo('mail_error');
		    die();
	    } else {
	    	echo("success");
	    	die();
	    }
	} else {
		echo('update_error');
		die();
	}
} else {
	// --------------------------------------------------------------------------  		
	// Otherwise the login attempt was bogus so redirect them back to the 
	// login page with the invalid authorization error.
	// --------------------------------------------------------------------------  	
	echo("error");
	die();
}

?>
