# Acme Widget Co

### Pre requisites
- `git` protocol client installed
- Repository management client like `SourceTree` or something that helps in the `git` workflow
- `php 7.2` should be installed
- `mysql 5.7` should be installed

### Project checkout flow
Clone the entire repository on your local server using the command

git clone https://github.com/aroratarunit/vidalytics.git

Say the above is cloned out in a folder say `vidalytics`.
Execute the following command to go in the checked out project folder

## Migrate the Database

- Create Database 'acme' or anything and import the db.sql.
- Configure the Database credentials in the config/connection.php file.

IMPORTANT
Database and Tables have to be there.

### Run the project 
- Hit the below to run the project on your local server.
/////
http://localhost/vidalytics/ or deploy on any server like https://www.hostname.com .

## Third party library for UI
  - Bootstrap 4.3.1

### Widget module

- File descriptions:-
	- Apis:-
		- api/index.php?action=add_to_cart (POST) - This api used to add product to the cart.
		- api/index.php?action=cart_products (GET) - This api used to get all the cart products with order summary.
		- api/index.php?action=clear_cart (POST) - This api used to clear all the cart products.

	- Product Controller file (classes/product_controller.php) :- It's managing all the requests,queries,offers,discounts.
	Currently Cart Total is coming along with cart products.
	- widget.html:- This file used to manage UI and frontend scripts.
	- widget.js :- This file used to create html element.
	- index.html :- This file just to give an example how to integrate script file.

### Access the widget.
	- Include the widget.js script into your root index.html file.
//////	
<script src="https://www.hostname.com/widget.js"></script>
//////
www.hostname.com can be replaced with any host on which widget.js is hosted.
www.hostname.com should also be replaced in widget.html too.
Please the script in body tag

IMPORTANT
Do not forget to replace the URL in widget.html

Assumption
Cuurently considered that there will be one offer and offer type will be "On purchase of same product in even quantity then second item will be on 50% off", database having a column "is_offer" to maintain this functionality.
Example "buy one red widget,get the second half price"