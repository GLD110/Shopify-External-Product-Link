<?php
include('functions.php');


$shopDetails = getShopByName($_SESSION['shop']);
$shopifyClient = new ShopifyClient($shopDetails['shop_name'], $shopDetails['access_token'], SHOPIFY_API_KEY, SHOPIFY_SECRET);

$response = $shopifyClient->call('GET', '/admin/recurring_application_charges/'.$shopDetails['payment_id'].'.json', array());

$sql = "update shop set payment_status='".addslashes($response['status'])."' where shop_pid='".addslashes($_SESSION['shop_id'])."'";
$query = mysqli_query($con, $sql);

if($response['status'] != "accepted" && $response['status'] != "active") {
	$response = createPayment();
	header("Location:".$response['confirmation_url']);
	exit();
} else if($response['status'] != "active") {
	activatePayment($response);
	header("Location:".SITE_URL."/settings.php");
	exit();
} else {
	header("Location:".SITE_URL."/settings.php");
	exit();
}
?>