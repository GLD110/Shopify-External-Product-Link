<?php
include('include/header.php');
?>
<div class="container margintop">
    <div class="row external_header">
       <div class="col-sm-4"></div>
       <div class="col-sm-4">
            <h2 class="titlestats">Stats</h2>
        </div>
        <form action="stats.php" method="POST"><div class="col-sm-4"> <input type="text" name="search" placeholder="Search.." class="searchproduct" id="searchproduct" value="<?php echo $_REQUEST['search'];?>"><input type='hidden' value="<?php if($_REQUEST['startdate'] !=''){ echo $_REQUEST['startdate'];}else{ echo date("Y-m-d");} ?>" name="startdate" /><input type='hidden' value="<?php if($_REQUEST['enddate'] !=''){ echo $_REQUEST['enddate'];}else{echo date("Y-m-d");} ?>" name="enddate" /></div></form>
    </div> 
</div>
<form method="post" action="">
<div class="container external_header">
    <div class="row">
	    <form action="stats.php" id="stats_form" method="POST">
            <input type="hidden" name="search" id="search" value="<?php echo $_REQUEST['search'];?>">
	    	<input type="hidden" name="sort" value="<?php echo $_REQUEST['sort'];?>">
	    	<input type="hidden" name="direction" value="<?php echo $_REQUEST['direction'];?>">
	    	<div class='col-sm-8'>
		        <div class='col-sm-4'>
		            <div class="form-group">
		                <div class='input-group date' id='datetimepicker1'>
		                    <input type='text' class="form-control" value="<?php if($_REQUEST['startdate'] !=''){ echo $_REQUEST['startdate'];}else{ echo date("Y-m-d");} ?>" name="startdate" />
		                    <span class="input-group-addon">
		                        <span class="glyphicon glyphicon-calendar"></span>
		                    </span>
		                </div>
		            </div>
		        </div>  
		         <div class='col-sm-4'>
		            <div class="form-group">
		                <div class='input-group date' id='datetimepicker2'>
		                    <input type='text' class="form-control" value="<?php if($_REQUEST['enddate'] !=''){ echo $_REQUEST['enddate'];}else{echo date("Y-m-d");} ?>" name="enddate" />
		                    <span class="input-group-addon">
		                        <span class="glyphicon glyphicon-calendar"></span>
		                    </span>
		                </div>
		            </div>
		        </div> 
		        <div class='col-sm-4'>
		        	<button type="submit" class="btn strtbtn">Apply</button>
		        </div> 
	        </div>    
	    </form>
        <div class="col-sm-3">    
            <div class="col-sm-6">
             <button type="button" onclick="history.back();"  class="btn strtbtn">Back</button>

            <!-- <a href="index.php"  class="btn strtbtn">Back</a> -->
            </div>
            <div class="col-sm-6"><button type="submit" class="btn strtbtn">Refresh</button></div> 
        </div>    
    </div>
</div>
<div class="container margintop statelist">
    <div class="row stattitle">
         <div class="col-sm-4"><h4><a href="stats.php?<?php if($_GET["page"]) { echo 'page='.$_GET["page"].'&'; } ?>sort=name&direction=<?php if($_REQUEST['direction'] == 'desc') { echo 'asc'; } else { echo 'desc'; } ?>&startdate=<?php echo $_REQUEST['startdate']; ?>&enddate=<?php echo $_REQUEST['enddate'];?>" style="text-decoration: none; color: black;">External Product Links</a><i class="glyphicon glyphicon-chevron-down">&nbsp;</i></h4></div>
         <div class="col-sm-4"><h4><a href="stats.php?<?php if($_GET["page"]) { echo 'page='.$_GET["page"].'&'; } ?>sort=click&direction=<?php if($_REQUEST['direction'] == 'desc') { echo 'asc'; } else { echo 'desc'; } ?>&startdate=<?php echo $_REQUEST['startdate']; ?>&enddate=<?php echo $_REQUEST['enddate'];?>" style="text-decoration: none; color: black;">Link Clicks</a><i class="glyphicon glyphicon-chevron-down">&nbsp;</i></h4></div>
               <div class="col-sm-4"><h4>Reset</h4></div>  
    </div>
    <?php 
    $search_sql = "";
    $sort_sql = "";

    if($_REQUEST['search']) {
        $search_sql = " and (product_name like '%".addslashes($_REQUEST['search'])."%')";
    }

    if($_REQUEST['sort']) {
        if($_REQUEST['sort'] == "name") {
            $sort_sql = " order by product_name ";
        } else {
    	    $sort_sql = " order by count(product_id) ";
        }
    }

    if($_REQUEST['direction']) {
    	$sort_sql .= $_REQUEST['direction'];
    } else {
    	$sort_sql .= "ASC";
    }

    if(isset($_REQUEST['startdate'])) {
        $sql = "select product_id, count(product_id) as linkhits from stats where shop_id = '".$_SESSION['shop_id']."' and `date` between '".$_REQUEST['startdate']."' and '".$_REQUEST['enddate']."' ".$search_sql." group by product_id ".$sort_sql.""; 
    } else {
        $sql = "select product_id, count(product_id) as linkhits from stats where shop_id = '".$_SESSION['shop_id']."' and `date` between '".date("Y-m-d")."' and '".date("Y-m-d")."' ".$search_sql." group by product_id ".$sort_sql.""; 
    }

    $rpp = 12; // results per page
    $adjacents = 3;
    $page = intval($_GET["page"]);
    if($page<=0) $page = 1;
    $reload = $_SERVER['PHP_SELF'];
    if($_GET['search']) {
        $reload = $reload."?search=".$_GET['search']."&";
        if($_REQUEST['sort']) {
        	$reload .= "sort=".$_REQUEST['sort']."&";
        }
        if($_REQUEST['direction']) {
        	$reload .= "direction=".$_REQUEST['direction']."&";
        }
    } else {
        $reload = $reload."?";
        if($_REQUEST['sort']) {
        	$reload .= "sort=".$_REQUEST['sort']."&";
        }
        if($_REQUEST['direction']) {
        	$reload .= "direction=".$_REQUEST['direction']."&";
        }
    }
    $result = mysqli_query($con, $sql);
    if (mysqli_connect_errno())
    {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    // count total number of appropriate listings:
    $tcount=mysqli_num_rows($result);
    $tpages = ($tcount) ? ceil($tcount/$rpp) : 1; // total pages, last page number
    $count = 0;
    
    if($page)
    {
        $i = ($page-1)*$rpp;
    }
    else
    {
        $i=1;
        $i = ($i)*$rpp;
    }

    ?>
    <?php
    if($tcount>0) {
        while(($count<$rpp) && ($i<$tcount))
        {
            mysqli_data_seek($result,$i);
            $row = mysqli_fetch_array($result);

            $productDetails = getProductDetails($row["product_id"],$_SESSION['shop']);
        ?>
         <div class="row pdetail">
             <div class="col-sm-4 ptitle"><h4><?php echo $productDetails['title']; ?></h4></div>
             <div class="col-sm-4"><h4><?php echo $row['linkhits'] ?></h4></div> 
                        <div class="col-sm-4"><a href="javascript:" class="resetlink" data-id='<?php echo $row["product_id"];?>'>Reset Stats</a></div>         
    
         </div>
        <?php $i++; $count++; 
        }
	}

    if(isset($_REQUEST['startdate'])) {
        $sql2 = "select SUM(linkhits) from (select count(product_id) as linkhits from stats where shop_id = '".$_SESSION['shop_id']."' and `date` between '".$_REQUEST['startdate']."' and '".$_REQUEST['enddate']."' group by product_id) as totalhits";
    } else {
	   $sql2 = "select SUM(linkhits) from (select count(product_id) as linkhits from stats where shop_id = '".$_SESSION['shop_id']."' and `date` between '".date("Y-m-d")."' and '".date("Y-m-d")."' group by product_id) as totalhits";
    }
	$result2 = mysqli_query($con, $sql2);
	if (mysqli_connect_errno())
    {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
	$row2 = mysqli_fetch_assoc($result2);
    ?>
    <div class="row stattitle">
        <div class="col-sm-4"><h4>Total</h4></div>
        <div class="col-sm-4"><h4><?php echo ($row2['SUM(linkhits)']) ? $row2['SUM(linkhits)'] : '0'; ?></h4></div>
                <div class="col-sm-4"><h4>&nbsp;</h4></div>         
   
    </div>
    <?php 
    if($tcount>0) { 
        include("pagination.php"); 
        echo paginate_three($reload, $page, $tpages, $adjacents);
    }
    ?>
</div>
</form>
</body>
</html>
<script type="text/javascript">
$(document).ready(function() {
    $(document).on("click",".resetlink",function() {
        bootbox.confirm('Do you want to reset the stats', function(result) {
            if(result) {
				
                var appurl = 'https://www.thirstysoftware.com/externalproductlinks/';           
                var productid = $(".resetlink").attr("data-id");


                $.ajax({
                    url: "https://www.thirstysoftware.com/externalproductlinks/product_reset.php",
                  type: "POST",
                    data: {product_id : productid },
                    dataType: "json",
                    success: function(response) {
				//alert('confirm');
				
                        if(response.result == "true") {
					
                            window.location.reload();
                        } else {
                            bootbox.alert("Error! Please try again.");
                        }
                    }
                });
            }    
        });
    });

    $(document).on("keyup keydown", "#searchproduct", function() {
        $("#search").val($(this).val());
    });
});
</script>