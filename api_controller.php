<?php 
include('connection.php');
Database::initialize();

class ApiProduct 
{
	private $conn;

	function __construct() {
    	$this->conn = Database::$conn;
  	}

  	/* Get all the added products into the cart acording to visitor_id with applied offer and delivery charge */
	public function getCartProducts(){

	    $response['status'] =200;
		$response['message'] ='Success';
		$response['result'] =array();
		
		$visitor_id = $_REQUEST['visitor_id'];
		$all_carts = array();
		$total_cost = 0; 
		$all_carts['delivery_charge'] = 0;
		$all_carts['sub_total'] = 0;
		$all_carts['discount'] = 0;
		$all_carts['total'] = 0;
		$all_carts['products'] = array();

		$offer_products = array();
		$offer_products_price = array();

		if(!empty($visitor_id)){
			$sql = "SELECT `cart`.`id`, `cart`.`product_id`,`cart`.`quantity`,`products`.`name`,`products`.`price`,`products`.`is_offer`,`products`.`code` FROM cart JOIN products ON `products`.`id` = `cart`.`product_id`  WHERE visitor_id = '".$visitor_id."'";

			$result = $this->conn->query($sql);
			if ($result->num_rows > 0) {
				$key = 0;
				while($row = $result->fetch_assoc()){
					$all_carts['products'][$key] = $row;
					$total_cost = $total_cost+ ($row['price'] * $row['quantity']);
					if($row['is_offer']){
						$offer_products[$row['product_id']] = isset($offer_products[$row['product_id']]) ? ($offer_products[$row['product_id']] + $row['quantity']) :  $row['quantity'];
						$offer_products_price[$row['product_id']] = $row['price'];
					}
					$key++;
				}

				$all_carts['discount'] = $this->applyOffer($offer_products,$offer_products_price);
				$all_carts['delivery_charge'] = $this->calculateDeliveryCharge($total_cost-$all_carts['discount']);
				$all_carts['sub_total'] = round($total_cost,2);
				
				$all_carts['total'] = $total_cost+$all_carts['delivery_charge']-$all_carts['discount'];
				$all_carts['total'] = round($all_carts['total'],2);
			}
			$this->conn->close();
			$response['result'] = $all_carts;
		}else{
			$response['status'] =400;
			$response['message'] ='Visitor Id is required!';
		}

		return json_encode($response);
	}

	/* Validate product and add to cart */
	public function addTocart(){
		$response['status'] =200;
		$response['message'] ='Successfully product added to the cart.';
		
		$code = $_REQUEST['code'];
		$visitor_id = $_REQUEST['visitor_id'];
		$quantity = $_REQUEST['quantity'] ? $_REQUEST['quantity'] : 1;
		if(!empty($visitor_id)){
			$valid_product= $this->checkProductCode($code);

			if($valid_product){
				$cart_product = $this->checkProductInCart($valid_product,$visitor_id);

				if(!empty($cart_product)){
					$this->updateCart($cart_product,$quantity);
				}else{
					
					$this->finalAddtoCart($valid_product,$visitor_id,$quantity);
				}
			}else{
				$response['status'] =400;
				$response['message'] ='Invalid product code!';
			}
		}else{
			$response['status'] =400;
			$response['message'] ='Visitor Id is required!';
		}
		return json_encode($response);

	}

	/* Clear all the cart products deleted for the cart table */
	public function clearCart(){
		$visitor_id = $_REQUEST['visitor_id'];
		$response['status'] =200;
		$response['message'] ='Successfully clear all the cart products';
		if(!empty($visitor_id)){
			// sql to delete a record
			$sql = "DELETE FROM cart WHERE visitor_id='".$visitor_id."'";
			$this->conn->query($sql);
		}else{
			$response['status'] =400;
			$response['message'] ='Visitor Id is required!';
		}
		return json_encode($response);	
	}

	/* Check product is valid */
	private function checkProductCode($code){
		if(!empty($code)){
			$sql = "SELECT * FROM `products` WHERE `code` = '$code'";
			$result = $this->conn->query($sql);
			if ($result->num_rows > 0) {
				return $result->fetch_assoc();
			} else {
			  return null;
			}
		}else{
			null;
		}
	}

	/* Check product in cart */
	private function checkProductInCart($product,$visitor_id){
		if(!empty($product) && !empty($visitor_id)){
			$product_id =$product['id'];
			$sql = "SELECT * FROM `cart` WHERE `visitor_id` = '".$visitor_id."' AND `product_id` = ".$product_id."";
			$result = $this->conn->query($sql);
			if ($result->num_rows > 0) {
				return $result->fetch_assoc();
			} else {
			  return null;
			}
		}else{
			null;
		}
	}

	/* Insert product into the cart table */
	private function finalAddtoCart($product,$visitor_id,$quantity){
		$date_time = date("Y-m-d h:i:s");
		$product_id = $product['id'];
		$sql = "INSERT INTO cart (product_id,created_at,visitor_id,quantity)
		VALUES (".$product_id.",'".$date_time."','".$visitor_id."',".$quantity.")";
		
		if ($this->conn->query($sql) === TRUE) {
		 return true;
		} else {
		  return false;
		}
	}

	/* update product into the cart table */
	private function updateCart($cart,$quantity){
		$quantity = $quantity + $cart['quantity'];
		$id = $cart['id'];
		$sql = "UPDATE cart SET quantity=".$quantity." WHERE id=".$id."";
		if ($this->conn->query($sql) === TRUE) {
		   return true;
		} else {
		  return false;
		}
		
	}

	/* Calucate Delivery Charge according to the total cost 
	Note:- To incentivise customers to spend more, delivery costs are reduced based on the amount
		spent. Orders under $50 cost $4.95. For orders under $90, delivery costs $2.95. Orders of
		$90 or more have free delivery.
		*/
	private function calculateDeliveryCharge($cost){
		switch ($cost) {
			case ($cost >= 90):
				return 0;
				break;

			case ($cost >= 50 && $cost < 90):
				return 2.95;
				break;
			
			default:
				return 4.95;
				break;
		}
	}

	/* Allpy offer for offer product 
	Note:- They are also experimenting with special offers. The initial offer will be “buy one red widget,
	get the second half price”.
	*/
	private function applyOffer($offer_products,$offer_products_price){
		$total_discount = 0;
		foreach ($offer_products as $key => $value) {
			$total_no_of_project = floor($value/2);
			$total_discount = $total_discount + (($offer_products_price[$key]/2)*$total_no_of_project);
		}
		return round($total_discount,2);
	}


	function getClientIp() {
	    $ipaddress = '';
	    if (isset($_SERVER['HTTP_CLIENT_IP']))
	        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED']))
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
	        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_FORWARDED']))
	        $ipaddress = $_SERVER['HTTP_FORWARDED'];
	    else if(isset($_SERVER['REMOTE_ADDR']))
	        $ipaddress = $_SERVER['REMOTE_ADDR'];
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	}
}
?>