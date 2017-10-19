<?php
require_once('config.php');

function addSlashesArray($array) {
	foreach ($array as $key => $val) {
		if (is_array($val)) {
			$array[$key] = addSlashesArray($val);
		} else {
			$array[$key] = addslashes($val);
		}
	}
	return $array;
}

function registerShop($params) {
    global $con;
	$params = addSlashesArray($params);
	try {
		$sql = "insert into shop set code='".addslashes($params['code'])."',shop_name='".addslashes($params['shop'])."',hmac='".addslashes($params['hmac'])."',signature='".addslashes($params['signature'])."',date_created='".date("Y-m-d H:i:s")."'";
		$query = mysqli_query($con, $sql);
		if (mysqli_connect_errno())
    {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
		$_SESSION['shop_id'] = mysqli_insert_id();
		$_SESSION['shop'] = $params['shop'];
		installApplication($params['shop'], $params['code']);
		return true;
	} catch(Exception $e) {
		return false;
	}
}

function loginShop($params) {
    global $con;
	try {
		$sql = "update shop set code='".addslashes($params['code'])."',hmac='".addslashes($params['hmac'])."',signature='".addslashes($params['signature'])."' where shop_name='".addslashes($params['shop'])."'";
		$query = mysqli_query($con, $sql);
		if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

		$sql = "select shop_pid,code from shop where shop_name='".addslashes($params['shop'])."'";
		$query = mysqli_query($con, $sql);
		if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
		if(mysqli_num_rows($query) > 0) {
			$row = mysqli_fetch_assoc($query);
			$_SESSION['shop_id'] = $row['shop_pid'];
			$_SESSION['shop'] = $params['shop'];
			installApplication($params['shop'], $params['code']);
			return true;
		} else {
			return false;
		}
	} catch(Exception $e) {
		return false;
	}
}

function checkShopRegistered($shop) {
    global $con;
	try {
		$sql = "select shop_pid from shop where shop_name='".addslashes($shop)."'";
		
		$query = mysqli_query($con, $sql);
		//var_dump($con);
		if (mysqli_connect_errno())
         {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }
		if(mysqli_num_rows($query) > 0) {
			return true;
		} else {
			return false;
		}
	} catch(Exception $e) {
		return false;
	}
}

function installApplication($shop, $code) {
    global $con;
	$shopifyClient = new ShopifyClient($shop, SHOPIFY_TOKEN, SHOPIFY_API_KEY, SHOPIFY_SECRET);

	$accessToken = $shopifyClient->getAccessToken($code);

	$sql = "update shop set access_token='".$accessToken."' where shop_name='".addslashes($shop)."'";
	$query = mysqli_query($con, $sql);
	if (mysqli_connect_errno())
    {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

	$shopifyClient = new ShopifyClient($shop, $accessToken, SHOPIFY_API_KEY, SHOPIFY_SECRET);

	try {

		$shopDetails = $shopifyClient->call('GET', '/admin/shop.json', array());

		$sql = "update shop set currency='".addslashes($shopDetails['currency'])."',name='".addslashes($shopDetails['name'])."',shop_email='".addslashes($shopDetails['email'])."',owner_name='".addslashes($shopDetails['shop_owner'])."',phone_no='".addslashes($shopDetails['phone'])."',street='".addslashes($shopDetails['address1'])."',city='".addslashes($shopDetails['city'])."',zip='".addslashes($shopDetails['zip'])."',country='".addslashes($shopDetails['country_name'])."' where shop_name='".addslashes($shop)."'";
		$query = mysqli_query($con, $sql);
			if (mysqli_connect_errno())
        {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

		$data = array(
	        "webhook" => array(
	            "topic" => "shop/update",
	            "address" => SITE_URL."/shop_update.php",
	            "format" => "json"
	        )
	    );

	    $shopifyClient->call('POST', '/admin/webhooks.json', $data);

	    $data = array(
	        "webhook" => array(
	            "topic" => "app/uninstalled",
	            "address" => SITE_URL."/shop_uninstalled.php",
	            "format" => "json"
	        )
	    );

	    $shopifyClient->call('POST', '/admin/webhooks.json', $data);
		
		
		
		
		
		$data = array(
	        "webhook" => array(
	            "topic" => "themes/create",
	            "address" => SITE_URL."/shop_themechange.php",
	            "format" => "json"
	        )
	    );

	    $shopifyClient->call('POST', '/admin/webhooks.json', $data);

	    $response_js = $shopifyClient->call('GET', '/admin/script_tags.json?src='.SITE_URL."/js/function.js", array());

       	if(!$response_js) {        
               $data = array(
               "script_tag" => array(
                   "event" => "onload",
                   "src" => SITE_URL."/js/function.js"
               )
           );
        	$shopifyClient->call('POST', '/admin/script_tags.json', $data);
       	}
       	addContent($shop);
	    $response = createPayment(); 
	       
	    return $response[0]['confirmation_url'];

	} catch(Exception $e) {
		
	}
}

function addContent($shop) {
    global $con;
	$sql = "select `access_token` from shop where shop_name='".addslashes($shop)."'";
	$query = mysqli_query($con, $sql);
		if (mysqli_connect_errno())
    {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
	if(mysqli_num_rows($query) > 0) {
		$data =  mysqli_fetch_assoc($query);
	} 

	$access_token = $data['access_token'];

	$shopifyClient = new ShopifyClient($shop, $access_token, SHOPIFY_API_KEY, SHOPIFY_SECRET);

	// $p_params = array(
	// 				'script_tag' => array(
	// 							'event' => "onload",
	// 							'src'	=> 'http://thirstysoftware.com/externalproductlinks/js/external-links.js',
	// 							'display_scope' => 'online_store'
	// 								) 
	// 				);
	// $shopDetails = $shopifyClient->call('POST', '/admin/script_tags.json', $p_params);





	$shopDetails = $shopifyClient->call('GET', '/admin/themes.json', array());

	//$head_text = '<style type="text/css">form[action="/cart/add"] input[type="submit"], form[action="/cart/add"] input[type="button"], form[action="/cart/add"] button {display:none;}select[name="id"] {display:none !important;}</style></head>';
	/*$footer_text = '<script>$( document ).ready(function() {if($("form[action="/cart/add"] select option").length == 1 && $("form[action="/cart/add"] select").find("option:first").val() == "Default Title"){$("head").append("<style >form[action="/cart/add"] select{display:none !important;}</style>");}});</script></body>';
*/
	// Get Theme Id's
	
	$footer_text ="{{ shop.metafields.external.head }} </head>";
	
	foreach ($shopDetails as $value) {
		$template_id = $value['id'];

		$themeDetails 	= $shopifyClient->call('GET', '/admin/themes/'.$template_id.'/assets.json?asset[key]=layout/theme.liquid&theme_id='.$template_id, array());
		$theme_liquid = $themeDetails['value'];
		
		// Content Updated
		//$updated = str_replace('</head>', $head_text, $theme_liquid);
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
	
	
	
	
	
	
$forms= 'form[action="/cart/add"] select' ;
$forms1= 'form[action="/cart/add"] select option' ;

	
	$data='<style type="text/css">form[action="/cart/add"] input[type="submit"], form[action="/cart/add"] input[type="button"], form[action="/cart/add"] button {display:none;}select[name="id"] {display:none !important;}</style>';
$data.='<script>$( document ).ready(function() {';
$data.="if($('".$forms1."').length == 1 && ";
$data.="$('".$forms."').find('option:first').val() == 'Default Title'){";
$data.="$('".$forms."').css({'display':'none !important'});";
$data.='}});</script>';

$shop1 =  $shopifyClient->call('GET', '/admin/metafields.json'); 


$product =  array(
 "metafield" => array(
 "namespace" => "external",
"key" =>"head",
 "value" =>$data,  "value_type" => "string" ));
  	    $response = $shopifyClient->call('POST', '/admin/metafields.json', $product);
  


	
	
	
	
		return true;
}





function createPayment() {
    global $con;
	try {
	    $shopDetails = getShopByName($_SESSION['shop']);
		$shopifyClient = new ShopifyClient($shopDetails['shop_name'], $shopDetails['access_token'], SHOPIFY_API_KEY, SHOPIFY_SECRET);

		$payment = array(
	    	"recurring_application_charge" => array(
	    		"name" => "Create External Product Link",
			    "price" => APP_COST,
			    "return_url" => SITE_URL.'/validate_payment.php',
			    "trial_days" => TRIAL_DAYS
	    	)
	    );
	    $response = $shopifyClient->call('POST', '/admin/recurring_application_charges.json', $payment);

	    if($response[0]['id']) {
		    $sql = "update shop set payment_id='".addslashes($response[0]['id'])."' where shop_pid='".addslashes($_SESSION['shop_id'])."'";
			$query = mysqli_query($con, $sql);
				if (mysqli_connect_errno())
    {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
		} else {
			$sql = "update shop set payment_id='".addslashes($response['id'])."' where shop_pid='".addslashes($_SESSION['shop_id'])."'";
			$query = mysqli_query($con, $sql);
				if (mysqli_connect_errno())
    {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
		}

	    return $response;
	} catch(Exception $e) {
		
	}    
}

function activatePayment($params) {
    global $con;
	try {
	    $shopDetails = getShopByName($_SESSION['shop']);
		$shopifyClient = new ShopifyClient($shopDetails['shop_name'], $shopDetails['access_token'], SHOPIFY_API_KEY, SHOPIFY_SECRET);

		$payment = array(
	    	"recurring_application_charge" => $params
	    );

	    $response = $shopifyClient->call('POST', '/admin/recurring_application_charges/'.$shopDetails['payment_id'].'/activate.json', $payment);

	    $sql = "update shop set payment_status='".addslashes($response['status'])."' where shop_pid='".addslashes($_SESSION['shop_id'])."'";
		$query = mysqli_query($con, $sql);
			if (mysqli_connect_errno())
    {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

	    return $response;
	} catch(Exception $e) {
		
	}    
}

function getShopByName($shop) {
    global $con;
	$sql = "select * from shop where shop_name='".addslashes($shop)."'";
	$query = mysqli_query($con, $sql);
		if (mysqli_connect_errno())
    {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
	if(mysqli_num_rows($query) > 0) {
		return mysqli_fetch_assoc($query);
	} else {
		return false;
	}
}

function getShopByID($shop_id) {
    global $con;
	$sql = "select * from shop where shop_pid='".addslashes($shop_id)."'";
	$query = mysqli_query($con, $sql);
		if (mysqli_connect_errno())
    {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
	if(mysqli_num_rows($query) > 0) {
		return mysqli_fetch_assoc($query);
	} else {
		return false;
	}
}

function encrypt_string($string) {
	return base64_encode(base64_encode(base64_encode(base64_encode(base64_encode($string)))));
}

function decrypt_string($string) {
	return base64_decode(base64_decode(base64_decode(base64_decode(base64_decode($string)))));
}

function getProducts($page) {
	$shopDetails = getShopByName($_SESSION['shop']);
	$shopifyClient = new ShopifyClient($shopDetails['shop_name'], $shopDetails['access_token'], SHOPIFY_API_KEY, SHOPIFY_SECRET);
	$response = $shopifyClient->call('GET', '/admin/products.json?limit=2&page='.$page, array());
	return $response;
}

function getProductCount(){
	$shopDetails = getShopByName($_SESSION['shop']);
	$shopifyClient = new ShopifyClient($shopDetails['shop_name'], $shopDetails['access_token'], SHOPIFY_API_KEY, SHOPIFY_SECRET);
	$response = $shopifyClient->call('GET', '/admin/products/count.json', array());
	return $response;
}

function getProductDetails($product_id,$shop) {
	$shopDetails = getShopByName($shop);
	$shopifyClient = new ShopifyClient($shopDetails['shop_name'], $shopDetails['access_token'], SHOPIFY_API_KEY, SHOPIFY_SECRET);
	$response = $shopifyClient->call('GET', '/admin/products/'.$product_id.'.json?fields=id,handle,title,image,variants', array());
	return $response;
}

function getAllProducts($search = "") {
	$shopDetails = getShopByName($_SESSION['shop']);
	$shopifyClient = new ShopifyClient($shopDetails['shop_name'], $shopDetails['access_token'], SHOPIFY_API_KEY, SHOPIFY_SECRET);

	$getCount = $shopifyClient->call('GET', '/admin/products/count.json?title='.$search.'', array());
	$totalPages = ceil($getCount/250);

	$response = array();

	for($page = 1; $page <= $totalPages; $page++) {
		$response[] = $shopifyClient->call('GET', '/admin/products.json?title='.$search.'&fields=id,title&limit=250&page='.$page, array());
	}
	return $response;
}
function getSimilarBundleIDsAPI($product_id){
    global $con;
	$sql = "select * from products where product_id = '".$product_id."'";
	$query = mysqli_query($con, $sql);
		if (mysqli_connect_errno())
    {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
	return $query;	
}
function productCount($product_id,$data,$shopname){
    global $con;
	$productname = getProductDetails($product_id,$shopname);
	$shopDetails = getShopByName($shopname);
	$sql = mysqli_query($con, "insert into stats set shop_id ='".$shopDetails['shop_pid']."' ,product_name='".$productname['title']."' ,product_id='".$product_id."',date='".$data."'");
	if($sql){
		return true;
	}else{
		return false;
	}
}
function resetProductCount($product_id,$shopname){
    global $con;
		file_put_contents('newfile.txt', 'so  far');
	$sql = "delete from stats where product_id='".$product_id."' and shop_id ='".$shopname."'";
	$query = mysqli_query($con, $sql);
		if (mysqli_connect_errno())
    {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
	file_put_contents('newfile.txt',mysqli_connect_error());
	if($$query){
		return true;
	}else{
		return false;
	}
}

function get_page2(array $input, $pageNum, $perPage) {
    $start = ($pageNum-1) * $perPage;
    $end = $start + $perPage;
    $count = count($input);

    // Conditionally return results
    if ($start < 0 || $count <= $start) {
        // Page is out of range
        return array(); 
    } else if ($count <= $end) {
        // Partially-filled page
        return array_slice($input, $start);
    } else {
        // Full page 
        return array_slice($input, $start, $end - $start);
    }
}

// shop_uninstall

function shopUninstall($shop) {
    global $con;
	$sql = "select `access_token` from shop where shop_name='".addslashes($shop)."'";
	$query = mysqli_query($con, $sql);
		if (mysqli_connect_errno())
    {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
	if(mysqli_num_rows($query) > 0) {
		$data =  mysqli_fetch_assoc($query);
	} 

	$access_token = $data['access_token'];

	$shopifyClient = new ShopifyClient($shop, $access_token, SHOPIFY_API_KEY, SHOPIFY_SECRET);

	// $p_params = array(
	// 				'script_tag' => array(
	// 							'event' => "onload",
	// 							'src'	=> 'http://thirstysoftware.com/externalproductlinks/js/external-links.js',
	// 							'display_scope' => 'online_store'
	// 								) 
	// 				);
	// $shopDetails = $shopifyClient->call('POST', '/admin/script_tags.json', $p_params);





	$shopDetails = $shopifyClient->call('GET', '/admin/themes.json', array());

	//$head_text = '<style type="text/css">form[action="/cart/add"] input[type="submit"], form[action="/cart/add"] input[type="button"], form[action="/cart/add"] button {display:none;}select[name="id"] {display:none !important;}</style></head>';
	/*$footer_text = '<script>$( document ).ready(function() {if($("form[action="/cart/add"] select option").length == 1 && $("form[action="/cart/add"] select").find("option:first").val() == "Default Title"){$("head").append("<style >form[action="/cart/add"] select{display:none !important;}</style>");}});</script></body>';
*/
	// Get Theme Id's
	
	$footer_text ="{{ shop.metafields.external.head }} </head>";
	
	foreach ($shopDetails as $value) {
		$template_id = $value['id'];

		$themeDetails 	= $shopifyClient->call('GET', '/admin/themes/'.$template_id.'/assets.json?asset[key]=layout/theme.liquid&theme_id='.$template_id, array());
		$theme_liquid = $themeDetails['value'];
		
		// Content Updated
		//$updated = str_replace('</head>', $head_text, $theme_liquid);
		$final 	 = str_replace($footer_text,'</head>', $theme_liquid);

		// Upload new content
	 	$params   		= array(
	 						'asset' => array(
 									'key'	=> 'layout/theme.liquid',
 									'value'	=> $final
 									)
	 						);

		$updateTheme	= $shopifyClient->call('PUT', '/admin/themes/'.$template_id.'/assets.json', $params);
	}
	
	
	
	
	/*
	
$forms= 'form[action="/cart/add"] select' ;
$forms1= 'form[action="/cart/add"] select option' ;

	
	$data='<style type="text/css">form[action="/cart/add"] input[type="submit"], form[action="/cart/add"] input[type="button"], form[action="/cart/add"] button {display:none;}select[name="id"] {display:none !important;}</style>';
$data.='<script>$( document ).ready(function() {';
$data.="if($('".$forms1."').length == 1 && ";
$data.="$('".$forms."').find('option:first').val() == 'Default Title'){";
$data.="$('".$forms."').css({'display':'none !important'});";
$data.='}});</script>';

$shop1 =  $shopifyClient->call('GET', '/admin/metafields.json'); 


$product =  array(
 "metafield" => array(
 "namespace" => "external",
"key" =>"head",
 "value" =>$data,  "value_type" => "string" ));
  	    $response = $shopifyClient->call('POST', '/admin/metafields.json', $product);
  


	*/
	
	
	
		return true;
}
?>