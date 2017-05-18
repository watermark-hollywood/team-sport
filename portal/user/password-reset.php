<?php 

// ---------------------------------------------------------------------------------
//  FILENAME:      password-reset.php
//
//  DESCRIPTION:   Password Reset script 
//
//  NOTES:         Resets the user's password if they have the proper guid from a
//					recovery email
//
//  COPYRIGHTS:    Copyright (c) Watermark 2017
//                 All Rights Reserved                                     
//
//  HISTORY:
//
//    MM/DD/YY  WHO        NOTES
// ---------------------------------------------------------------------------------
//    03/15/17  RAM     Created this file
// ---------------------------------------------------------------------------------
 
// --------------------------------------------------------------------------    	  	
// Connect to the database.
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
	$pass = filter_var(trim($_POST["reset_password"]), FILTER_SANITIZE_STRING);
	$passrepeat = filter_var(trim($_POST["reset_password_repeat"]), FILTER_SANITIZE_STRING);
	$guid = filter_var(trim($_POST["email_guid"]), FILTER_SANITIZE_STRING);


	if(strlen($pass) > 7 && $pass == $passrepeat){
		// --------------------------------------------------------------------------  
		// Look the user up in the database by guid sent in the email link 
		// --------------------------------------------------------------------------  

		$query = "SELECT * FROM users WHERE validationGUID='$guid'"; 
		$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
		$numrows = mysqli_num_rows($result);

		// --------------------------------------------------------------------------  
		// If the number of rows is 1 that means that this user was found in our 
		// database.  
		// --------------------------------------------------------------------------  		
		if($numrows == 1)
		{

			$md5password = sha1($pass);
			$newguid = GetGUID();
			$row = mysqli_fetch_array($result,  MYSQLI_ASSOC);

			$query = "UPDATE users SET validationGUID='$newguid', password='$md5password' WHERE validationGUID = '$guid'";
			$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
			$affected = mysqli_affected_rows($GLOBALS["___mysqli_ston"]);

			if($affected == 1){
				//----------------------------------------------------------------------
				// update succeeded. since we trust their guid and therefore their email
				// go ahead and log them in.
				//----------------------------------------------------------------------
				session_start();
				$_SESSION['userid'] = $row['id'];
				$_SESSION['type'] = $row['type'];
				$_SESSION['email'] = $row['email'];
				$_SESSION['domain'] = $row['domain'];
				$_SESSION['domain_id'] = $domainResult['id'];
				$_SESSION['username'] = $row['fname'] . " " . $row['lname'];

				$output = "success";
				die($output);

				echo('success');
				die();
				
			} else {
				//-----------------------------------------------------------------------
				// update failed, maybe they already tried this guid before and it's only
				// good once.
				//-----------------------------------------------------------------------
				echo('bad-link-error');
				die();
			}
		} else {
			// --------------------------------------------------------------------------  		
			// Never found the guid in the first place, must have been used before
			// --------------------------------------------------------------------------  	
			echo("bad-link-error");
			die();
		}
	} else {
		// --------------------------------------------------------------------------  		
		// Password too short, or doesn't match.
		// -------------------------------------------------------------------------- 
		echo("bad-pw-error");
		die();
	}
}

?>
