<?php
include('functions.php');

if($_POST['upsell_button_text']) {

	$check_sql = mysqli_query($con, "select * from upsell_layout_settings where shop_id='".$_SESSION['shop_id']."'");
	if(mysqli_num_rows($check_sql) > 0){
		$sql = "update upsell_layout_settings set upsell_button_text='".addslashes($_POST['upsell_button_text'])."', upsell_layout_class='".addslashes($_POST['upsell_layout_class'])."', bundle_heading='".addslashes($_POST['bundle_heading'])."', bundle_link_text='".addslashes($_POST['bundle_link_text'])."' where shop_id='".$_SESSION['shop_id']."'";
		$insert = mysqli_query($con, $sql);
		if (mysqli_connect_errno())
    {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
	}else{
		$sql = "insert into upsell_layout_settings set shop_id='".$_SESSION['shop_id']."',upsell_button_text='".addslashes($_POST['upsell_button_text'])."', upsell_layout_class='".addslashes($_POST['upsell_layout_class'])."', bundle_heading='".addslashes($_POST['bundle_heading'])."', bundle_link_text='".addslashes($_POST['bundle_link_text'])."'";
		$insert = mysqli_query($con, $sql);
		if (mysqli_connect_errno())
    {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
	}
	
	if($insert){
		$_SESSION['success_message'] = "Settings have been updated successfully.";
		header("Location:settings.php");
		exit();   
	}else{
		$_SESSION['error_message'] = "Error! Please try again.";
		header("Location:settings.php");
		exit();	
	}

} else {
	$_SESSION['error_message'] = "Error! Please try again.";
	header("Location:settings.php");
	exit();
}

?>