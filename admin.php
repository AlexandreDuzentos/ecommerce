<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;

//Rota para o admin
$app->get('/admin',function(){

    User::verifyLogin();

    $page = new PageAdmin();

    $page->setTpl("index");      

});

//Rota para o Login do admin
$app->get('/admin/login',function(){
   
   $page = new PageAdmin([
   	  "header"=>false,
   	  "footer"=>false,
   ]);

   $page->setTpl("login");

});

//Rota para validar o Login
$app->post('/admin/login', function(){
 
      User::login($_POST["login"],$_POST["password"]);

      header("Location:/admin");
      exit;
});

//Rota para terminar a sessão
$app->get('/admin/logout',function(){
    
    User::logout();

    header("Location:/admin/login");
    exit;

});

$app->post("/admin/forgot",function(){
  
  $user = User::getForgot($_POST["email"]);

  header("Location:/admin/forgot/sent");
  exit;
  
});

$app->get("/admin/forgot/sent",function(){
   
    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false,
  ]);

  $page->setTpl("forgot-sent");


});

$app->get("/admin/forgot/reset",function(){
 
  $user = user::validForgotDecrypt($_GET["code"]);// consulta os dados do usuário fazendo uma junçao entre as tabelas tb_users.tb_persons,tb_recoveriespasswords e retorna os dados)

  $page = new pageAdmin([
      "header"=>false,
      "footer"=>false
  ]);

  $page->setTpl("forgot-reset",array(
    "name" => $user["desperson"],
    "code" => $_GET["code"]
  ));

});

$app->post("/admin/forgot/reset",function(){

 $forgot = User::validForgotDecrypt($_POST["code"]); // consulta os dados do usuário fazendo uma junçao entre as tabelas tb_users.tb_persons,tb_recoveriespasswords e retorna os dados)

  User::setForgotUsed($forgot["idrecovery"]); //define da data de recuperação da senha para agora onde o a data de recuperação é igual a $forgot["idrecovery"]

  $user = new User();

  $user->get((int) $forgot["iduser"]);

  $_POST["password"] = password_hash($_POST["password"],PASSWORD_DEFAULT,array(
    "cost"=>12, //o quão de processamento que o servidor utilizara
  ));

  $user->setPassword($_POST["password"]);

  header("Location:/admin/forgot/reset/success");
  exit;

  });


$app->get("/admin/forgot/reset/success",function(){

     $page = new PageAdmin([
       "header"=>false,
       "footer"=>false,
     ]);

     $page->setTpl("forgot-reset-success");
});

?>