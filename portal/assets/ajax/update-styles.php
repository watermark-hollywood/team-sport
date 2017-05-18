<?php 

// ---------------------------------------------------------------------------------
//  FILENAME:      update-styles.php
//
//  DESCRIPTION:   Updating individual portal styles  
//
//  NOTES:         Updates style definitions in the database and writes a .css file
//				   for individual portals 
//                 
//
//  COPYRIGHTS:    Copyright (c) Watermark 2017
//                 All Rights Reserved                                     
//
//  HISTORY:
//
//    MM/DD/YY  WHO        NOTES
// ---------------------------------------------------------------------------------
//    03/03/16  RAM     Created this file
// ---------------------------------------------------------------------------------
 
// --------------------------------------------------------------------------    	  	
// Connect to the database.
// --------------------------------------------------------------------------  

require_once ('../../includes/managesessions.php'); 
require_once ('../../includes/swdb_connect.php'); 
require_once ('../../includes/utilityfunctions.php'); 


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	//check if its an ajax request, exit if not
	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {

	    $output = 'notajax';
	    die($output);
	}
	// --------------------------------------------------------------------------    	  	
	// Store the user and pass in local variables.
	// --------------------------------------------------------------------------  
	$body_background_color = filter_var(trim($_POST['body-background-color']), FILTER_SANITIZE_STRING);
	$body_text_color = filter_var(trim($_POST["body-text-color"]), FILTER_SANITIZE_STRING);
	$topbar_background_color = filter_var(trim($_POST["topbar-background-color"]), FILTER_SANITIZE_STRING);
	$topbar_link_color = filter_var(trim($_POST["topbar-link-color"]), FILTER_SANITIZE_STRING);
	$image_name = $_POST["imageloader"];

	// --------------------------------------------------------------------------  
	// Update styles in database
	// --------------------------------------------------------------------------  

	//body background-color
	$domain_id = $_SESSION['domain_id'];
	$query = UpdatePortalStyles($domain_id, 'body', 'background-color', $body_background_color);
	$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

	//body color
	$query = UpdatePortalStyles($domain_id, 'body', 'color', $body_text_color);
	$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

	//topbar background-color
	$query = UpdatePortalStyles($domain_id, '.bg-primary', 'background-color', $topbar_background_color);
	$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

	//topbar link color
	$query = UpdatePortalStyles($domain_id, '.navbar-inverse .navbar-nav .nav-link', 'color', $topbar_link_color);
	$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
	
	//topbar link color
	$query = UpdatePortalStyles($domain_id, '.navbar-inverse .nav .nav-pills .nav-link', 'color', $topbar_link_color);
	$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
	
	//logo image
	if(strlen($image_name[0] > 1)) {
		$query = UpdatePortalStyles($domain_id, 'logo-image', 'name', $image_name[0]);
		$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
	}
	

	$raw_css = file_get_contents('../templates/css_template.css');
	$css_file = sprintf($raw_css, $body_background_color, $body_text_color, $topbar_background_color, $topbar_link_color, $topbar_link_color);

	$domain_name = $_SESSION['domain'];
	$css_written = file_put_contents('../../domains/'.$domain_name.'/css/portal.css', $css_file, FILE_USE_INCLUDE_PATH);

	
	// --------------------------------------------------------------------------  
	// Success
	// -------------------------------------------------------------------------- 	
	if($css_written){	
		echo("success");
		die();
	} else {
		echo("error");
		die();
	}
}

?>
