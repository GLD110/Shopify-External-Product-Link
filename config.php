<?php
ini_set('always_populate_raw_post_data', '-1');
error_reporting(E_WARNING);
session_start();

$con = mysqli_connect ("localhost", "thirsub3_epl", "thirsub3_epl", 'thirsub3_epl');

if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  
  }

define("SITE_URL", "https://kanvaskreations.com/epl");
define("SHOPIFY_API_KEY", "447fd7b272a0637f11bc556aac8ea625");
define("SHOPIFY_SECRET", "6497c2dc868839fb9e0a103729911de6");
define("SHOPIFY_SCOPE", "read_products,write_products,read_script_tags,write_script_tags,read_themes,write_themes");
define("REDIRECT_URL", SITE_URL."/auth.php");
define("SHOPIFY_TOKEN", "f5dcd183db64309f3e9c8c9377374268-1508596168");
define("TRIAL_DAYS", 2);
define("APP_COST",4.99);

include_once('classes/shopify.php');
?>