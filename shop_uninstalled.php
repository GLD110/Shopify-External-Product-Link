<?php
include('functions.php');

$jsondata = file_get_contents("php://input");

if ($jsondata) {
    $dataobject = json_decode($jsondata);
    $data = json_decode(json_encode($dataobject), true);

    $shopDetails = getShopByName($data['myshopify_domain']);
    if($shopDetails) {
	    $sql = "update shop set payment_status='pending' where shop_pid='".addslashes($shopDetails['shop_pid'])."'";
		$query = mysqli_query($con, $sql);
		if (mysqli_connect_errno())
    {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
	}
}
?>