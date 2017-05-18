<?php 
	set_time_limit(0);

// ---------------------------------------------------------------------------------
//  FILENAME:      create-domain.php
//
//  DESCRIPTION:   Domain Creation Script 
//
//  NOTES:         This source script is used to create subdomains for the current
//				   website.
//
//  COPYRIGHTS:    Copyright (c) Watermark 2016
//                 All Rights Reserved                                     
//
//  HISTORY:
//
//    MM/DD/YY  WHO        NOTES
// ---------------------------------------------------------------------------------
//    02/27/17  RAM     Created this file
// ---------------------------------------------------------------------------------
 
// --------------------------------------------------------------------------    	  	
// Connect to the database. Include utility functions
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
		$row = mysqli_fetch_array($result,  MYSQLI_ASSOC);

		//wait 10 minutes to send email
		sleep(600);

		// --------------------------------------------------------------------------  
	    // Load the subject string.
	    // --------------------------------------------------------------------------  
	    $strSubject = "Welcome to Project Oslo";
	    
	    // --------------------------------------------------------------------------
	    // Set the From/To Email and From/To Name fields.
	    // --------------------------------------------------------------------------  
	    $strFromName  	= "Project Oslo";
	    $strFromEmail 	= "no-reply@project-oslo.com";
	    $strToName  	= $row['fname']." ".$row['lname'];
	    $strToEmail 	= $row['email'];
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
	    $replace  = array($row['email'], $row['domain'], $row['fname'], $row['lname']);
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
		$output = "userNotFound";
		die($output);
	}
}




if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	//check if its an ajax request, exit if not
	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {

	    $output = 'notajax';
	    die($output);
	}

	// --------------------------------------------------------------------------    	  	
	// Store the local variables.
	// --------------------------------------------------------------------------  
	$domain = 	strtolower(filter_var(trim($_POST["domain"]), FILTER_SANITIZE_STRING));
	$admin_id = 	filter_var(trim($_POST["admin_id"]), FILTER_SANITIZE_STRING);

	
	if($_POST["domain"] && $_POST["admin_id"]){
		// --------------------------------------------------------------------------  
		// Look the domain up in the database.
		// --------------------------------------------------------------------------  
		$query = "SELECT * FROM domains WHERE domain = '$domain';"; 
		$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
		$numrows = mysqli_num_rows($result);

		// --------------------------------------------------------------------------  
		// If the number of rows is 1 that means that this domain was found in our 
		// database. Redirect them back to verify with an error
		// --------------------------------------------------------------------------  	

		if($numrows == 1)
		{
			$output = 'exists';
		    die($output);
		}

		// --------------------------------------------------------------------------  
		// If nothing was found, add the domain
		// --------------------------------------------------------------------------  
		else
		{
			CreateSubdomain($domain, "portal");

			//SQL INSERT GOES HERE
			$query = "INSERT INTO domains (domain, admin_id) VALUES ('$domain', '$admin_id')";
			$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
			$last_id = $GLOBALS["___mysqli_ston"]->insert_id;
			

			$query = "INSERT INTO domain_styles (domain_id, style_name, property_name, property_value) VALUES ('$last_id', 'body', 'background-color', '#FFFFFF')";
			$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query);
			
			$query = "INSERT INTO domain_styles (domain_id, style_name, property_name, property_value) VALUES ('$last_id', 'body', 'color', '#333333')";
			$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query);
			
			$query = "INSERT INTO domain_styles (domain_id, style_name, property_name, property_value) VALUES ('$last_id', '.bg-primary', 'background-color', '#0275d8')";
			$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query);
			
			$query = "INSERT INTO domain_styles (domain_id, style_name, property_name, property_value) VALUES ('$last_id', '.navbar-inverse .navbar-nav .nav-link', 'color', '#ededed')";
			$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query);

			$query = "INSERT INTO domain_styles (domain_id, style_name, property_name, property_value) VALUES ('$last_id', 'logo-image', 'name', 'logo.png')";
			$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query);
			$last_id = $GLOBALS["___mysqli_ston"]->insert_id;
		
			if($last_id > 0){
				if (!file_exists('../domains/'.$domain.'')) {
    				
    				$srcfile = '../assets/images/logo.png';
    				$destfile = '../domains/'.$domain.'/images/logo.png';
					mkdir(dirname($destfile), 0777, true);
					copy($srcfile, $destfile);
    				
    				$srcfile = '../assets/images/logo.png';
    				$destfile = '../domains/'.$domain.'/images/tn/logo.png';
					mkdir(dirname($destfile), 0777, true);
					copy($srcfile, $destfile);
    				
    				$srcfile = '../assets/plugins/orakuploader/images/loader.gif';
    				$destfile = '../domains/'.$domain.'/images/loader.gif';
    				copy($srcfile, $destfile);
    				
    				$srcfile = '../assets/plugins/orakuploader/images/no-image.jpg';
    				$destfile = '../domains/'.$domain.'/images/no-image.jpg';
    				copy($srcfile, $destfile);

    				mkdir('../domains/'.$domain.'/css', 0777, true);
    				mkdir('../domains/'.$domain.'/images/products', 0777, true);
    				mkdir('../domains/'.$domain.'/images/products/tn', 0777, true);
				}

				$raw_css = file_get_contents('../assets/templates/css_template.css');
				$css_file = sprintf($raw_css, '#FFFFFF', '#333333', '#0275d8', '#ededed', '#ededed');

				$css_written = file_put_contents('../domains/'.$domain.'/css/portal.css', $css_file, FILE_USE_INCLUDE_PATH);

				sendDomainEmail($admin_id);

				echo("success");
				die();
			} else {
				//----------------------------------------------------------------------------
				// The insert failed
				//----------------------------------------------------------------------------
				$output = 'error';
			    die($output);
			}
		}
	}
}

?>