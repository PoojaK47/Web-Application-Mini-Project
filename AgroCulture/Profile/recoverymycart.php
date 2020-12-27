
<!DOCTYPE html>
<html>
	<head>
		<title>Webslesson Demo | PHP Ajax Shopping Cart by using Bootstrap Popover</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
		<style>
		    /* Remove the navbar's default margin-bottom and rounded borders */ 
		    .navbar {
		      margin-bottom: 0;
		      border-radius: 0;
		    }
		    
		    /* Set height of the grid so .sidenav can be 100% (adjust as needed) */
		    .row.content {height: 450px}
		    
		    /* Set gray background color and 100% height */
		    .sidenav {
		      padding-top: 20px;
		      background-color: #f1f1f1;
		      height: 100%;
		    }
		    
		    /* Set black background color, white text and some padding */
		    footer {
		      background-color: #555;
		      color: white;
		      padding: 15px;
		    }
		    
		    /* On small screens, set height to 'auto' for sidenav and grid */
		    @media screen and (max-width: 767px) {
		      .sidenav {
		        height: auto;
		        padding: 15px;
		      }
		      .row.content {height:auto;} 
		    }
	  	</style>
	</head>
	<body>
		
		
		<div class="container-fluid text-center">    
  			<div class="row content">
  				
  				<div class="col-sm-8 text-left">
  					<br />
					<h3 align="center"><a href="https://www.webslesson.info/2018/11/shopping-cart-with-add-bulk-item-into-cart-using-php-ajax.html">PHP Ajax Shopping Cart with Add Multiple Item into Cart</a></h3>
					<br />
					<div class="panel panel-default">
						<div class="panel-heading">
							<div class="row">
								<div class="col-md-6">Cart Details</div>
								<div class="col-md-6" align="right">
									<button type="button" name="clear_cart" id="clear_cart" class="btn btn-warning btn-xs">Clear</button>
								</div>
							</div>
						</div>
						<div class="panel-body" id="shopping_cart">

						</div>
					</div>

					
  				<div class="col-sm-2">
  					
  				</div>
  			</div>
  		</div>
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-87739877-1', 'auto');
		  ga('send', 'pageview');

		</script>
	</body>
</html>

<script>  
$(document).ready(function(){

	load_product();

	load_cart_data();
    
	function load_product()
	{
		$.ajax({
			url:"fetch_item.php",
			method:"POST",
			success:function(data)
			{
				$('#display_item').html(data);
			}
		});
	}

	function load_cart_data()
	{
		$.ajax({
			url:"fetch_cart.php",
			method:"POST",
			success:function(data)
			{
				$('#shopping_cart').html(data);
			}
		});
	}

	$(document).on('click', '.select_product', function(){
		var product_id = $(this).data('product_id');
		if($(this).prop('checked') == true)
		{
			$('#product_'+product_id).css('background-color', '#f1f1f1');
			$('#product_'+product_id).css('border-color', '#333');
		}
		else
		{
			$('#product_'+product_id).css('background-color', 'transparent');
			$('#product_'+product_id).css('border-color', '#ccc');
		}
	});

	$('#add_to_cart').click(function(){
		var product_id = [];
		var product_name = [];
		var product_price = [];
		var action = "add";
		$('.select_product').each(function(){
			if($(this).prop('checked') == true)
			{
				product_id.push($(this).data('product_id'));
				product_name.push($(this).data('product_name'));
				product_price.push($(this).data('product_price'));
			}
		});

		

	$(document).on('click', '.delete', function(){
		var product_id = $(this).attr("id");
		var action = 'remove';
		if(confirm("Are you sure you want to remove this product?"))
		{
			$.ajax({
				url:"action.php",
				method:"POST",
				data:{product_id:product_id, action:action},
				success:function()
				{
					load_cart_data();
					alert("Item has been removed from Cart");
				}
			})
		}
		else
		{
			return false;
		}
	});

	$(document).on('click', '#clear_cart', function(){
		var action = 'empty';
		$.ajax({
			url:"action.php",
			method:"POST",
			data:{action:action},
			success:function()
			{
				load_cart_data();
				alert("Your Cart has been clear");
			}
		});
	});
    
});

</script>

<?php
session_start();

$total_price = 0;
$total_item = 0;

$output = '
<div class="table-responsive" id="order_table">
 <table class="table table-bordered table-striped">
  <tr>  
            <th width="40%">Product Name</th>  
            <th width="10%">Quantity</th>  
            <th width="20%">Price</th>  
            <th width="15%">Total</th>  
            <th width="5%">Action</th>  
        </tr>
';
if(!empty($_SESSION["myCart"]))
{
 foreach($_SESSION["myCart"] as $keys => $values)
 {
  $output .= '
  <tr>
   <td>'.$values["product"].'</td>
   <td>'.$values["product_quantity"].'</td>
   <td align="right">$ '.$values["price"].'</td>
   <td align="right">$ '.number_format($values["product_quantity"] * $values["price"], 2).'</td>
   <td><button name="delete" class="btn btn-danger btn-xs delete" id="'. $values["pid"].'">Remove</button></td>
  </tr>
  ';
  $total_price = $total_price + ($values["product_quantity"] * $values["price"]);
  //$total_item = $total_item + 1;
 }
 $output .= '
 <tr>  
        <td colspan="3" align="right">Total</td>  
        <td align="right">$ '.number_format($total_price, 2).'</td>  
        <td></td>  
    </tr>
 ';
}
else
{
 $output .= '
    <tr>
     <td colspan="5" align="center">
      Your Cart is Empty!
     </td>
    </tr>
    ';
}
$output .= '</table></div>';

echo $output;
echo '<pre>';
print_r($_SESSION["myCart"]);
echo '</pre>';


?>
