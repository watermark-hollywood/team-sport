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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	//check if its an ajax request, exit if not
	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {

	    echo('notajax');
	    die();
	}




	// --------------------------------------------------------------------------    	  	
	// Store the user and pass in local variables.
	// --------------------------------------------------------------------------  
	$first = 	filter_var(trim($_POST["invite_first"]), FILTER_SANITIZE_STRING);
	$last = 	filter_var(trim($_POST["invite_last"]), FILTER_SANITIZE_STRING);
	$pass = 	filter_var(trim($_POST["invite_password"]), FILTER_SANITIZE_STRING);
	$repeat = 	filter_var(trim($_POST["invite_password_repeat"]), FILTER_SANITIZE_STRING);
	$guid = 	filter_var(trim($_POST["invite_guid"]), FILTER_SANITIZE_STRING);
	$domain = 	preg_replace('/[^a-z]+/i', '', $_POST['invite_domain']); 
	$domain = 	strtolower($domain);
	$newguid = 	GetGUID();

	
	if(strlen($pass) > 7 && $pass == $repeat){
		//SQL INSERT GOES HERE
	    $passwordmd5 = sha1($pass);

		$query = "UPDATE users SET fname = '$first', lname = '$last', password = '$passwordmd5', validationGUID = '$newguid', domain = '$domain' ";
		$query .= "WHERE validationGUID = '$guid'; ";
		$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query);
		$numrows = mysqli_affected_rows($GLOBALS["___mysqli_ston"]);
	
		if($numrows > 0){

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
				echo('exists');
			    die();
			} else {

				$query = "SELECT * FROM users WHERE validationGUID = '$newguid';"; 
				$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
				$numrows = mysqli_num_rows($result);

				if($numrows == 1)
				{
					$row = mysqli_fetch_array($result,  MYSQLI_ASSOC);
					$admin_id = $row['id'];

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
						}

						$raw_css = file_get_contents('../assets/templates/css_template.css');
						$css_file = sprintf($raw_css, '#FFFFFF', '#333333', '#0275d8', '#ededed', '#ededed');

						$css_written = file_put_contents('../domains/'.$domain.'/css/portal.css', $css_file, FILE_USE_INCLUDE_PATH);

						echo("success");
						die();
					}
				} else {
					//----------------------------------------------------------------------------
					// The insert failed
					//----------------------------------------------------------------------------
					echo('error');
				    die();
				}
			} 	
		} else {
			echo('not found');
			die();
		}
	} else {
		//----------------------------------------------------------------------------
		// The passwords didn't match or were too short
		//----------------------------------------------------------------------------
		echo('pass');
	    die();
	}
}

?>