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
require_once ('../includes/managesessions.php'); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	//check if its an ajax request, exit if not
	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {

	    $output = 'notajax';
	    die($output);
	}

	// --------------------------------------------------------------------------    	  	
	// Store the user and pass in local variables.
	// --------------------------------------------------------------------------  
	$first = 	filter_var(trim($_POST["invite_first"]), FILTER_SANITIZE_STRING);
	$last = 	filter_var(trim($_POST["invite_last"]), FILTER_SANITIZE_STRING);
	$pass = 	filter_var(trim($_POST["invite_password"]), FILTER_SANITIZE_STRING);
	$repeat = 	filter_var(trim($_POST["invite_password_repeat"]), FILTER_SANITIZE_STRING);
	$guid = 	filter_var(trim($_POST["invite_guid"]), FILTER_SANITIZE_STRING);
	$newguid = 	GetGUID();

	
	if(strlen($pass) > 7 && $pass == $repeat){
		//SQL INSERT GOES HERE
	    $passwordmd5 = sha1($pass);

		$query = "UPDATE users SET fname = '$first', lname = '$last', password = '$passwordmd5', validationGUID = '$newguid' ";
		$query .= "WHERE validationGUID = '$guid'; ";
		$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query);
		$numrows = mysqli_affected_rows($GLOBALS["___mysqli_ston"]); 
	
		if($numrows > 0){

			$query = "SELECT t1.id, t1.type, t1.email, t1.domain, t2.id, t1.fname, t1.lname FROM users t1 JOIN domains t2 on t1.domain = t2.domain WHERE t1.validationGUID='$newguid'"; 
			$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
			$numrows = mysqli_num_rows($result);

			// --------------------------------------------------------------------------  
			// If the number of rows is 1 that means that this user was found in our 
			// database.  So start a session and copy the userid into a session 
			// variable so it can be used across other pages.  Then since the user 
			// has logged in succesfully redirect them to the next page.
			// --------------------------------------------------------------------------  		
			if($numrows > 0)
			{
				$row = mysqli_fetch_array($result,  MYSQLI_ASSOC);
				
				session_start();
				$_SESSION['userid'] = $row['t1.id'];
				$_SESSION['type'] = $row['type'];
				$_SESSION['email'] = $row['email'];
				$_SESSION['domain'] = $row['domain'];
				$_SESSION['domain_id'] = $row['t2.id'];
				$_SESSION['username'] = $row['fname'] . " " . $row['lname'];


				echo('success');
			    die();
			}
			echo('notfound');
			die();
		} else {
			echo('error');
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