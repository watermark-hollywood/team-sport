<?php

// ---------------------------------------------------------------------------------
//  FILENAME:      createsubdomain.php
//
//  DESCRIPTION:   Hack to create subdomains at Bluehost.com
//
//  NOTES:         This source script contains the code to create a subdomain using
//                 curl to send the requests to the bluehost server.  
//
//  COPYRIGHTS:    Copyright (c) Watermark Digital 2016
//                 All Rights Reserved                             
//
//  HISTORY:
//
//    MM/DD/YY  WHO	    NOTES
// ---------------------------------------------------------------------------------
//    03/03/17  UJS     Created this file
// ---------------------------------------------------------------------------------


function CreateSubdomain($subdomain, $docroot)
{
	// --------------------------------------------------------------------------    	  	
	// Set the user name and password for our login to bluehost.
	// --------------------------------------------------------------------------    	  	
	$username = "rojectos";
	$password = "TheWM2207!";

	// --------------------------------------------------------------------------    	  	
	// Set the URL for the login first since we need to authenticate.
	// --------------------------------------------------------------------------    	  	
	$login = "https://my.bluehost.com/cgi/account/cpanel";
	$time = time();

	// --------------------------------------------------------------------------    	  	
	// Set the POST request variables
	// --------------------------------------------------------------------------    	  	
	$postBuffer = "ldomain=%s&lpass=%s&l_redirect=/cgi-bin/cplogin&l_server_time=%s&l_expires_min=0";
	$postBuffer = sprintf($postBuffer, $username, $password, $time);

	// --------------------------------------------------------------------------    	  	
	// Initialize the cURL library
	// --------------------------------------------------------------------------    	  	
	$c = curl_init();

	// --------------------------------------------------------------------------    	  	
	// Initialize the parameters for the request and send the request
	// --------------------------------------------------------------------------    	  	
	curl_setopt($c, CURLOPT_URL, $login);
	curl_setopt($c, CURLOPT_COOKIEJAR, "cookies.txt");
	curl_setopt($c, CURLOPT_COOKIEFILE, "cookies.txt");
	curl_setopt($c, CURLOPT_POST, 1);
	curl_setopt($c, CURLOPT_POSTFIELDS, $postBuffer);
	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
	$rc = curl_exec($c);
	//print_r($rc);

	// --------------------------------------------------------------------------    	  	
	// Now.. let's send the request to add a subdomain
	// --------------------------------------------------------------------------    	  	
	$newdomain = "https://my.bluehost.com/cgi/dm/subdomain/add";

	//$subdomain = "test2";
	$domain    = "project-oslo.com";
	//$docroot   = "oslo2";

	// --------------------------------------------------------------------------    	  	
	// Set the POST request variables
	// --------------------------------------------------------------------------    	  	
	$postBuffer = "sub=%s&rdomain=%s&docroot=%s";
	$postBuffer = sprintf($postBuffer, $subdomain, $domain, $docroot);

	// --------------------------------------------------------------------------    	  	
	// Initialize the parameters for the request and send the request
	// --------------------------------------------------------------------------    	  	
	curl_setopt($c, CURLOPT_URL, $newdomain);
	curl_setopt($c, CURLOPT_COOKIEJAR, "cookies.txt");
	curl_setopt($c, CURLOPT_COOKIEFILE, "cookies.txt");
	curl_setopt($c, CURLOPT_POST, 1);
	curl_setopt($c, CURLOPT_POSTFIELDS, $postBuffer);
	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
	$rc = curl_exec($c);
	//print_r($rc);

	echo "done";
}


$subdomain = "test6";
$docroot   = "portal";


CreateSubdomain($subdomain, $docroot)


?>