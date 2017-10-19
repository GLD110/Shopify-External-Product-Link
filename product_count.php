<?php
include('functions.php');
if($_POST['product_id']){
	$data = date("Y-m-d") ;
	$SimilarBundleID = productCount($_POST['product_id'],$data,$_POST['shop_name']);
	echo json_encode(array("result" => "true"));
} else {
	echo json_encode(array("result" => "false"));
	exit();
}
?>