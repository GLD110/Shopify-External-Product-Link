<?php
include('functions.php');

if(isset($_POST['productid'])){   

    $i = 0;
    foreach ($_POST['productid'] as $postid) {
        $productname = getProductDetails($postid,$_SESSION['shop']);
        $buttontext = $_POST['buttontext'];
        $externallink = $_POST['externallink'];
        $status = $_POST['statu'];        
        $id = mysqli_query($con, "select product_id from products where product_id = '".$postid."'");
        $num = mysqli_num_rows($id);        
        if($num <= 0 ){
             $sql = mysqli_query($con, "insert into products set product_id = '".$postid."' , external_link = '".$externallink[$i]."' , buttom_text = '".$buttontext[$i]."' , status = '".$status[$i]."', shop_id = '".$_SESSION['shop_id']."', product_name = '".$productname['title']."'");             
         }else{  
           
          $sql = mysqli_query($con, "update  products set product_id = '".$postid."' , external_link = '".$externallink[$i]."' , buttom_text = '".$buttontext[$i]."' , status = '".$status[$i]."' , shop_id = '".$_SESSION['shop_id']."' , product_name = '".$productname['title']."' where product_id = '".$postid."'");          
         }  
    $i++;}
    if($sql){
                header('Location: index.php');
                exit();
             }
}else{
    header('Location: index.php');
    exit();
}

?>