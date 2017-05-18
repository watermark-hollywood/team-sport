<?php 
	set_time_limit(0);

// ---------------------------------------------------------------------------------
//  FILENAME:      update-product-images.php
//
//  DESCRIPTION:   Update Product Images 
//
//  NOTES:         This source script is used to update product images
//
//  COPYRIGHTS:    Copyright (c) Watermark 2017
//                 All Rights Reserved                                     
//
//  HISTORY:
//
//    MM/DD/YY  WHO        NOTES
// ---------------------------------------------------------------------------------
//    04/13/17  RAM     Created this file
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
	$product_id 	= 	filter_var(trim($_POST["product_id"]), FILTER_SANITIZE_STRING);
	$product_images = $_POST["imageloader"];

	if($_POST["product_id"]){
		// --------------------------------------------------------------------------  
		// Wipe out existing images.
		// --------------------------------------------------------------------------
		$query = "DELETE from product_images WHERE product_id = '$product_id';";
		$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

		for($i=0;$i<count($product_images);$i++){
			$query = "INSERT INTO product_images (product_id, file_name) VALUES ($product_id, '$product_images[$i]');"; 
			$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
		}

		echo("success");
	    die();

	} else {
		echo("missing params");
		die();
	}
}


?>