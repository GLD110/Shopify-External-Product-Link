<?php
include('functions.php');
$snippetname = $_REQUEST['snippetname'];
$themes = getThemes();
if($themes && count($themes) > 0) {
	foreach($themes as $theme) {
		if($snippetname == "bb_bundle_products"){
			if($theme['role'] == "main") {
				installSnippet($theme['id']);
			}

			if($theme['role'] == "mobile") {
				installSnippet($theme['id']);
			}
		}else if($snippetname == "bb_normal_price"){
			if($theme['role'] == "main") {
				installPriceSnippet($theme['id']);
			}

			if($theme['role'] == "mobile") {
				installPriceSnippet($theme['id']);
			}
		}else if($snippetname == "bb_upsell_cart"){
			if($theme['role'] == "main") {
				installUpsellSnippet($theme['id']);
			}

			if($theme['role'] == "mobile") {
				installUpsellSnippet($theme['id']);
			}
		}
		
	}
}
?>