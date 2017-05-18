<?php 

// ---------------------------------------------------------------------------------
//  FILENAME:      login.php
//
//  DESCRIPTION:   Login and Authentication System 
//
//  NOTES:         This source script is used to authenticate users for our 
//                 system.  The userid and hashed password are stored in the 
//                 database. The script checks to see if the user is a valid user 
//                 and if so provides access to the subsequent pages.  Note we 
//                 are using the PHP Password Hash functions which are way better 
//                 than MD5 or SHA1.
//
//  COPYRIGHTS:    Copyright (c) Watermark 2016
//                 All Rights Reserved                                     
//
//  HISTORY:
//
//    MM/DD/YY  WHO        NOTES
// ---------------------------------------------------------------------------------
//    01/01/16  UJS     Created this file
// ---------------------------------------------------------------------------------
 
// --------------------------------------------------------------------------    	  	
// Connect to the database.
// --------------------------------------------------------------------------  
require_once ('../includes/swdb_connect.php'); 
require_once ('../includes/managesessions.php'); 
require_once ('../assets/libs/mailer/class.phpmailer.php');
require_once ('../assets/libs/mailer/class.smtp.php');

// --------------------------------------------------------------------------    	  	
// The PHP Password Hash functions were introduced in 5.3.7, so make sure
// the version of PHP on the server is that version at least. If not, then 
// return a 403 forbidden error.
// -------------------------------------------------------------------------- 
if (version_compare(phpversion(), '5.3.7', '<')) 
{
    header( "Location: index.php?invauth=403" );
}

// --------------------------------------------------------------------------    	  	
// Store the user and pass in local variables.
// --------------------------------------------------------------------------  
$user = filter_var(trim($_POST["login_email"]), FILTER_SANITIZE_STRING);
$pass = filter_var(trim($_POST["login_password"]), FILTER_SANITIZE_STRING);

// --------------------------------------------------------------------------  
// Look the user up in the database by email address.
// --------------------------------------------------------------------------  

$query = "SELECT * FROM users WHERE email='$user'"; 
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
	$row = mysqli_fetch_array($result,  MYSQLI_ASSOC);

	// --------------------------------------------------------------------------  
	// Grab the stored hash for the user and store it. Then verify the password
	// comparing the text version against the stored password hash.
	// --------------------------------------------------------------------------  
	$md5password = sha1($pass);
	if($md5password == $row['password']) {
		
		//unverified email trying to log in
		if(($row['type'] & 1) > 0) {
			// --------------------------------------------------------------------------  
		    // Load the subject string.
		    // --------------------------------------------------------------------------  
		    $strSubject = "Welcome to Project Oslo";
		    
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
		    if(($row["type"] & 8) > 0){
			    $strEmailBodyTemp = file_get_contents('../assets/email_creatives/user_contact.html');
			}

		    //This is a portal admin email creative
		    if(($row["type"] & 16) > 0){
			    $strEmailBodyTemp = file_get_contents('../assets/email_creatives/email_contact.html');
			}


		    // --------------------------------------------------------------------------  
		    // Now perform variable substitution on the email body.. so we are left with
		    // email body with data in it.
		    // --------------------------------------------------------------------------  
		    $original = array("%%email%%", "%%domain%%", "%%guid%%");
		    $replace  = array($row["email"], $_SERVER['HTTP_HOST'], $row["validationGUID"]);
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
				$output = "unverified";
				die($output);
			} 
		}

		//unapproved by admin trying to log in
		if(($row['type'] & 2) > 0) {
			if(($row["type"] & 8) > 0){
				$output = "unapproved-user";
				die($output);
			} 
			if(($row["type"] & 16) > 0){
				$output = "unapproved-admin";
				die($output);
			}
		}

		$admin_id = $row['domain'];
		$query = "SELECT id FROM domains WHERE domain='$admin_id'"; 
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
			$domainResult = mysqli_fetch_array($result,  MYSQLI_ASSOC);
		}

		$_SESSION['userid'] = $row['id'];
		$_SESSION['type'] = $row['type'];
		$_SESSION['email'] = $row['email'];
		$_SESSION['domain'] = $row['domain'];
		$_SESSION['domain_id'] = $domainResult['id'];
		$_SESSION['username'] = $row['fname'] . " " . $row['lname'];

		$output = "success";
		die($output);
	} else {

		$output = "error";
		die($output);
	}

} else {
	// --------------------------------------------------------------------------  		
	// Otherwise the login attempt was bogus so redirect them back to the 
	// login page with the invalid authorization error.
	// --------------------------------------------------------------------------  	
	$output = "not found";
	die($output);
}

?>
