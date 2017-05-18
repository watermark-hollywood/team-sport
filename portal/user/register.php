<?php 

// ---------------------------------------------------------------------------------
//  FILENAME:      register-admin.php
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
//    02/24/17  RAM     Created this file
// ---------------------------------------------------------------------------------
 
// --------------------------------------------------------------------------    	  	
// Connect to the database. Include utility functions and mail scripts
// --------------------------------------------------------------------------  
require_once ('../includes/swdb_connect.php'); 
require_once ('../includes/utilityfunctions.php');
require_once ('../assets/libs/mailer/class.phpmailer.php');
require_once ('../assets/libs/mailer/class.smtp.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	//check if its an ajax request, exit if not
	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {

	    $output = 'notajax';
	    die($output);
	}

	// --------------------------------------------------------------------------    	  	
	// Store the user and pass in local variables.
	// --------------------------------------------------------------------------  
	$first = 	ucfirst(filter_var(trim($_POST["register_first"]), FILTER_SANITIZE_STRING));
	$last = 	ucfirst(filter_var(trim($_POST["register_last"]), FILTER_SANITIZE_STRING));
	$email = 	filter_var(trim($_POST["register_email"]), FILTER_SANITIZE_STRING);
	$pass = 	filter_var(trim($_POST["register_password"]), FILTER_SANITIZE_STRING);
	$repeat = 	filter_var(trim($_POST["register_password_repeat"]), FILTER_SANITIZE_STRING);
	$type = 	filter_var(trim($_POST["register_type"]), FILTER_SANITIZE_STRING);

	//portal administrator registering, get the domain they entered into the form
	if(($type & 16) > 0) {
		$domain = preg_replace('/[^a-z]+/i', '', $_POST['register_domain']); 
		$domain = strtolower($domain);
	}
	
	//portal user registering, get the domain of the portal they are registering from
	if(($type & 8) > 0) {
		$domain = ExtractSubdomains($_SERVER['HTTP_HOST']);
	}
	

	
	if(strlen($pass) > 7 && $pass == $repeat){
		// --------------------------------------------------------------------------  
		// Look the user up in the database by email address.
		// --------------------------------------------------------------------------
		if(($type & 16) > 0) {  
			$query = "SELECT * FROM users WHERE email = '$email' OR domain = '$domain';"; 
		}
		if(($type & 8) > 0) {
			$query = "SELECT * FROM users WHERE email = '$email';"; 
		}
		$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
		$numrows = mysqli_num_rows($result);
		
		
		// --------------------------------------------------------------------------  
		// If the number of rows is 1 that means that this email or domain was found in our 
		// database. Redirect them back to registration with an error
		// --------------------------------------------------------------------------  	

		if($numrows == 1)
		{
			$output = 'exists';
		    die($output);
		}

		// --------------------------------------------------------------------------  
		// If nothing was found, register the person
		// --------------------------------------------------------------------------  
		else
		{
			//SQL INSERT GOES HERE
			$guid = GetGUID();
		    $passwordmd5 = sha1($pass);
		    $typemask = 0;

		    //type is the bitmask for the users 8 represents a portal user
		    if(($type & 8) > 0){
		    	$typemask = 8;
		    }
		    
		    //type is the bitmask for the users 16 represents a portal admin
		    if(($type & 16) > 0){
		    	$typemask = 16;
		    }

		    //adding 1 to the typemask because the email is not verified
		    $typemask = $typemask +1;

		    //also adding 2 to the bitmask if manual validation is turned on 
		    $validate = CheckManualValidate();
		    if($validate == true){
		    	$typemask = $typemask + 2;
		    } else {
		    	//always add manual validation to portal users (for now)
		    	if(($type & 8) > 0){
		    		$typemask = $typemask + 2;
		    	}
		    }


			$query = "INSERT INTO users (fname, lname, email, password, domain, type, validationGUID) VALUES ('$first', '$last', '$email', '$passwordmd5', '$domain', '$typemask', '$guid')";
			$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
		
			if($result){
				// --------------------------------------------------------------------------  
			    // Load the subject string.
			    // --------------------------------------------------------------------------  
			    $strSubject = "Welcome to Project Oslo";
			    
			    // --------------------------------------------------------------------------
			    // Set the From/To Email and From/To Name fields.
			    // --------------------------------------------------------------------------  
			    $strFromName  	= "Project Oslo";
			    $strFromEmail 	= "no-reply@project-oslo.com";
			    $strToName  	= $first." ".$last;
			    $strToEmail 	= $email;
			    $strAltBody		= "To view this message, please use an HTML compatible email viewer."; 

			    // --------------------------------------------------------------------------  
			    // Grab today's date;
			    // --------------------------------------------------------------------------  
			    $strDate = date("F j, Y");
			    $date_made = date('Y-m-d');   
			                                    
			    // --------------------------------------------------------------------------   
			    // Load the email content from a file, and store it in a string 
			    // --------------------------------------------------------------------------  
			    if(($type & 8) > 0){
				    $strEmailBodyTemp = file_get_contents('../assets/email_creatives/user_contact.html');
				}
				if(($type & 16) >0){
				    $strEmailBodyTemp = file_get_contents('../assets/email_creatives/email_contact.html');
				}

			    // --------------------------------------------------------------------------  
			    // Now perform variable substitution on the email body.. so we are left with
			    // email body with data in it.
			    // --------------------------------------------------------------------------  
			    $original = array("%%email%%", "%%domain%%", "%%guid%%");
			    $replace  = array($email, $_SERVER['HTTP_HOST'], $guid);
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
			    	$output = 'mailerr';
				    die($output);
			    } 

			    // --------------------------------------------------------------------------           
			    // If the email was successfully mailed.
			    // --------------------------------------------------------------------------       
				else 
				{
					$output = 'success';
				    die($output);
				}





			} else {
				$output = 'dberr';
			    die($output);
			}		
		}
	} else {
		//----------------------------------------------------------------------------
		// The passwords didn't match or were too short
		//----------------------------------------------------------------------------
		$output = 'pass';
	    die($output);
	}
}

?>