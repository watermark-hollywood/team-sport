<?php 
	set_time_limit(0);

// ---------------------------------------------------------------------------------
//  FILENAME:      update-user-type.php
//
//  DESCRIPTION:   Update User Access Type 
//
//  NOTES:         This source script is used to update user access levels
//
//  COPYRIGHTS:    Copyright (c) Watermark 2017
//                 All Rights Reserved                                     
//
//  HISTORY:
//
//    MM/DD/YY  WHO        NOTES
// ---------------------------------------------------------------------------------
//    03/10/17  RAM     Created this file
// ---------------------------------------------------------------------------------
 
// --------------------------------------------------------------------------    	  	
// Connect to the database. Include utility functions
// --------------------------------------------------------------------------  
require_once ('../includes/swdb_connect.php'); 
require_once ('../includes/utilityfunctions.php');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	//check if its an ajax request, exit if not
	
	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {

	    $output = 'notajax';
	    die($output);
	}
	
	// --------------------------------------------------------------------------    	  	
	// Store the local variables.
	// --------------------------------------------------------------------------  
	$user_id 	= 	filter_var(trim($_POST["user_id"]), FILTER_SANITIZE_STRING);
	$user_verify = 	filter_var(trim($_POST["user_verified"]), FILTER_SANITIZE_STRING);
	$original_type = 	filter_var(trim($_POST["original_type"]), FILTER_SANITIZE_STRING);
	$user_type = $original_type;

	if(($original_type & 1) > 0){
		if($user_verify == 'true') {
			$user_type = $user_type - 1;
		}
	} else {
		if($user_verify == 'false') {
			$user_type = $user_type + 1;
		}
	}
	
	if($_POST["user_id"]){
		// --------------------------------------------------------------------------  
		// Look the domain up in the database.
		// --------------------------------------------------------------------------  
		$query = "UPDATE users SET type = '$user_type' WHERE id = '$user_id';"; 
		$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
		$numrows = mysqli_affected_rows($GLOBALS["___mysqli_ston"]);

		// --------------------------------------------------------------------------  
		// If the number of rows is 1 that means that this domain was found in our 
		// database. Redirect them back to verify with an error
		// --------------------------------------------------------------------------  	

		if($numrows == 1)
		{
			echo("success");
		    die();
		} else {
			echo("not found");
			die();
		}
	} else {
		echo("missing params");
		die();
	}
}


?>