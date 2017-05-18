<?php 

// ---------------------------------------------------------------------------------
//  FILENAME:      logout.php
//
//  DESCRIPTION:   Log Users Out
//
//  NOTES:         Self-Explanatory
//                 
//
//  COPYRIGHTS:    Copyright (c) Watermark 2017
//                 All Rights Reserved                                     
//
//  HISTORY:
//
//    MM/DD/YY  WHO        NOTES
// ---------------------------------------------------------------------------------
//    03/01/17  RAM     Created this file
// ---------------------------------------------------------------------------------
 
// --------------------------------------------------------------------------    	  	
// Connect to the database.
// --------------------------------------------------------------------------  
require_once ('../includes/managesessions.php'); 

$domain = $_SESSION['domain'];
session_unset();
session_destroy();

header ("Location: http://".$domain.".project-oslo.com/");


?>
