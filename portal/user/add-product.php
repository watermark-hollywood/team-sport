<?php 
	set_time_limit(0);

// ---------------------------------------------------------------------------------
//  FILENAME:      add-product.php
//
//  DESCRIPTION:   Add Portal Products 
//
//  NOTES:         This source script is used to Add Products to OSLO Portals
//
//  COPYRIGHTS:    Copyright (c) Watermark 2017
//                 All Rights Reserved                                     
//
//  HISTORY:
//
//    MM/DD/YY  WHO        NOTES
// ---------------------------------------------------------------------------------
//    04/15/17  RAM     Created this file
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
	
	$domain 				= 	filter_var(trim($_POST["domain"]), FILTER_SANITIZE_STRING);
	$product_name 			= 	filter_var(trim($_POST["product_name"]), FILTER_SANITIZE_STRING);
	$product_description 	= 	filter_var(trim($_POST["product_description"]), FILTER_SANITIZE_STRING);
	$product_code 			= 	filter_var(trim($_POST["product_code"]), FILTER_SANITIZE_STRING);
	$category_id 			= 	filter_var(trim($_POST["category_id"]), FILTER_SANITIZE_STRING);
	$important_info			= 	filter_var(trim($_POST["important_info"]), FILTER_SANITIZE_STRING);
	$featured				=	filter_var(trim($_POST["featured"]), FILTER_SANITIZE_STRING);
	$options = $_POST["option"];
	$vdp_fields = $_POST["vdp_field"];

	$category_image = $_POST["imageloader"];


	if(!$featured){
		$featured = 0;
	}

	$query = "SELECT id from domains where domain = '$domain'";
	$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
	$row = mysqli_fetch_array($result,  MYSQLI_ASSOC);
	$domain_id = $row['id'];
		
	$query  = "INSERT INTO products (category_id, domain_id, name, product_code, description, featured, important_info)";
	$query .= "VALUES ('$category_id', $domain_id, '$product_name', '$product_code', '$product_description', $featured, '$important_info');";
	$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
	$numrows = mysqli_affected_rows($GLOBALS["___mysqli_ston"]);
	$last_id = $GLOBALS["___mysqli_ston"]->insert_id;
	// --------------------------------------------------------------------------  
	// If the number of rows is 1 that means that this domain was found in our 
	// database. Redirect them back to verify with an error
	// --------------------------------------------------------------------------  	

	for($i=0;$i<count($category_image);$i++){
		$query = "INSERT INTO product_images (product_id, file_name) VALUES ('$last_id', '$category_image[$i]');";
		$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
	}

	foreach($vdp_fields as $key => $value){
		foreach($value as $newkey => $newvalue){
			//echo($key." ".$newvalue);
			
			$option_id = filter_var(trim($key), FILTER_SANITIZE_STRING);
			$vdp_field_name = filter_var(trim($newvalue), FILTER_SANITIZE_STRING);
			$query = "INSERT INTO product_option_vdp (option_id, product_id, vdp_field_name) VALUES ('$option_id', '$last_id', '$vdp_field_name');";
			$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query);
			
		} 
	}


	foreach($options as $option) {
		foreach($option as $aOption){
			$aOption = explode(":", $aOption);

			$query = "INSERT INTO product_options (product_id, option_key, option_value, option_price, option_selects, option_file, option_vdp, option_id) VALUES ('$last_id', '$aOption[1]', '$aOption[2]', '$aOption[3]', '$aOption[0]', $aOption[4], $aOption[5], $aOption[6]);";
			$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
		}
	}

	echo($last_id);
	die();
	
}


?>