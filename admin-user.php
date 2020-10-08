<?php

use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;

//Rota para listar usuários
$app->get("/admin/users",function(){

   User::verifyLogin();

   $user = User::listAll(); //consulta dados no banco de dados para passar para o template

   $page = new PageAdmin();

   $page->setTpl("users",array(
   	"users"=>$user,

   ));

});

//Rota para criar usuários
$app->get("/admin/users/create",function(){
    
   User::verifyLogin();

   $page = new PageAdmin();

   $page->setTpl("users-create");

});

//Rota para deletar usuários
$app->get("/admin/users/:iduser/delete",function($iduser){

  User::verifyLogin();

  $user = new User();

  $user->get((int)$iduser); //seleciona do banco o id do usuário passado como paramêtro(para ter a certeza que o usuário ainda existe no banco)

  $user->delete(); //apaga um usuário do banco

  header("Location:/admin/users");
  exit;
  


});

//Rota para editar usuários
 $app->get('/admin/users/:iduser', function($iduser){
 
   User::verifyLogin();
 
   $user = new User();

    $user->get((int)$iduser);
 
   $page = new PageAdmin();
 
   $page ->setTpl("users-update",array(
      "user"=>$user->getValues(),
   ));   
 
});



//Rota para salvar a criação
$app->post("/admin/users/create", function(){

	  User::verifyLogin();

	  //var_dump($_POST); //os dados do usuários estão vindo via post

      $user = new User();

      $_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

      $_POST["despassword"] = password_hash($_POST["despassword"],PASSWORD_DEFAULT,array(
        "cost" => 12,
      ));

      $user->setData($_POST); //configura os dados do usuário nos getters do save

      $user->save();

      header("Location:/admin/users");
      exit;


	  
});

//Rota para salvar a edição
$app->post("/admin/users/:iduser", function($iduser){

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	//$_POST["despassword"] = password_hash($_POST["despassword"],PASSWORD_DEFAULT);

	$user->get((int)$iduser); //metodo para salvar a edição de um usuário em especifico

  //var_dump($user->getValues()); //traz os dados do iduser passado como paramêtro

	$user->setData($_POST); //configura os dados nos getters do update

	$user->update();

	header("Location:/admin/users");
	exit;

});

$app->get("/admin/forgot",function(){

  $page = new PageAdmin([
        "header"=>false,
        "footer"=>false,
  ]);

  $page->setTpl("forgot");

});
?>