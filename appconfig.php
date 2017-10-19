<?php
include('include/header.php');
$getSettings = getSettingsDetails();
if(mysqli_num_rows($getSettings) > 0){
    $row = mysqli_fetch_array($getSettings);
}
?>
<style type="text/css">
    #addbundle{min-height: 700px;}
    h3{margin-left: 15px;}
    a:hover{text-decoration: none;}
    label{cursor: pointer;width: 95%;}
    input[type="file"] {display: block;height: auto;}
    select {float: left; padding: 10px 0; width: 100%;}
    .form-group { margin-bottom: 5px!important;}
    .form-control{margin-bottom: 20px;}
    input[type="radio"] { line-height: normal; margin: 4px 0 0; width: auto;}
</style>
<script type="text/javascript">
$(document).ready(function() {
    $(document).on("click", ".installsnippet", function() {
        showLoading();
        var snippetname = $(this).attr("dataname");
        $.ajax({
            type: "POST",
            url: 'installsnippet.php',
            data: {snippetname: snippetname},
            success: function(response) {
                hideLoading();
                alert("Snippet has been installed.");
            },
            error: function() {
                alert("We couldn't install snippet in your theme. Please manually create the snippet using below code.");
            }
        });
    });
});

</script>
<div class="container">
    <div class="row">
        <div class="panel-group">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h4 class="panel-title"><b>A1. To install the Bundle Product snippet, click on the install button.</b></h4><br>
                    <a href="javascript:;" dataname="bb_bundle_products" class="installsnippet btn btn-info">Install</a><br><br>
                    <h4 class="panel-title"><b>A2. If above install didn't work for you, you can manually create a snippet in your <a href="https://<?php echo $shopDetails['shop_name']; ?>/admin/themes/current" target="_top" style="float:none;font-size:16px;color:red;text-transform:lowercase;">active theme</a>. Just copy the below code and create a file under <font style="color: red;">snippets/bb_bundle_products.liquid</font> and paste below code inside this file.</b></h4><br>
                    <pre><code><?php $html = getBundleBuddySnippetCode(); echo htmlentities(trim($html)); ?></code></pre><br>
                    <h4 class="panel-title"><b>A3. Copy the below code and paste in <a href="https://<?php echo $shopDetails['shop_name']; ?>/admin/themes/current/?key=templates/product.liquid" target="_top" style="float:none;font-size:16px;color:red;text-transform:lowercase;">product.liquid</a> above <span style="float:none;font-size:16px;color:red;text-transform:lowercase;">Buy Now Button OR where you want to show</span>.</b></h4><br>
                    <pre><code>{% include 'bb_bundle_products' %}</code></pre><br><br>
                </div>

                <div class="panel-body">
                    <h4 class="panel-title"><b>B1. To install the Bundle Normal Price snippet, click on the install button.</b></h4><br>
                    <a href="javascript:;" dataname="bb_normal_price" class="installsnippet btn btn-info">Install</a><br><br>
                    <h4 class="panel-title"><b>B2. If above install didn't work for you, you can manually create a snippet in your <a href="https://<?php echo $shopDetails['shop_name']; ?>/admin/themes/current" target="_top" style="float:none;font-size:16px;color:red;text-transform:lowercase;">active theme</a>. Just copy the below code and create a file under <font style="color: red;">snippets/bb_normal_price.liquid</font> and paste below code inside this file.</b></h4><br>
                    <pre><code><?php $html = getNormalPriceSnippetCode(); echo htmlentities(trim($html)); ?></code></pre><br>
                    <h4 class="panel-title"><b>B3. Copy the below code and paste in <a href="https://<?php echo $shopDetails['shop_name']; ?>/admin/themes/current/?key=templates/product.liquid" target="_top" style="float:none;font-size:16px;color:red;text-transform:lowercase;">product.liquid</a> above <span style="float:none;font-size:16px;color:red;text-transform:lowercase;">Main Price OR where you want to show</span>.</b></h4><br>
                    <pre><code>{% include 'bb_normal_price' %}</code></pre><br><br>
                </div>

                <div class="panel-body">
                    <h4 class="panel-title"><b>C1. To install the Bundle Cart Upsell Product snippet, click on the install button.</b></h4><br>
                    <a href="javascript:;" dataname="bb_upsell_cart" class="installsnippet btn btn-info">Install</a><br><br>
                    <h4 class="panel-title"><b>C2. If above install didn't work for you, you can manually create a snippet in your <a href="https://<?php echo $shopDetails['shop_name']; ?>/admin/themes/current" target="_top" style="float:none;font-size:16px;color:red;text-transform:lowercase;">active theme</a>. Just copy the below code and create a file under <font style="color: red;">snippets/bb_upsell_cart.liquid</font> and paste below code inside this file.</b></h4><br>
                    <pre><code><?php $html = getUpsellSnippetCode(); echo htmlentities(trim($html)); ?></code></pre><br>
                    <h4 class="panel-title"><b>C3. Copy the below code and paste in <a href="https://<?php echo $shopDetails['shop_name']; ?>/admin/themes/current/?key=templates/cart.liquid" target="_top" style="float:none;font-size:16px;color:red;text-transform:lowercase;">cart.liquid</a> <span style="float:none;font-size:16px;color:red;text-transform:lowercase;">where you want to show</span>.</b></h4><br>
                    <pre><code>{% include 'bb_upsell_cart' %}</code></pre><br><br>
                </div>
          </div>
        </div>
    </div>
</div>
</body>
</html>