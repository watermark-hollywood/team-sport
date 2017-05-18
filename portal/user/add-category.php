<?php 
	set_time_limit(0);

// ---------------------------------------------------------------------------------
//  FILENAME:      add-category.php
//
//  DESCRIPTION:   Add Platform Categories 
//
//  NOTES:         This source script is used to Add Categories to the OSLO Platform
//
//  COPYRIGHTS:    Copyright (c) Watermark 2017
//                 All Rights Reserved                                     
//
//  HISTORY:
//
//    MM/DD/YY  WHO        NOTES
// ---------------------------------------------------------------------------------
//    04/14/17  RAM     Created this file
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
	
	$category_name 	= 	filter_var(trim($_POST["category_name"]), FILTER_SANITIZE_STRING);
	$category_base_price = 	filter_var(trim($_POST["category_base_price"]), FILTER_SANITIZE_STRING);
	$category_description = 	filter_var(trim($_POST["category_description"]), FILTER_SANITIZE_STRING);
	$category_image = $_POST["imageloader"];
	$category_vdp = filter_var(trim($_POST["VDP"]), FILTER_SANITIZE_STRING);
	$category_active = filter_var(trim($_POST["active"]), FILTER_SANITIZE_STRING);

	if($category_vdp == "on"){
		$category_vdp = 1;
	} else {
		$category_vdp = 0;
	}
	
	if($category_active == "on"){
		$category_active = 1;
	} else {
		$category_active = 0;
	}


	$query = "INSERT INTO product_categories (category_name, category_description, category_image, category_base_price, category_vdp, category_active) VALUES ('$category_name', '$category_description', '$category_image[0]', $category_base_price, $category_vdp, $category_active);"; 
	$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
	$numrows = mysqli_affected_rows($GLOBALS["___mysqli_ston"]);
	$last_id = $GLOBALS["___mysqli_ston"]->insert_id;
	// --------------------------------------------------------------------------  
	// If the number of rows is 1 that means that this domain was found in our 
	// database. Redirect them back to verify with an error
	// --------------------------------------------------------------------------  	

	if($numrows == 1)
	{
		echo($last_id);
	    die();
	} else {
		echo("not found");
		die();
	}
}


?>