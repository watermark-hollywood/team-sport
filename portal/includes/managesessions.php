<?php

// --------------------------------------------------------------------------           
// Tell server to keep the session data for AT LEAST 1 hour
// -------------------------------------------------------------------------- 
ini_set('session.gc_maxlifetime', 3600);

// --------------------------------------------------------------------------           
// Set the cookie parameters to instruct each client that they should 
// remember their session id for EXACTLY 1 hour
// --------------------------------------------------------------------------           
session_set_cookie_params(3600);

// --------------------------------------------------------------------------           
// Now start our session
// --------------------------------------------------------------------------           
session_start(); 
if(!isset($_SESSION['cart_contents'])){
	$_SESSION['cart_contents'] = array();
}

// --------------------------------------------------------------------------           
// Get the current time
// --------------------------------------------------------------------------           
$now = time();

// --------------------------------------------------------------------------           
// Set our session variable.. either new or old, it should live at most 
// for another hour
// --------------------------------------------------------------------------           
$_SESSION['discard_after'] = $now + 3600;


// --------------------------------------------------------------------------           
// Check to see if we have our Session variable set that tells us whether 
// or not we should discard our session.
// --------------------------------------------------------------------------           
if (isset($_SESSION['discard_after']) && $now > $_SESSION['discard_after']) 
{
	// --------------------------------------------------------------------------           
	// If the variable is set.. it means that the session has worn out its
	// welcome, so we kill the existing session and start a brand new one.
	// --------------------------------------------------------------------------           
    session_unset();
    session_destroy();
    session_start();
}

?>