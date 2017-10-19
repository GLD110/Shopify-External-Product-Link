<?php
include_once('functions.php');

if(checkShopRegistered($_REQUEST['shop'])) {
	if(loginShop($_REQUEST)) {
		header("Location:".SITE_URL);
		exit();
	} else {
		header("Location:".SITE_URL);
		exit();
	}
} else {
	if(registerShop($_REQUEST)) {
		header("Location:".SITE_URL);
		exit();
	} else {
		header("Location:".SITE_URL);
		exit();
	}
}
?>