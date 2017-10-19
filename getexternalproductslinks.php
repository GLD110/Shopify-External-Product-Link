<?php
include('functions.php');
$data = '';

if($_POST['product_id']){
	$SimilarBundleID = getSimilarBundleIDsAPI($_POST['product_id']);
	//var_dump($SimilarBundleID);
	$count = mysqli_num_rows($SimilarBundleID);
	//echo $count;
	if ( $count > 0 ) {
		$data = mysqli_fetch_array($SimilarBundleID);
		if ($data['status'] == "off") {
			echo json_encode(array("result" => "true", "data" => array("status" => "off")));
			exit();
		}
		else {
			echo json_encode(array("result" => "true", "data" => $data ));
			exit();	
		}
	}
	else {
		echo json_encode(array("result" => "true", "data" => array("status" => "off")));
		exit();
	}
}
else {
	echo json_encode(array("result" => "false"));
	exit();
}

// Original code if you need to re use that code please remove the comments.

/*
if($_POST['product_id']){
	$SimilarBundleID = getSimilarBundleIDsAPI($_POST['product_id']);
	if(mysqli_num_rows($SimilarBundleID) > 0) {
		while($res2 = mysqli_fetch_array($SimilarBundleID)) {			
				 $data = $res2;
		}
		echo json_encode(array("result" => "true", "data" => $data ));
		exit();	
	}
	else {
		#echo json_encode(array("result" => "false")); // old way
		echo json_encode(array("result" => "true", "data" => array("status" => "off")));
		exit();
	}
} else {
	echo json_encode(array("result" => "false"));
	exit();
} */