<?php 
	set_time_limit(0);

// ---------------------------------------------------------------------------------
//  FILENAME:      admin-accept-invite.php
//
//  DESCRIPTION:   User Registration System 
//
//  NOTES:         This source script is used to register admin users for our 
//                 system.  The userid and hashed password are stored in the 
//                 database. Note we are using the PHP Password Hash functions 
//				   which are way better than MD5 or SHA1.
//
//  COPYRIGHTS:    Copyright (c) Watermark 2016
//                 All Rights Reserved                                     
//
//  HISTORY:
//
//    MM/DD/YY  WHO        NOTES
// ---------------------------------------------------------------------------------
//    03/20/17  RAM     Created this file
// ---------------------------------------------------------------------------------
 
// --------------------------------------------------------------------------    	  	
// Connect to the database. Include utility functions and mail scripts
// --------------------------------------------------------------------------  
require_once ('../includes/swdb_connect.php'); 
require_once ('../includes/utilityfunctions.php');
require_once ('createsubdomain.php');
require_once ('../assets/libs/mailer/class.phpmailer.php');
require_once ('../assets/libs/mailer/class.smtp.php');

function sendDomainEmail($userid) {

	$query = "SELECT * FROM users WHERE id = '$userid';"; 
	$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
	$numrows = mysqli_num_rows($result);

	if($numrows == 1)
	{
		$mailrow = mysqli_fetch_array($result,  MYSQLI_ASSOC);

		//wait 10 minutes to send email
		sleep(600);

		// --------------------------------------------------------------------------  
	    // Load the subject string.
	    // --------------------------------------------------------------------------  
	    $strSubject = "Welcome to your Project Oslo Portal";
	    
	    // --------------------------------------------------------------------------
	    // Set the From/To Email and From/To Name fields.
	    // --------------------------------------------------------------------------  
	    $strFromName  	= "Project Oslo";
	    $strFromEmail 	= "no-reply@project-oslo.com";
	    $strToName  	= $mailrow['fname']." ".$mailrow['lname'];
	    $strToEmail 	= $mailrow['email'];
	    $strAltBody		= "To view this message, please use an HTML compatible email viewer."; 

	    // --------------------------------------------------------------------------  
	    // Grab today's date;
	    // --------------------------------------------------------------------------  
	    $strDate = date("F j, Y");
	    $date_made = date('Y-m-d');   
	                                    
	    // --------------------------------------------------------------------------   
	    // Load the email content from a file, and store it in a string 
	    // --------------------------------------------------------------------------  
	    $strEmailBodyTemp = file_get_contents('../assets/email_creatives/domain_contact.html');
		
	    // --------------------------------------------------------------------------  
	    // Now perform variable substitution on the email body.. so we are left with
	    // email body with data in it.
	    // --------------------------------------------------------------------------  
	    $original = array("%%email%%", "%%domain%%", "%%fname%%", "%%lname%%");
	    $replace  = array($mailrow['email'], $mailrow['domain'], $mailrow['fname'], $mailrow['lname']);
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
	    	echo('mailerr');
		    die();
	    } 

	    // --------------------------------------------------------------------------           
	    // If the email was successfully mailed.
	    // --------------------------------------------------------------------------       
		else 
		{
			echo('success');
		    die();
		}
	} else {
		echo("userNotFound");
		die();
	}
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	//check if its an ajax request, exit if not
	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {

	    echo('notajax');
	    die();
	}




	// --------------------------------------------------------------------------    	  	
	// Store the user and pass in local variables.
	// --------------------------------------------------------------------------  
	$userid = 	filter_var(trim($_POST["create_admin_id"]), FILTER_SANITIZE_STRING);
	sendDomainEmail($userid);
}

?>