<?php

use \Hcode\Page;
use \Hcode\Model\Product;
use \Hcode\Model\User;
use \Hcode\Model\Category;
//Rota para a home page
$app->get('/', function() {

	$products = Product::listAll();
    
	$page = new Page();

	$page->setTpl("index",array(
       'products'=>Product::checkList($products),

	));

});

$app->get('/categories/:idcategory',function($idcategory){

  $page = (isset($_GET['page'])) ? $_GET['page'] : 1;

  User::verifyLogin();

  $category = new Category();

  $pagination = $category->getProductsPage($page);

  $category->get((int)$idcategory);

  $pages = array();

  for($i=0 ; $i <= $pagination['pages']; $i++){
  	 array_push($pages,array(
  	 	'link'=>"/categories/".$category->getidcategory()."?page=".$i,
  	 	'page'=>$i,
  	 ));
  }

  $page = new Page();

  $page->setTpl("category",array(
    'category'=>$category->getValues(),
    'products'=>$pagination['data'],
    'pages'=>$pages,

  ));
 
});

$app->get("/products/:desurl",function($desurl){

   $product = new Product();

   $product->getFromURL($desurl);
  
   $page = new Page();

   $page->setTpl("product-detail",array(
       'product'=>$product->getValues(),
       'categories'=>$product->getCategories()
   ));

})

?>