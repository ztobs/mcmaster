<?php
// usage php do_product.php $ProdListUrl $cat_db_id



include('functions.php');
$ProdListUrl = trim($argv[1]);
$cat_db_id = trim($argv[2]);

//error_reporting(E_ALL & ~E_NOTICE);
//error_reporting(0);



$dom1d = getDomSelenium($ProdListUrl, chooseProxy());

$prod = getProdParts($dom1d);
$prod_name = $prod['name'];
$prod_price = $prod['price'];
$prod_desc = $prod['description'];
$prod_image = $prod['image'];
$prod_filters = $prod['filters'];

if(existProd($prod_name))
{	
	insertProdCat($prod_name, $cat_db_id);
	insertProdFilter($prod_name, $prod_filters);
}
else
{
	insertProduct($prod_name, $prod_price, $prod_desc, $cat_db_id, implode(",", $prod_filters), $prod_image);
}