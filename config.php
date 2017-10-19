<?php
ini_set('always_populate_raw_post_data', '-1');
error_reporting(E_WARNING);
session_start();

$con = mysqli_connect ("localhost", "thirsub3_epl", "(kGl~aF92H0#", 'thirsub3_dookie');

if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  
  }

define("SITE_URL", "https://www.thirstysoftware.com/externalproductlinks");
define("SHOPIFY_API_KEY", "f4f251d863225596a53183a211c345dd");
define("SHOPIFY_SECRET", "3efda1bc0ea2bae5f0f47a8bf8fee0bd");
define("SHOPIFY_SCOPE", "read_products,write_products,read_script_tags,write_script_tags,read_themes,write_themes");
define("REDIRECT_URL", SITE_URL."/auth.php");
define("SHOPIFY_TOKEN", "e0f1f5e52beb8f90b25902fd7dceb9e3-1490578021");
define("TRIAL_DAYS", 2);
define("APP_COST",4.99);

include_once('classes/shopify.php');
?>