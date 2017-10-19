<?php
include('functions.php');


if( $_POST['product_id']){
			
    $SimilarBundleID = resetProductCount( $_POST['product_id'],$_SESSION['shop_id']);
	
      echo json_encode(array("result" => "true"));
} else {
    echo json_encode(array("result" => "false"));
    exit();
}
?>