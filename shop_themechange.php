<?php
include('functions.php');

$jsondata = file_get_contents("php://input");

if ($jsondata) {
    $dataobject = json_decode($jsondata);
    $data = json_decode(json_encode($dataobject), true);

    $headerArray = getallheaders();
    $shopDetails = getShopByName($headerArray['X-Shopify-Shop-Domain']);
    if($shopDetails) {
	    
	
		
	
		$shop = $shopDetails['shop_name'];

	$sql = "select `access_token` from shop where shop_name='".addslashes($shop)."'";
	$query = mysqli_query($con, $sql);
	if (mysqli_connect_errno())
    {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
	if(mysqli_num_rows($query) > 0) {
		$data2 =  mysqli_fetch_assoc($query);
	} 

	$access_token = $data2['access_token'];

	$shopifyClient = new ShopifyClient($shop, $access_token, SHOPIFY_API_KEY, SHOPIFY_SECRET);
$shopDetails = $shopifyClient->call('GET', '/admin/themes.json', array());
$footer_text ="{{ shop.metafields.external.head }} </head>";
foreach ($shopDetails as $value) {
		$template_id = $value['id'];
$tsid = $value['theme_store_id'];

if($tsid == $data['theme_store_id']){
//$message =  $tsid.'sdf'.$data['theme_store_id'];
	
	    try {
		    $themeDetails 	= $shopifyClient->call('GET', '/admin/themes/'.$template_id.'/assets.json?asset[key]=layout/theme.liquid&theme_id='.$template_id, array());
	    } catch(ShopifyApiException $e){
	        continue; // move to next item in array
	    }
	    
		$theme_liquid = $themeDetails['value'];
		

		$final 	 = str_replace('</head>', $footer_text, $theme_liquid);

		// Upload new content
	 	$params   		= array(
	 						'asset' => array(
 									'key'	=> 'layout/theme.liquid',
 									'value'	=> $final
 									)
	 						);

		$updateTheme	= $shopifyClient->call('PUT', '/admin/themes/'.$template_id.'/assets.json', $params);
	}
	}
	

	
		
		

	}
}
?>