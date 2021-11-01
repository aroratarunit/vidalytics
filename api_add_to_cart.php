<?php 
include('api_controller.php');
$product = new ApiProduct;
echo $product->addTocart();
?>