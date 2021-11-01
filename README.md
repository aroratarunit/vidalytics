# Acme Widget Co

### Pre requisites
- `git` protocol client installed
- Repository management client like `SourceTree` or something that helps in the `git` workflow
- `php 7.2` should be installed
- `mysql 5.7` should be installed

### Project checkout flow
Clone the entire repository on your local server using the command

git clone https://github.com/dashrathdots/acme.git

Say the above is cloned out in a folder say `acme`.
Execute the following command to go in the checked out project folder

## Migrate the Database

- Create Database 'acme' and import the acme.sql
- Configure the Database credentials in the connection.php file.


### Run the project 
- Hit the below to run the project on your local server.
```
http://localhost/acme/
```


## Third party library for UI
  - Bootstrap 4.3.1


### Widget module

- Checkout into the widget branch.
``` 
	git checkout widget 
```
- File descriptions:-
	- Apis:-
		- api_add_to_cart.php (POST) - This api used to add product to the cart.
		- api_cart_products.php (GET) - This api used to get all the cart products with order summary.
		- api_clear_all_cart.php (POST) - This api used to clear all the cart products.

	- Used Controller file (api_controller.php) :- It's managing all the requests,queries,offers,discounts.
	- widget.html:- This file used to manage UI and frontend scripts.
	- widget.js :- This file used to create html element.

### Access the widget.
	- Include the widget.js script into your root index.html file.
```
<script src="http://host.com/acme/widget.js"></script>
```




 