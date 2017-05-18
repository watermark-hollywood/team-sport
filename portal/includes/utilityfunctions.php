<?php

// ---------------------------------------------------------------------------------
//  FILENAME:      utilityfunctions.php
//
//  DESCRIPTION:   Functions used throughout the application
//
//  NOTES:         This source script contains various "utility" type functions 
//                 used by the application.
//                 
//  COPYRIGHTS:    Copyright (c) Watermark Digital 2016
//                 All Rights Reserved                            
//
//  HISTORY:
//
//    MM/DD/YY  WHO        NOTES
// ---------------------------------------------------------------------------------
//    01/30/16  UJS     Created this file
// ---------------------------------------------------------------------------------

// --------------------------------------------------------------------------           
// Global Defines
// --------------------------------------------------------------------------           
DEFINE ('STRMSG_UNASSIGN_USER', 'UNASSIGN USER FROM RR');

/**
    * Function: ArrayDisplay
    * 
    * NOTE: This function is used to dump the contents of an array to the display. The 
    * function works in conjunction with the DEBUG flag which should be defined at the 
    * top of the file. If DEBUG is defined.. it will print the contents of the array.
    *
    * @param array $aData - the array to display
    * @param string $strTitleText - any text you want to display (like array name)
    * @return void
*/
function ArrayDisplay($aData, $strTitleText)
{
    if (DEBUG)
    {
        echo "-----------------------------";
        echo $strTitleText;
        echo "-----------------------------";
        echo "<pre>";
        print_r($aData);
        echo "</pre>";
    }
}


/**
    * Function: GetDomainFromDomainID
    * 
    * NOTE: This function is used to retrieve the domain from the domain id 
    *
    * @param string $domainid - the domain identifier 
    * @return int $userid - the userid that matches the avatar 
*/
function GetDomainFromDomainID($domainid)
{    
    $query = "SELECT domain FROM domains WHERE id='$domainid'";    
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
    $row = mysqli_fetch_array($result,  MYSQLI_ASSOC);
    $domain = $row['domain'];
    return $domain;
}



function GetUserInfo($domain)
{
    $rc = 0;
    $aUsers = array();


    $query = "SELECT id, email, fname, lname, type FROM users WHERE domain='$domain' "; 
    
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $aUser = array(
                    'id'                    => $row['id'],
                    'email'                 => $row['email'],
                    'lname'             => $row['lname'],
                    'fname'              => $row['fname'],
                    'type'              => $row['type']                   
                    );      
        array_push($aUsers, $aUser);       
    }

    return $aUsers;
} // End of Function

function GetProducts($domain)
{
    $aProducts = array();
    $query = "SELECT t1.id AS id, t1.category_id, t2.category_name, name, product_code, description, price, important_info FROM products t1 JOIN product_categories t2 ON t2.id = t1.category_id JOIN domains t3 ON t1.domain_id = t3.id  WHERE t3.domain = '$domain'"; 
    
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $aProduct = array(
                    'id'                        => $row['id'],
                    'category_id'               => $row['category_id'],
                    'category_name'             => $row['category_name'],
                    'product_name'              => $row['name'],
                    'product_code'              => $row['product_code'],
                    'product_description'       => $row['description'],
                    'product_price'             => $row['price'],
                    'product_important_info'    => $row['important_info']

                    );            
    
        array_push($aProducts, $aProduct);
    }

    return $aProducts;

} // End of Function

function GetCategories()
{
    $rc = 0;
    $aCategories = array();


    $query = "SELECT id, category_name, category_description, category_image, category_vdp, category_active, category_minimum_order, category_availability FROM product_categories"; 
    
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $aCategory = array(
                    'id'                            => $row['id'],
                    'category_name'                 => $row['category_name'],
                    'category_description'          => $row['category_description'],
                    'category_image'                => $row['category_image'],
                    'category_vdp'                  => $row['category_vdp'],
                    'category_active'               => $row['category_active'],
                    'category_minimum_order'               => $row['category_minimum_order'],
                    'category_availability'               => $row['category_availability']                   
                    );      
        array_push($aCategories, $aCategory);       
    }
    return $aCategories;

} // End of Function


function GetAllProductInfo($product_id)
{
    $query = "SELECT t1.id AS id, category_id, category_name, name, product_code, description, important_info, featured, t2.category_availability, t2.category_base_price, t2.category_minimum_order, t2.category_vdp FROM products t1 JOIN product_categories t2 ON t1.category_id = t2.id WHERE t1.id = $product_id;"; 
    
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $aProduct = array(
                    'id'                => $row['id'],
                    'category_id'       => $row['category_id'],
                    'category_name'     => $row['category_name'],
                    'name'              => $row['name'],
                    'product_code'      => $row['product_code'],
                    'description'       => $row['description'],
                    'availability'      => $row['category_availability'],
                    'price'             => $row['category_base_price'],
                    'minimum_order'     => $row['category_minimum_order'],
                    'important_info'    => $row['important_info'],
                    'featured'          => $row['featured'],
                    'vdp'               => $row['category_vdp']
                    );            
    }

    return $aProduct;
} // End of Function

function GetAllCategoryInfoFromProductID($product_id)
{
    $query = "SELECT t1.id, category_name, category_description, category_image, category_base_price, category_minimum_order, category_availability, category_vdp FROM product_categories t1 JOIN products t2 ON t1.id = t2.category_id WHERE t2.id = $product_id;"; 
    
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $aCategory = array(
                    'id'                    => $row['id'],
                    'category_name'         => $row['category_name'],
                    'category_base_price'   => $row['category_base_price'],
                    'category_description'  => $row['category_description'],
                    'category_image'        => $row['category_image'],
                    'category_minimum_order'=> $row['category_minimum_order'],
                    'category_availability' => $row['category_availability'] ,
                    'category_vdp'          => $row['category_vdp']                   
                    );            
    }

    return $aCategory;
} // End of Function

function GetAllCategoryInfo($category_id)
{
    $query = "SELECT id, category_name, category_description, category_image, category_base_price, category_minimum_order, category_availability, category_vdp FROM product_categories WHERE id = $category_id;"; 
    
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $aCategory = array(
                    'id'                    => $row['id'],
                    'category_name'         => $row['category_name'],
                    'category_base_price'   => $row['category_base_price'],
                    'category_description'  => $row['category_description'],
                    'category_image'        => $row['category_image'],
                    'category_minimum_order'=> $row['category_minimum_order'],
                    'category_availability' => $row['category_availability'] ,
                    'category_vdp'          => $row['category_vdp']                   
                    );            
    }

    return $aCategory;
} // End of Function

function GetPortalCategories($domain)
{
    $aCategories = array();
    $query = "SELECT t1.id AS id, category_name FROM product_categories t1 JOIN products t2 ON t2.category_id = t1.id JOIN domains t3 ON t2.domain_id = t3.id  WHERE t3.domain = '$domain' GROUP BY t1.id;"; 
    
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $aCategory = array(
                    'id'                    => $row['id'],
                    'category_name'         => $row['category_name']                  
                    );            
    
        array_push($aCategories, $aCategory);
    }

    return $aCategories;
} // End of Function

function GetAllCategories()
{
    $aCategories = array();
    $query = "SELECT id, category_name FROM product_categories ORDER BY category_name ASC;"; 
    
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $aCategory = array(
                    'id'                    => $row['id'],
                    'category_name'         => $row['category_name']                  
                    );            
    
        array_push($aCategories, $aCategory);
    }

    return $aCategories;
} // End of Function

function GetCategoryOptions($category_id)
{

    $aOptions = array();
    $query = "SELECT id, category_id, option_name, option_values, option_prices, option_file, option_vdp FROM category_options WHERE category_id = $category_id;"; 
    
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $aOption = array(
                    'id'                => $row['id'],
                    'category_id'           => $row['category_id'],
                    'option_name'        => $row['option_name'],
                    'option_values'      => $row['option_values'],
                    'option_prices'      => $row['option_prices'],
                    'option_file'        => $row['option_file'],
                    'option_vdp'        => $row['option_vdp']               
                    );
        array_push($aOptions, $aOption);            
    }
    return $aOptions;
} // End of Function

function GetOptionVDPFields($product_id, $option_id)
{

    $aFields = array();
    $query = "SELECT vdp_field_name FROM product_option_vdp WHERE option_id = $option_id AND product_id = $product_id;"; 
    
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $field =  $row['vdp_field_name'];

        array_push($aFields, $field);            
    }

    return $aFields;
} // End of Function

function GetProductOptions($product_id)
{

    $aOptions = array();
    $aOptionsGroup = array();
    $key = "";
    $query = "SELECT id, option_key, option_value, option_price, option_selects, option_file, option_vdp, option_id FROM product_options WHERE product_id = $product_id ORDER BY option_key ASC, option_value ASC;"; 
    
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        if($row['option_key'] != $key){
            if(count($aOptionsGroup) > 0){
                array_push($aOptions, $aOptionsGroup);
            }
            $key = $row['option_key'];
            $aOptionsGroup = array();
        }
        $aOption = array(
                    'id'                => $row['id'],
                    'option_key'        => $row['option_key'],
                    'option_value'      => $row['option_value'],
                    'option_price'      => $row['option_price'],
                    'option_selects'    => $row['option_selects'],
                    'option_file'       => $row['option_file'],
                    'option_vdp'        => $row['option_vdp'] ,
                    'option_id'         => $row['option_id']               
                    );
        array_push($aOptionsGroup, $aOption);            
    }
    if(count($aOptionsGroup) > 0) {
        array_push($aOptions, $aOptionsGroup);
    }

    return $aOptions;
} // End of Function


function GetProductImages($product_id)
{
    $aImages = array();
    $query = "SELECT t1.id AS id, name, file_name FROM products t1 JOIN product_images t2 ON t1.id = t2.product_id WHERE t1.id = $product_id;"; 
    
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $aImage = array(
                    'id'                => $row['id'],
                    'name'              => $row['name'],
                    'file_name'         => $row['file_name']                 
                    );  
        array_push($aImages, $aImage);            
    }

    return $aImages;
} // End of Function

function GetProductVDP($product_id)
{
    $aFields = array();
    $query = "SELECT vdp_field_name FROM product_vdp WHERE product_id = $product_id;"; 
    
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        array_push($aFields, $row['vdp_field_name']);            
    }

    return $aFields;
} // End of Function

function GetProductSpecifications($product_id)
{
    $aSpecs = array();
    $query = "SELECT spec_key, spec_value FROM product_specifications WHERE product_id = $product_id;"; 
    
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $aSpec = array(
                    'spec_key'                => $row['spec_key'],
                    'spec_value'              => $row['spec_value']                 
                    );  
        array_push($aSpecs, $aSpec);            
    }

    return $aSpecs;
} // End of Function

function GetNavProducts($category_id, $domain)
{
    $aProducts = array();

    $query = "SELECT t1.id AS id, t1.name AS name FROM products t1 JOIN domains t2 on t1.domain_id = t2.id WHERE t2.domain = '$domain' AND t1.category_id = $category_id";
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $aProduct = array(
                    'id'           => $row['id'],
                    'name'         => $row['name']                  
                    );      
        array_push($aProducts, $aProduct);       
    }

    return $aProducts;
} // End of Function

function GetNavCategories($domain)
{
    $aCategories = array();

    $query = "SELECT t1.id AS id, category_name FROM product_categories t1 JOIN products t2 on t1.id = t2.category_id JOIN domains t3 on t2.domain_id = t3.id WHERE t3.domain = '$domain' GROUP BY t1.id";
    
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $aCategory = array(
                    'id'                    => $row['id'],
                    'category_name'         => $row['category_name']                  
                    );      
        array_push($aCategories, $aCategory);       
    }

    return $aCategories;
} // End of Function

function GetFeatured($domain)
{
    $aAllFeatured = array();

    $query = "SELECT t1.id AS id, category_id, category_name, name, product_code, t2.category_availability, description, t2.category_base_price, t2.category_minimum_order, important_info, t3.file_name AS file_name FROM products t1 JOIN product_categories t2 on t1.category_id = t2.id JOIN product_images t3 on t3.product_id = (SELECT t4.product_id FROM product_images t4 WHERE t1.id = t4.product_id LIMIT 1) JOIN domains t5 on t1.domain_id = t5.id WHERE t5.domain = '$domain' AND featured = 1 GROUP BY t3.product_id";
    
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $aFeatured = array(
                    'id'                => $row['id'],
                    'category_id'       => $row['category_id'],
                    'category_name'     => $row['category_name'],
                    'name'              => $row['name'],
                    'product_code'      => $row['product_code'],
                    'availability'      => $row['category_availability'],
                    'description'       => $row['description'],
                    'price'             => $row['category_base_price'],
                    'minimum_order'     => $row['category_minimum_order'],
                    'important_info'    => $row['important_info'], 
                    'file_name'         => $row['file_name']                   
                    );      
        array_push($aAllFeatured, $aFeatured);       
    }

    return $aAllFeatured;
} // End of Function

function GetCategoryProducts($category_id, $domain)
{
    $aProducts = array();

    $query = "SELECT t1.id AS id, category_id, category_name, name, product_code, t2.category_availability, description, t2.category_base_price, t2.category_minimum_order, important_info, t3.file_name AS file_name FROM products t1 JOIN product_categories t2 on t1.category_id = t2.id JOIN product_images t3 on t3.product_id = (SELECT t4.product_id FROM product_images t4 WHERE t1.id = t4.product_id LIMIT 1) JOIN domains t5 on t1.domain_id = t5.id WHERE t2.id = $category_id AND t5.domain = '$domain' GROUP BY t3.product_id";
    
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $aProduct = array(
                    'id'                => $row['id'],
                    'category_id'       => $row['category_id'],
                    'category_name'     => $row['category_name'],
                    'name'              => $row['name'],
                    'product_code'      => $row['product_code'],
                    'availability'      => $row['category_availability'],
                    'description'       => $row['description'],
                    'price'             => $row['category_base_price'],
                    'minimum_order'     => $row['category_minimum_order'],
                    'important_info'    => $row['important_info'], 
                    'file_name'         => $row['file_name']                     
                    );      
        array_push($aProducts, $aProduct);       
    }

    return $aProducts;
} // End of Function

function GetCarouselItems($domain)
{
    $aCarouselItems = array();

    $query = "SELECT file_name, link, slide_title, slide_desc FROM carousel_items t1 JOIN domains t2 on t1.domain_id = t2.id WHERE t2.domain = '$domain'";
    
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $aCarouselItem = array(
                    'file_name'      => $row['file_name'],
                    'link'           => $row['link'],
                    'slide_title'    => $row['slide_title'],
                    'slide_desc'     => $row['slide_desc']                  
                    );      
        array_push($aCarouselItems, $aCarouselItem);       
    }

    return $aCarouselItems;
} // End of Function

function GetAllUserInfo()
{
    $aUsers = array();


    $query = "SELECT id, domain, email, fname, lname, type FROM users ORDER BY domain DESC, type DESC"; 
    
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    // --------------------------------------------------------------------------                 
    // Enumerate through the list of Projects and store each within option tags
    // --------------------------------------------------------------------------                 
    while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC)) 
    {
        $aUser = array(
                    'id'                    => $row['id'],
                    'domain'                => $row['domain'],
                    'email'                 => $row['email'],
                    'fname'                 => $row['fname'],
                    'lname'                 => $row['lname'],
                    'type'                  => $row['type']                   
                    );      
        array_push($aUsers, $aUser);       
    }

    return $aUsers;
} // End of Function

// --------------------------------------------------------------------------           
// Send debug information to browser console
// --------------------------------------------------------------------------
function debug_to_console($data) 
{
    if (is_array($data))
    {
        $output = "<script>console.log('Debug Objects: " . implode(',', $data) . "');</script>";
    }
    
    else
    {
        $output = "<script>console.log('Debug Objects: " . $data . "');</script>";
    }

    echo $output;
}

// --------------------------------------------------------------------------           
// Implementation of mysql_result for mysqli
// --------------------------------------------------------------------------
function Getmysqli_result($res, $row, $field=0) 
{ 
    $res->data_seek($row); 
    $datarow = $res->fetch_array(); 
    return $datarow[$field]; 
} 


// --------------------------------------------------------------------------           
// Checks if needle is within input string.. return -1 on error, otherwise
// returns the location of the needle/substring.
// --------------------------------------------------------------------------
function InString($haystack, $needle) 
{ 
    $pos=strpos($haystack, $needle); 
    if ($pos !== false) 
    { 
        return $pos; 
    } 
    
    else 
    { 
        return -1; 
    } 
} 


// --------------------------------------------------------------------------           
// Performs a string length (but strips whitespace)
// --------------------------------------------------------------------------           
function GetLengthAndTrim($strInput)
{
    $strInput   = trim($strInput);
    $iLength    = strlen($strInput); 
    return $iLength;
}

// --------------------------------------------------------------------------           
// The user field in the users table contains the email address for a given 
// user.  Since this is a unique value, check to see if the user previously
// exists.  Returns userid if it does and 0 if the user does not exist.
// -------------------------------------------------------------------------- 
function DoesUserExist($email)
{
    $rc = 0;

    $query = "SELECT id FROM users WHERE email='$email'"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 

    if (mysqli_num_rows($result) == 1) 
    {   
        $row = mysqli_fetch_array($result,  MYSQLI_ASSOC); 
        $rc = $row['id'];                    
    }    

    return $rc;
} // End of Function


// --------------------------------------------------------------------------  
// Escape data using htmlentities
// --------------------------------------------------------------------------  
function escape_data_html ($data) 
{
    // --------------------------------------------------------------------------           
    // Check if Magic Quotes are enabled.
    // --------------------------------------------------------------------------           
    if (ini_get('magic_quotes_gpc')) 
    {
        $data = stripslashes($data);
    }

    // --------------------------------------------------------------------------           
    // Now deal with idiots who attempt to perform a cross-site scripting 
    // attack by filtering the string.
    // --------------------------------------------------------------------------           
    $data = htmlentities ($data);

    // --------------------------------------------------------------------------           
    // Return the filtered data string back to the caller.
    // --------------------------------------------------------------------------           
    return $data;

} // End of function.


// --------------------------------------------------------------------------  
// Performs a location redirect 
// --------------------------------------------------------------------------  
function RedirectToPage($strPage)
{
    $url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);

    // --------------------------------------------------------------------------                  
    // Check for a trailing slash.
    // --------------------------------------------------------------------------      
    if ((substr($url, -1) == '/') OR (substr($url, -1) == '\\') ) 
    {
        $url = substr ($url, 0, -1); // Chop off the slash.
    }
    
    // --------------------------------------------------------------------------              
    // Add the page.
    // --------------------------------------------------------------------------  
    $url .= '/';
    $url .= $strPage;

    // --------------------------------------------------------------------------  
    // Now redirect.                
    // --------------------------------------------------------------------------  
    header("Location: $url");
    exit();    
}

// --------------------------------------------------------------------------              
// String Concatenate... concats a string to the length specified and adds
// ellipses at the cutoff point.
// --------------------------------------------------------------------------              
function StringConcat ($strPassed, $iMaxLength)  
{ 
    if (strlen($strPassed) > $iMaxLength)  
    { 
        $strPassed = substr($strPassed, 0, $iMaxLength); 
        $strPassed .= "..."; 
    } 
    return $strPassed; 
} 

// --------------------------------------------------------------------------              
// US Phone number validation.. checks NNN-NNN-NNNN where N is a number
// -------------------------------------------------------------------------- 
function IsPhoneNumberValid ($strPhoneNum) 
{
    $rc = false;

    if (ereg("^[0-9]{3}-[0-9]{3}-[0-9]{4}$", $strPhoneNum)) 
    {
        // --------------------------------------------------------------------------              
        // Phone number is valid.
        // --------------------------------------------------------------------------              
        $rc = true;
    }

    return $rc;
}

// --------------------------------------------------------------------------              
// US 5 Digit Zip code validation
// --------------------------------------------------------------------------
function IsZipcodeValid ($strZipcode) 
{
    $length  = strlen ($strZipcode);

    $numeric = is_numeric($strZipcode);

    // --------------------------------------------------------------------------          
    // Make sure we have the following (a non zero length string that is exactly 
    // five characters long, and consists of all numeric characters. 
    // --------------------------------------------------------------------------
    $rc = ($length === false || $length < 5 || $numeric === false);        
    return !($rc);
}


// --------------------------------------------------------------------------              
// Normalizes a Name (capitalizes Words)
// --------------------------------------------------------------------------
function NormalizeName($name) 
{
    $name = strtolower($name);
    $normalized = array();

    foreach (preg_split('/([^a-z])/', $name, NULL, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) as $word) 
    {
        if (preg_match('/^(mc)(.*)$/', $word, $matches)) 
        {
            $word = $matches[1] . ucfirst($matches[2]);
        }

        $normalized[] = ucfirst($word);
    }

    return implode('', $normalized);
}


// --------------------------------------------------------------------------
// Beautifully simple function to push a key and value pair into an 
// associative array.
// --------------------------------------------------------------------------
function array_push_assoc($array, $key, $value)
{
    $array[$key] = $value;
    return $array;
}

// --------------------------------------------------------------------------
// Checks a number to see if it is even. Returns true if even, false if odd.
// --------------------------------------------------------------------------
function IsNumberEven($number)
{
    if ($number % 2 == 0) 
    {
        return true;
    }
    
    else
    {
        return false;
    }
}

//--------------------------------------------------------------------------
// Create a GUID 
//--------------------------------------------------------------------------
function GetGUID(){
    mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = chr(45);// "-"
    $uuid = substr($charid, 0, 8).$hyphen
        .substr($charid, 8, 4).$hyphen
        .substr($charid,12, 4).$hyphen
        .substr($charid,16, 4).$hyphen
        .substr($charid,20,12);
    return $uuid;
}

//--------------------------------------------------------------------------
// Extract the domain from a URL
//--------------------------------------------------------------------------
function ExtractDomain($domain)
{
    if(preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $domain, $matches))
    {
        return $matches['domain'];
    } else {
        return $domain;
    }
}


//--------------------------------------------------------------------------
// Extract the subdomain from a domain
//--------------------------------------------------------------------------
function ExtractSubdomains($domain)
{
    $subdomains = $domain;
    $domain = ExtractDomain($subdomains);

    $subdomains = rtrim(strstr($subdomains, $domain, true), '.');

    return $subdomains;
}


//--------------------------------------------------------------------------
// Check if manual approval is turned on for portals
//--------------------------------------------------------------------------
function CheckManualValidate() 
{
    $query = "SELECT * FROM platform_prefs WHERE pref = 'validate_portal';"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
    $numrows = mysqli_num_rows($result);    
    $validate = 0;
    if($numrows == 1)
    {
        $row = mysqli_fetch_array($result,  MYSQLI_ASSOC);
        $validate = $row["value"];
    }
    //if manual verification of portals is turned off, remove the bitmask for "not admin approved" (2)
    if($validate < 1) {
        return false;
    } else {
        return true;
    }
}

function UpdatePortalStyles($domain_id, $style_name, $property_name, $property_value) {
    return "UPDATE domain_styles SET property_value = '$property_value' WHERE property_name = '$property_name' AND style_name = '$style_name' AND domain_id = '$domain_id';"; 
}

function ValidateDomain($host) {
    $login_domain = ExtractSubdomains($host);
    $query = "SELECT domain FROM domains ";
    $query .="WHERE domain = '$login_domain';"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
    $numrows = mysqli_num_rows($result);
    
    if ($numrows < 1) {
        header( "Location: http://www.project-oslo.com" );
    }
}



function ValidatePlatformAdminLoggedIn($type) {
    if(!isset($_SESSION['type']) || ($_SESSION['type'] & 32) < 1) {
        header( "Location: /" ); 
    }
}

function ValidateAdminLoggedIn($type) {
    if(!isset($_SESSION['type']) || (($_SESSION['type'] & 16) < 1 && ($_SESSION['type'] & 32) < 1)) {
        header( "Location: /" ); 
    }
}

function ValidateUserLoggedIn($type) {
    if(!isset($_SESSION['type']) || (($_SESSION['type'] & 8) < 1 && ($_SESSION['type'] & 16) < 1 && ($_SESSION['type'] & 32) < 1)) {
        header( "Location: /" ); 
    }
}

function GetDomainLogo($host) {
    $login_domain = ExtractSubdomains($host);
    $query = "SELECT property_name, property_value FROM domain_styles t1 ";
    $query .= "JOIN domains t2 on t1.domain_id = t2.id ";
    $query .= "WHERE t2.domain = '$login_domain' AND t1.style_name = 'logo-image';";
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
    $numrows = mysqli_num_rows($result);
    if($numrows == 1) {
        $row = mysqli_fetch_array($result,  MYSQLI_ASSOC);
        $img = $row['property_value'];
        echo('<a href="/"><img src="domains/'.$login_domain.'/images/tn/'.$img.'" border="0" height="46" /></a>');
    } else { 
        echo('<a href="/"><img src="domains/'.$login_domain.'/images/tn/logo.png" border="0" height="46" /></a>');
    }
}


/**
    * Function: msg
    * 
    * NOTE: This function is used to display a message to the user. The 
    * message information is stored as an array in a session variable
    *
    * @param string $message - the message to be displayed
    * @param string $type - the type of message (success, danger, etc.)
    * @return void
*/
function msg($message, $type) 
{
    $_SESSION['flash'] = array(
                                'type' => $type,
                                'message' => $message
                            );
}

/**
    * Function: go
    * 
    * NOTE: This function is used to perform a PHP location redirect  
    * to the specified URL 
    *
    * @param string $url - the URL to go to
    * @return void
*/
function go($url = '') 
{
    header('Location: ' . $url);
    die();
}

/**
    * Function: url
    * 
    * NOTE: This function is used to get a full URL (path and filename) from 
    * a partial url by appending the host and full path
    *
    * @param string $url - the URL 
    * @return void
*/
function url($url = '') 
{
    $host = $_SERVER['HTTP_HOST'];
    $host = !preg_match('/^http/', $host) ? 'http://' . $host : $host;
    $path = preg_replace('/\w+\.php/', '', $_SERVER['REQUEST_URI']);
    $path = preg_replace('/\?.*$/', '', $path);
    $path = !preg_match('/\/$/', $path) ? $path . '/' : $path;

    if ( preg_match('/http:/', $host) && is_ssl() ) 
    {
        $host = preg_replace('/http:/', 'https:', $host);
    }
    
    if ( preg_match('/https:/', $host) && !is_ssl() ) 
    {
        $host = preg_replace('/https:/', 'http:', $host);
    }
    
    return $host . $path . $url;
}

/**
    * Function: email
    * 
    * NOTE: This function is used to send an email using phpmailer. 
    *
    * @param string $to - the person you are sending the email to 
    * @param string $file - the template file containing the content
    * @param array $values - the values for variable substitution
    * @param string $subject - the subject line      
    * @return void
*/
function email($to, $file, $values, $subject) 
{
    global $config;

    $BASE_PATH = '/home2/rojectos/public_html/portal/';
    // --------------------------------------------------------------------------
    // Add config data to values array
    // --------------------------------------------------------------------------
    $values = array_merge($values, $config);

    // --------------------------------------------------------------------------
    // Get email header
    // --------------------------------------------------------------------------
    $content = file_get_contents($BASE_PATH.'assets/email_creatives/layout/header.php');

    // --------------------------------------------------------------------------
    // Get email content
    // --------------------------------------------------------------------------
    $content .= file_get_contents($BASE_PATH.'assets/email_creatives/' . $file . '.php');

    // --------------------------------------------------------------------------
    // Get email footer
    // --------------------------------------------------------------------------
    $content .= file_get_contents($BASE_PATH.'assets/email_creatives/layout/footer.php');

    // --------------------------------------------------------------------------
    // Perform variable substitution for placeholders values
    // --------------------------------------------------------------------------
    foreach ( $values as $key => $value ) 
    {
        $content = str_replace('{' . $key . '}', $value, $content);
    }
    
    // --------------------------------------------------------------------------
    // Build our email and send
    // --------------------------------------------------------------------------

    require_once $BASE_PATH.'assets/libs/PHPMailer/PHPMailerAutoload.php';
    $mail = new PHPMailer;
    $mail->isMail();
    $mail->From = $config['email'];
    $mail->FromName = $config['name'];
    $mail->addAddress($to);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $content;
    $mail->send();
}

/**
    * Function: template
    * 
    * NOTE: This function is used to require a template file 
    *
    * @param string $path - the path and filename of the template 
    * @param bool $container - default set to true 
    * @return void
*/
function template($path, $container = true) 
{
    global $csrf;
    require $BASE_PATH.'assets/templates/' . $path . '.php';
}

/**
    * Function: is_ssl
    * 
    * NOTE: This function is used to check if a connection is using SSL 
    *
    * @return true if yes and false if no
*/
function is_ssl() 
{
    if ( isset($_SERVER['HTTPS']) ) 
    {
        if ( 'on' == strtolower($_SERVER['HTTPS']))
            return true;

        if ( '1' == $_SERVER['HTTPS'])
            return true;

    } 

    elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) 
    {
        return true;
    }
    return false;
}


/**
    * Function: post
    * 
    * NOTE: This function is used to get POST request 
    *
    * @param string $key - the array key 
    * @return array $_POST - the post array
*/
function post($key = null) 
{
    if ( is_null($key) ) 
    {
        return $_POST;
    }
    
    $post = isset($_POST[$key]) ? $_POST[$key] : null;
    
    if (is_string($post)) 
    {
        $post = trim($post);
    }
    
    return $post;
}

/**
    * Function: get
    * 
    * NOTE: This function is used to get GET request 
    *
    * @param string $key - the array key 
    * @return array $_GET - the get array
*/
function get($key = null) 
{
    if ( is_null($key) ) 
    {
        return $_GET;
    }
    
    $get = isset($_GET[$key]) ? $_GET[$key] : null;
    
    if (is_string($get)) 
    {
        $get = trim($get);
    }
    
    return $get;
}

/**
    * Function: currencyCode
    * 
    * NOTE: This function is used to get the Font Awesome currency code 
    *
    * @return string - the 3 digit currency code 
*/
function currencyCode() 
{
    global $config;

    switch ( $config['currency'] ) 
    {
        case 'USD':
        case 'CAD':
        case 'AUD':
            return 'usd';
        break;
        case 'EUR':
            return 'eur';
        break;
        case 'GBP':
            return 'gbp';
        break;
    }
}

/**
    * Function: currencySymbol
    * 
    * NOTE: This function is used to get the currency symbol 
    *
    * @return string - the 1 digit currency symbol
*/
function currencySymbol() 
{
    global $config;

    switch ( $config['currency'] ) 
    {
        case 'USD':
        case 'CAD':
        case 'AUD':
            return '$';
        break;

        case 'EUR':
            return '&euro;';
        break;

        case 'GBP':
            return '&pound;';
        break;
    }
}

/**
    * Function: currencySuffix
    * 
    * NOTE: This function is used to get the currency suffix
    *
    * @return string - the 3 digit currency suffix
*/
function currencySuffix() 
{
    global $config;

    switch ( $config['currency'] ) 
    {
        case 'AUD':
            return '(AUD)';
        break;
    
        case 'CAD':
            return '(CAD)';
        break;
    }
}

/**
    * Function: currency
    * 
    * NOTE: This function is used to format a number with the correct currency code 
    * prefixed ahead of the number
    *
    * @return string - the formatted number with currency code 
*/
function currency($amount) 
{
    return currencySymbol() . number_format($amount, 2, '.', ',');
}

/**
    * Function: coutries
    * 
    * NOTE: This function is used to return an array of country names and their 
    * two digit country codes. The key is the 2 digit country code and the value
    * is the country name
    *
    * @return array $countries - the array
*/
function countries() 
{
    $countries = array( 'US' => 'United States', 'CA' => 'Canada', 'UK' => 'United Kingdom', 'AU' => 'Australia', 'AF' => 'Afghanistan', 'AL' => 'Albania', 'DZ' => 'Algeria', 'AS' => 'American Samoa', 'AD' => 'Andorra', 'AO' => 'Angola', 'AI' => 'Anguilla', 'AQ' => 'Antarctica', 'AG' => 'Antigua and Barbuda', 'AR' => 'Argentina', 'AM' => 'Armenia', 'AW' => 'Aruba', 'AT' => 'Austria', 'AZ' => 'Azerbaijan', 'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh', 'BB' => 'Barbados', 'BY' => 'Belarus', 'BE' => 'Belgium', 'BZ' => 'Belize', 'BJ' => 'Benin', 'BM' => 'Bermuda', 'BT' => 'Bhutan', 'BO' => 'Bolivia', 'BA' => 'Bosnia and Herzegovina', 'BW' => 'Botswana', 'BR' => 'Brazil', 'BN' => 'Brunei Darussalam', 'BG' => 'Bulgaria', 'BF' => 'Burkina Faso', 'BI' => 'Burundi', 'KH' => 'Cambodia', 'CM' => 'Cameroon', 'CV' => 'Cape Verde', 'KY' => 'Cayman Islands', 'CF' => 'Central African Republic', 'TD' => 'Chad', 'CL' => 'Chile', 'CN' => 'China', 'CX' => 'Christmas Island', 'CC' => 'Cocos (Keeling) Islands', 'CO' => 'Colombia', 'KM' => 'Comoros', 'CG' => 'Congo', 'CD' => 'Congo, The Democratic Republic of the', 'CK' => 'Cook Islands', 'CR' => 'Costa Rica', 'CI' => 'Cote D`Ivoire', 'HR' => 'Croatia', 'CY' => 'Cyprus', 'CZ' => 'Czech Republic', 'DK' => 'Denmark', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Dominican Republic', 'EC' => 'Ecuador', 'EG' => 'Egypt', 'SV' => 'El Salvador', 'GQ' => 'Equatorial Guinea', 'ER' => 'Eritrea', 'EE' => 'Estonia', 'ET' => 'Ethiopia', 'FK' => 'Falkland Islands (Malvinas)', 'FO' => 'Faroe Islands', 'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'France', 'GF' => 'French Guiana', 'PF' => 'French Polynesia', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgia', 'DE' => 'Germany', 'GH' => 'Ghana', 'GI' => 'Gibraltar', 'GR' => 'Greece', 'GL' => 'Greenland', 'GD' => 'Grenada', 'GP' => 'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GN' => 'Guinea', 'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hungary', 'IS' => 'Iceland', 'IN' => 'India', 'ID' => 'Indonesia', 'IR' => 'Iran (Islamic Republic Of)', 'IQ' => 'Iraq', 'IE' => 'Ireland', 'IL' => 'Israel', 'IT' => 'Italy', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JO' => 'Jordan', 'KZ' => 'Kazakhstan', 'KE' => 'Kenya', 'KI' => 'Kiribati', 'KP' => 'Korea North', 'KR' => 'Korea South', 'KW' => 'Kuwait', 'KG' => 'Kyrgyzstan', 'LA' => 'Laos', 'LV' => 'Latvia', 'LB' => 'Lebanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LI' => 'Liechtenstein', 'LT' => 'Lithuania', 'LU' => 'Luxembourg', 'MO' => 'Macau', 'MK' => 'Macedonia', 'MG' => 'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Maldives', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshall Islands', 'MQ' => 'Martinique', 'MR' => 'Mauritania', 'MU' => 'Mauritius', 'MX' => 'Mexico', 'FM' => 'Micronesia', 'MD' => 'Moldova', 'MC' => 'Monaco', 'MN' => 'Mongolia', 'MS' => 'Montserrat', 'MA' => 'Morocco', 'MZ' => 'Mozambique', 'NA' => 'Namibia', 'NP' => 'Nepal', 'NL' => 'Netherlands', 'AN' => 'Netherlands Antilles', 'NC' => 'New Caledonia', 'NZ' => 'New Zealand', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'NO' => 'Norway', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestine Autonomous', 'PA' => 'Panama', 'PG' => 'Papua New Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Philippines', 'PL' => 'Poland', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'RE' => 'Reunion', 'RO' => 'Romania', 'RU' => 'Russian Federation', 'RW' => 'Rwanda', 'VC' => 'Saint Vincent and the Grenadines', 'MP' => 'Saipan', 'SM' => 'San Marino', 'SA' => 'Saudi Arabia', 'SN' => 'Senegal', 'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovak Republic', 'SI' => 'Slovenia', 'SO' => 'Somalia', 'ZA' => 'South Africa', 'ES' => 'Spain', 'LK' => 'Sri Lanka', 'KN' => 'St. Kitts/Nevis', 'LC' => 'St. Lucia', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SZ' => 'Swaziland', 'SE' => 'Sweden', 'CH' => 'Switzerland', 'SY' => 'Syria', 'TW' => 'Taiwan', 'TI' => 'Tajikistan', 'TZ' => 'Tanzania', 'TH' => 'Thailand', 'TG' => 'Togo', 'TK' => 'Tokelau', 'TO' => 'Tonga', 'TT' => 'Trinidad and Tobago', 'TN' => 'Tunisia', 'TR' => 'Turkey', 'TM' => 'Turkmenistan', 'TC' => 'Turks and Caicos Islands', 'TV' => 'Tuvalu', 'UG' => 'Uganda', 'UA' => 'Ukraine', 'AE' => 'United Arab Emirates', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VE' => 'Venezuela', 'VN' => 'Viet Nam', 'VG' => 'Virgin Islands (British)', 'VI' => 'Virgin Islands (U.S.)', 'WF' => 'Wallis and Futuna Islands', 'YE' => 'Yemen', 'YU' => 'Yugoslavia', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe');
    return $countries;
}

/**
    * Function: states
    * 
    * NOTE: This function is used to return an array of states.. both US states
    * as well as Canadian and Australian provinces.
    *
    * @return array $states - the array
*/
function states() 
{
    $states = array(
          'US States' => array('AL' => 'Alabama','AK' => 'Alaska','AZ' => 'Arizona','AR' => 'Arkansas','BVI' => 'British Virgin Islands','CA' => 'California','CO' => 'Colorado','CT' => 'Connecticut','DE' => 'Delaware','FL' => 'Florida','GA' => 'Georgia','GU' => 'Guam','HI' => 'Hawaii','ID' => 'Idaho','IL' => 'Illinois','IN' => 'Indiana','IA' => 'Iowa','KS' => 'Kansas','KY' => 'Kentucky','LA' => 'Louisiana','ME' => 'Maine','MP' => 'Mariana Islands','MPI' => 'Mariana Islands (Pacific)','MD' => 'Maryland','MA' => 'Massachusetts','MI' => 'Michigan','MN' => 'Minnesota','MS' => 'Mississippi','MO' => 'Missouri','MT' => 'Montana','NE' => 'Nebraska','NV' => 'Nevada','NH' => 'New Hampshire','NJ' => 'New Jersey','NM' => 'New Mexico','NY' => 'New York','NC' => 'North Carolina','ND' => 'North Dakota','OH' => 'Ohio','OK' => 'Oklahoma','OR' => 'Oregon','PA' => 'Pennsylvania','PR' => 'Puerto Rico','RI' => 'Rhode Island','SC' => 'South Carolina','SD' => 'South Dakota','TN' => 'Tennessee','TX' => 'Texas','UT' => 'Utah','VT' => 'Vermont','USVI' => 'VI  U.S. Virgin Islands','VA' => 'Virginia','WA' => 'Washington','DC' => 'Washington, D.C.','WV' => 'West Virginia','WI' => 'Wisconsin','WY' => 'Wyoming',
          ),
          'Canadian Provinces' => array('AB' => 'Alberta','BC' => 'British Columbia','MB' => 'Manitoba','NB' => 'New Brunswick','NF' => 'Newfoundland','NT' => 'Northwest Territories','NS' => 'Nova Scotia','NVT' => 'Nunavut','ON' => 'Ontario','PE' => 'Prince Edward Island','QC' => 'Quebec','SK' => 'Saskatchewan','YK' => 'Yukon',
          ),
          'Australian Provinces' => array('AU-NSW' => 'New South Wales','AU-QLD' => 'Queensland','AU-SA' => 'South Australia','AU-TAS' => 'Tasmania','AU-VIC' => 'Victoria','AU-WA' => 'Western Australia','AU-ACT' => 'Australian Capital Territory','AU-NT' => 'Northern Territory',
          ),
    );
    return $states;
}

/**
    * Function: s
    * 
    * NOTE: This function is used for debugging purposes. It sends a debug string 
    * to the display and formats the element using the /PRE tags
    *
    * @param string $input - the value to display 
    * @return void 
*/
function s($input) 
{
    $output = '<pre>';

    if (is_array($input) || is_object($input)) 
    {
        $output .= print_r($input, true);
    } 

    else 
    {
        $output .= $input;
    }
    
    $output .= '</pre>';
    echo $output;
}

/**
    * Function: sd
    * 
    * NOTE: This function is used for debugging purposes. It sends a debug string 
    * to the display and formats the element using the /PRE tags and then stops
    * the currently executing script from running 
    *
    * @param string $input - the value to display 
    * @return void 
*/
function sd($input) 
{
    die(s($input));
}

?>