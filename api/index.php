<?php 
include('../classes/product_controller.php');
$product = new ProductController;
$action = isset($_REQUEST["action"])?$_REQUEST["action"]:"";
switch($action){
	case "cart_products":
		echo $product->getCartProducts();
		break;
	case "add_to_cart":
		echo $product->addToCart();
		break;
	case "clear_cart":
		echo $product->clearCart();
		break;
	default:
		echo $product->getCartProducts();
}
?>