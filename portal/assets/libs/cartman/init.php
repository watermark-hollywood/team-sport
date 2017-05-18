<?php
// --------------------------------------------------------------------------
// Specify our include files
// --------------------------------------------------------------------------

/*
echo "<pre>";
print_r($_SERVER);
echo "</pre>";
die();
*/
$BASE_PATH = '/home2/rojectos/public_html/portal/';
require $BASE_PATH.'includes/swdb_connect.php';
require $BASE_PATH.'includes/utilityfunctions.php';
require $BASE_PATH.'includes/managesessions.php';
require $BASE_PATH.'assets/libs/cartman/vendor/idiorm.php';
require $BASE_PATH.'assets/libs/cartman/vendor/paris.php';
require $BASE_PATH.'assets/libs/cartman/vendor/Stripe.php';

// --------------------------------------------------------------------------
// Specify our model files 
// --------------------------------------------------------------------------
require $BASE_PATH.'models/Config.php';
require $BASE_PATH.'models/Item.php';
require $BASE_PATH.'models/User.php';
require $BASE_PATH.'models/Portal.php';
require $BASE_PATH.'models/Invoice.php';
require $BASE_PATH.'models/Payment.php';
require $BASE_PATH.'models/Subscription.php';

// --------------------------------------------------------------------------
// Set our db credentials for our ORM 
// --------------------------------------------------------------------------
ORM::configure('mysql:host=' . 'localhost' . ';dbname=' . 'rojectos_oslo');
ORM::configure('username', 'rojectos_admin');
ORM::configure('password', 'W@termark!');

if (preg_match('/install/', $_SERVER['REQUEST_URI'])) 
{

	// --------------------------------------------------------------------------
	// prevent install action if we already have installed  
	// --------------------------------------------------------------------------
	try 
	{
		$config_collection = Model::factory('Config')->findMany();
		go('index.php');
	} 

	catch (Exception $e) 
	{
		$install_error = $e->getMessage();
		// allow install
	}

} 

else 
{
	// --------------------------------------------------------------------------
	// Check to see if we need to install  
	// --------------------------------------------------------------------------
	try 
	{
		$config_collection = Model::factory('Config')->findMany();
	} 

	catch (Exception $e) 
	{
		go('install.php');
	}

	// --------------------------------------------------------------------------
	// Extend config variable with db config values 
	// --------------------------------------------------------------------------
	$config_arr = array();
	//echo("<pre>");
	foreach ( $config_collection as $config_obj ) 
	{
		$config_arr[$config_obj->key] = $config_obj->value;
		//echo($config_obj->key." = ".$config_obj->value."<br/>");
	}
	//echo("</pre>");
	
	$config = $config_arr;

	// --------------------------------------------------------------------------
	// Set stripe credentials 
	// --------------------------------------------------------------------------
	if ( !empty($config['stripe_secret_key']) ) 
	{
		Stripe::setApiKey(trim($config['stripe_secret_key']));
	}
	// --------------------------------------------------------------------------
	// redirect to https now if we need to 
	// --------------------------------------------------------------------------
	if ( $config['https_redirect'] && !is_ssl() && get('action') != 'paypal_ipn' ) 
	{
		$redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	    header('Location: ' . $redirect);
	    die();
	}

}

// --------------------------------------------------------------------------
// Setup our CSRF token to prevent cross site request forgeries
// --------------------------------------------------------------------------
$csrf = '';

if ( session_id() ) 
{
	$csrf = md5(session_id() . 't3rm1nal');
}