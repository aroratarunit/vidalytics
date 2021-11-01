<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include('api_controller.php');
$product = new ApiProduct;
echo $product->getCartProducts();
?>