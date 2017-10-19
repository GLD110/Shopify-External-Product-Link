<?php
include('functions.php');

$jsondata = file_get_contents("php://input");

if ($jsondata) {
    $dataobject = json_decode($jsondata);
    $data = json_decode(json_encode($dataobject), true);

    $headerArray = getallheaders();
    $shopDetails = getShopByName($headerArray['X-Shopify-Shop-Domain']);
    if($shopDetails) {
	    
	}
}
?>