<?php
include_once('functions.php');

if(isset($_GET['shop']) && $_GET['shop'] != $_SESSION['shop']) {
    unset($_SESSION['shop']);
    unset($_SESSION['shop_id']);
}

if(!$_SESSION['shop_id']) {
    if(!$_GET['shop']) {
        echo "Shop doesn't exists!";
        die;
    }
	$shopifyClient = new ShopifyClient($_GET['shop'], SHOPIFY_ACCESS_TOKEN, SHOPIFY_API_KEY, SHOPIFY_SECRET);
    $authorizeUrl = $shopifyClient->getAuthorizeUrl(SHOPIFY_SCOPE, REDIRECT_URL);
    header("Location:".$authorizeUrl);
    exit();
} else {
    $shopDetails = getShopByID($_SESSION['shop_id']);
    if($shopDetails) {
        $shopifyClient = new ShopifyClient($shopDetails['shop_name'], $shopDetails['access_token'], SHOPIFY_API_KEY, SHOPIFY_SECRET);
        try{
            $response = $shopifyClient->call('GET', '/admin/webhooks/count.json', array());
            if(!$response) {
                $authorizeUrl = $shopifyClient->getAuthorizeUrl(SHOPIFY_SCOPE, REDIRECT_URL);
                header("Location:".$authorizeUrl);
                exit();
            }
        } catch(Exception $e) {
            $shopifyClient = new ShopifyClient($shopDetails['shop_name'], SHOPIFY_ACCESS_TOKEN, SHOPIFY_API_KEY, SHOPIFY_SECRET);
            $authorizeUrl = $shopifyClient->getAuthorizeUrl(SHOPIFY_SCOPE, REDIRECT_URL);
            header("Location:".$authorizeUrl);
            exit();
        }
    } else {
        if(!$_GET['shop']) {
            echo "Shop doesn't exists!";
            die;
        }
        $shopifyClient = new ShopifyClient($_GET['shop'], SHOPIFY_ACCESS_TOKEN, SHOPIFY_API_KEY, SHOPIFY_SECRET);
        $authorizeUrl = $shopifyClient->getAuthorizeUrl(SHOPIFY_SCOPE, REDIRECT_URL);
        header("Location:".$authorizeUrl);
        exit();
    }
}

$shopDetails = getShopByID($_SESSION['shop_id']);
if($shopDetails['access_token']) {
    if($shopDetails['payment_status'] != "accepted" && $shopDetails['payment_status'] != "active") {
        echo '<script>window.top.location.href = "'.SITE_URL.'/validate_payment.php";</script>';
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Shopify app</title>
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/bootstrap-multiselect.css">
        <link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/responsive.bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/bootstrap-toggle.min.css" >
         <link rel="stylesheet" type="text/css" href="css/bootstrap-datetimepicker.css" >
        <script type="text/javascript" src="js/all.js"></script>
        <script type="text/javascript" src="https://cdn.shopify.com/s/assets/external/app.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/bootbox.js"></script>
        <script type="text/javascript" src="js/bootstrap-multiselect.js"></script>
        <script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="js/dataTables.bootstrap.min.js"></script>
        <script type="text/javascript" src="js/dataTables.responsive.min.js"></script>
        <script type="text/javascript" src="js/responsive.bootstrap.min.js"></script>
        <script type="text/javascript" src="js/bootstrap-toggle.min.js"></script>
         <script type="text/javascript" src="js/moment.js"></script>
        <script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
        <script type="text/javascript" src="js/jquery.twbsPagination.js"></script>
        <script type="text/javascript" src="js/tabs.js"></script>
        <script type="text/javascript">
            ShopifyApp.init({
                apiKey: "<?php echo SHOPIFY_API_KEY; ?>",
                shopOrigin: "https://<?php echo $_SESSION['shop'];?>"
            });

            ShopifyApp.ready(function() {
                ShopifyApp.Bar.loadingOff();

                ShopifyApp.Bar.initialize({

                  icon: '<?php echo SITE_URL;?>/images/ts.png',  
                  title: 'Welcome',
                  buttons: {
                    //btn-primary
                    //primary: { label: "Product Settings", href: "index.php" },
                    secondary: [{ label: "Product Settings", href: "index.php", style: 'primary' },{ label: "Stats", href: "stats.php" }, { label: "Instructions", href: "settings.php" }],
                  }
                });
            });

            function showLoading() {
                $(".loading").show();
            }

            function hideLoading() {
                $(".loading").hide();
            }
            $(function() {
                $('.toggle-one').bootstrapToggle();
              })
            $(function () {
                $('#datetimepicker1').datetimepicker({
                    format: "YYYY-MM-DD",         
                });
                $('#datetimepicker2').datetimepicker({
                    format: "YYYY-MM-DD"         
                });
            });
        </script>

        


        <style type="text/css">
            .loading{position: absolute;background-color: #000000;opacity: .35;top: 0;left: 0;width: 100%;height: 100%;display: none;z-index: 999999;}
            .spinner{width: 40px;height: 40px;position: relative;margin: 25% auto;}
            .double-bounce1, .double-bounce2 {width: 100%;height: 100%;border-radius: 50%;background-color: red;opacity: 0.6;position: absolute;top: 0;left: 0;-webkit-animation: sk-bounce 2.0s infinite ease-in-out;animation: sk-bounce 2.0s infinite ease-in-out;}
            .double-bounce2 {-webkit-animation-delay: -1.0s;animation-delay: -1.0s;background-color: yellow;}
            @-webkit-keyframes sk-bounce { 0%, 100% { -webkit-transform: scale(0.0) } 50% { -webkit-transform: scale(1.0) }}
            @keyframes sk-bounce { 0%, 100% { transform: scale(0.0); -webkit-transform: scale(0.0); } 50% { transform: scale(1.0); -webkit-transform: scale(1.0); }}
        </style>
    </head>
    <body>
        <div class="loading">
            <div class="spinner">
                <div class="double-bounce1"></div>
                <div class="double-bounce2"></div>
            </div>
        </div>