<?php 

// ---------------------------------------------------------------------------------
//  FILENAME:      invite-domain-admin.php
//
//  DESCRIPTION:   Admin Invite System 
//
//  NOTES:         Inserts invited users into the database and sends them an email.
//
//  COPYRIGHTS:    Copyright (c) Watermark 2017
//                 All Rights Reserved                                     
//
//  HISTORY:
//
//    MM/DD/YY  WHO        NOTES
// ---------------------------------------------------------------------------------
//    03/13/17  RAM     Created this file
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
$inviter = filter_var(trim($_POST["inviter-name"]), FILTER_SANITIZE_STRING);
$invitee = filter_var(trim($_POST["invitee-email"]), FILTER_SANITIZE_STRING);
$guid = GetGUID();

// --------------------------------------------------------------------------  
// Look the user up in the database by email address.
// --------------------------------------------------------------------------  

$query = "SELECT * FROM users WHERE email='$invitee'"; 
$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
$numrows = mysqli_num_rows($result);

// --------------------------------------------------------------------------  
// If the number of rows is 1 that means that this user was found in our 
// database.  So don't invite them.
// --------------------------------------------------------------------------  		
if($numrows == 1)
{
	echo("exists");
	die();
} else {

	// --------------------------------------------------------------------------  
	// Otherwise insert the invited person into the database, usertype 12 is
	// 8 - portal user + 4 - invited by admin
	// -------------------------------------------------------------------------- 

	$query = "INSERT INTO users (email, type, validationGUID) ";
	$query .= "VALUES ('$invitee', '20', '$guid');";
	$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
	$numrows = mysqli_affected_rows($GLOBALS["___mysqli_ston"]);

	if($numrows == 1) {
		// --------------------------------------------------------------------------  
	    // Load the subject string.
	    // --------------------------------------------------------------------------  
	    $strSubject = "You've been invited to create a portal at www.project-oslo.com";
	    
	    // --------------------------------------------------------------------------
	    // Set the From/To Email and From/To Name fields.
	    // --------------------------------------------------------------------------  
	    $strFromName  	= "Project Oslo";
	    $strFromEmail 	= "no-reply@project-oslo.com";
	    $strToEmail 	= $invitee;
	    $strToName		= "";
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
	    $strEmailBodyTemp = file_get_contents('../assets/email_creatives/admin_invite.html');
		
	    // --------------------------------------------------------------------------  
	    // Now perform variable substitution on the email body.. so we are left with
	    // email body with data in it.
	    // --------------------------------------------------------------------------  
	    $original = array("%%inviter%%", "%%guid%%");
	    $replace  = array($inviter, $guid);
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
	    	echo("mailerr");
		    die();
	    } 

	    // --------------------------------------------------------------------------           
	    // If the email was successfully mailed.
	    // --------------------------------------------------------------------------       
		else 
		{
			echo("success");
			die();
		} 
	}
}

?>
