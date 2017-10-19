<?php
include('include/header.php');


/*

$shop =$_SESSION['shop'];

	$sql = "select `access_token` from shop where shop_name='".addslashes($shop)."'";
	$query = mysqli_query($sql) or die(mysqli_error());
	if(mysqli_num_rows($query) > 0) {
		$data =  mysqli_fetch_assoc($query);
	} 

	$access_token = $data['access_token'];

	$shopifyClient = new ShopifyClient($shop, $access_token, SHOPIFY_API_KEY, SHOPIFY_SECRET);
  //$response = $shopifyClient->call('GET', '/admin/webhooks.json', array());
  
 // print_r($response);
  
  
  
  
  $shopDetails = $shopifyClient->call('GET', '/admin/themes.json', array());


	
	$footer_text ="{{ shop.metafields.external.head }} </head>";
	
	foreach ($shopDetails as $value) {
		$template_id = $value['id'];
$tsid = $value['theme_store_id'];
echo $tsid.'  <-- <br>';
if($tsid == '829'){
	echo "work ** ".$template_id.'****'.$tsid;
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
  

*/




$AllProducts = getAllProducts($_REQUEST['search']);

if($_REQUEST['search']) {
    //$sel_search = mysqli_query('select product_id,product_name from products where external_link LIKE "%'.$_REQUEST["search"].'%"');
    //echo 'select product_id,product_name from products  where (external_link LIKE "%'.$_REQUEST["search"].'%" OR buttom_text LIKE "%'.$_REQUEST["search"].'%" OR product_name LIKE "%'.$_REQUEST["search"].'%" ) AND shop_id="'.$_SESSION['shop_id'].'"';
    $sel_search = mysqli_query($con, 'select product_id,product_name from products  where (external_link LIKE "%'.$_REQUEST["search"].'%" OR buttom_text LIKE "%'.$_REQUEST["search"].'%" OR product_name LIKE "%'.$_REQUEST["search"].'%" ) AND shop_id="'.$_SESSION['shop_id'].'"');
    $search_arr[] = array();
    
    //$AllProducts = ($AllProducts) ? $AllProducts[0] : array();
    $AllProducts = array();
    $loop = ($AllProducts) ? count($AllProducts[0]) : 0;
    while ($row = mysqli_fetch_assoc($sel_search)) {
        $AllProducts[$loop]['id'] = $row['product_id'];
        $AllProducts[$loop]['title'] = $row['product_name'];
        $loop++;
    }
    
} else {
    
    $AllProducts = $AllProducts[0];
}

$currentPage = ($_GET['page']) ? $_GET['page'] : 1;

function get_page(array $input, $pageNum, $perPage = 5) {
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



if($_POST['action'] == 'shop_uninstall'){
    
    
   if( shopUninstall($_SESSION['shop'])){
       echo '<div class="alert alert-success alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Success!</strong> Our code has been removed from your theme. Please delete the app to complete the uninstall process.
</div>';
   }
}

?>

<div class="container margintop">
    <div class="row external_header">
       <div class="col-sm-4">
           <form method="POST" action="index.php" id="uninstallform">
           <input name="action" type="hidden" value="shop_uninstall">
           </form>
           <button  class="btn strtbtn" value="shop_uninstall" id="uninstallbtn" name="action" style="color: RED;" onclick="uninstallConfirmation()">Click Here To Uninstall External Product Links</button>
       </div>
       <div class="col-sm-4">
            <div class="col-sm-4"><button type="button" class="btn strtbtn">Save</button></div> 
            <div class="col-sm-4">
             <button type="button" onclick="history.back();"  class="btn strtbtn">Back</button>

            <!-- <a href="stats.php"  class="btn strtbtn">Back</a> -->
            </div>
        </div>
        <div class="col-sm-4"> <form method="POST" action="index.php"><input type="text" name="search" placeholder="Search.." class="searchproduct" value="<?php echo $_REQUEST['search']; ?>"></div></form>
    </div>
</div>
<form method="POST" action="product_insert.php" id="productform">
    <div class="container productlist">
        <div class="row headerproduct">
            <div class="col-sm-3"><h4>Product</h4></div>
            <div class="col-sm-3"><h4>External URL</h4></div>
            <div class="col-sm-3"><h4>Button text</h4></div>
            <div class="col-sm-2 pull-right"><h4>On/Off</h4></div>
        </div>
        <?php
        if(!$AllProducts){
            $AllProducts = array();
        
        }
        $AllProduct = get_page($AllProducts, $currentPage);
        foreach($AllProduct as  $value) {
            ?>
            <div class="row productlist">
                <?php $id = $value['id'];
                 $data = mysqli_query($con, "select * from products where product_id = '".$id."'");
                 $row = mysqli_fetch_row($data);
                 if(trim($row[5]) == ""){
                 $row[5] = "off";
                 }
                 ?>
                
                <!-- <?php #echo print_r($row,1); ?> -->
                 
                <div class="col-sm-3 ptitle">
                <h4 class="productname"><?php echo $value['title'];?></h4>
                <?php if($row[3] !=''){?><a href="<?php echo $row[3];?>" target='_blank'>Test link</a><?php }?>
                </div>
                <div class="col-sm-3"><input type="text" name="externallink[]" class="pname frminput" value="<?php echo $row[3];?>"></div>
                <div class="col-sm-3"><input type="text" name="buttontext[]" class="buttontext frminput" value="<?php echo $row[4];?>"></div>
                <div class="col-sm-2 pull-right statuson"> <input type="hidden" name="statu[]" value="<?php echo $row[5];?>" class="onoff">
                <input class="toggle-one" <?php if($row[5] == 'on'){echo "checked";}?> type="checkbox" name="onoff[]" value="<?php echo $row[5];?>">
                
                </div>
                <input type="hidden" name="productid[]" value="<?php echo $value['id'];?>" />
            </div>
        <?php 
        }
        ?>  
        <ul class="pagination">
            <?php 
            $totalPages = ($AllProducts && count($AllProducts) > 0) ? ceil(count($AllProducts)/5) : 0; 

            if($currentPage > 1) {
                $previous_page = $currentPage - 1;
                echo '<li><a href="'.SITE_URL.'?page='.$previous_page.'&search='.$_REQUEST['search'].'"> < </a></li>';
            }
            
            for($page = 1; $page <= $totalPages; $page++) {
                if($currentPage == $page) {
                    $cls = "pagiactive";
                } else {
                    $cls = "inactive";
                }
                echo '<li><a class="'.$cls.'" href="'.SITE_URL.'?page='.$page.'&search='.$_REQUEST['search'].'">'.$page.'</a></li>';
            }

            if($currentPage < $totalPages) {
                $next_page = $currentPage + 1;
                echo '<li><a href="'.SITE_URL.'?page='.$next_page.'&search='.$_REQUEST['search'].'"> > </a></li>';
            }
            ?>
        </ul>
    </div>
    <div class="container footer">
        
        <div class="col-sm-4"></div>
        <div class="col-sm-4">
            <div class="col-sm-4"><button type="submit" class="btn strtbtn">Save</button></div> 
            <div class="col-sm-4">
            <button type="button" onclick="history.back();"  class="btn strtbtn">Back</button>
             <!-- <a href="<?php echo $_SERVER['HTTP_REFERER'] ?>" class="btn strtbtn">Back</a> -->
           <!--  <a href="stats.php"  class="btn strtbtn">Back</a> -->
            </div>
        </div>
        <div class="col-sm-4"> </div>
    </div>
</form>
<script type="text/javascript">
$(document).ready(function(){
    $(document).on("click",".strtbtn",function() {
        $('form#productform').submit();
    });
   

    $(document).on("click",".toggle",function() {
        if($(this).hasClass("off")) {
            $(this).parent('.statuson').find(".onoff").val("off");
        } else {
            $(this).parent('.statuson').find(".onoff").val("on");
        }
    });

});


function uninstallConfirmation() {
    
    if (confirm("Are you sure you want to Uninstall External Product Links from your theme?") == true) {
    
    $(document).on("click",".strtbtn",function() {
        $('form#uninstallform').submit();
    });
    }else{
        return;
    }
    
}

</script>
</body>
</html>