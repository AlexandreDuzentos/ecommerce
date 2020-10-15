<?php

namespace Hcode\Model;
use \Hcode\DB\sql;
use \Hcode\Model;
use \Hcode\Mailer;
use \Hcode\Model\User;

class Cart extends Model {
    
   const SESSION = "Cart";   


/*
     1 - O método getFromSession() tem o objetivo de carregar os dados do carrinho. Ele verifica se já um registro de um carrinho na sessão. Se não possuir ele cria para nós e nos retorna esse novo valor. Esse método é muito importante
     */
    public static function getFromSession()
    {
       
    	$cart = new Cart();

       if(isset($_SESSION[Cart::SESSION]) && (int)$_SESSION[Cart::SESSION]['idcart'] > 0) {

           $cart->get((int)$_SESSION[Cart::SESSION]['idcart']); 

       } else {

       	  $cart->getFromSessionID();

       	  if(!(int)$cart->getiduser() > 0){
             
             $data = [
              'dessesionid'=>session_id(),
             ];

             if(User::checkLogin(false)) {

              $user = User::getFromSession();

              $data['iduser'] = $user->getiduser();

             }


             $cart->setData($data);

             $cart->save();

             $cart->setToSession();
            
            
             
       	  }

       }

       return $cart;
    }

    public function setToSession()
    {
    	$_SESSION[Cart::SESSION] = $this->getValues();
    }
    
    public function getFromSessionID()
    {
       $sql = new Sql();

     $results =  $sql->select("SELECT * FROM tb_carts WHERE dessessionid = :dessessionid",array(
       	   ':dessessionid'=> session_id(),
       ));
     if(count($results) > 0){
     	$this->setData($results);
     }
       
    }

    public function get(int $idcart)
    {
       
       $sql = new Sql();

      $results =  $sql->select("SELECT * FROM tb_carts WHERE idcart = :idcart",array(
       	    ":idcart"=>$idcart,
       ));
      if(count($results)>0){
      	 $this->setData($results);
      } 
    }

public function save(){

   $sql = new Sql();

   $results = $sql->select("CALL sp_carts_save(:idcart,:dessessionid,:iduser,:deszipcode,:pvlfreight,:nrdays)",array(
     ':idcart'=>$this->getidcart(),
     ':dessessionid'=>$this->getdessessionid(),
     ':iduser'=>$this->getiduser(),
     ':deszipcode'=>$this->getdeszipcode(),
     ':pvlfreight'=>$this->getpvlfreight(),
     ':nrdays'=>$this->getnrdays(),
   ));
   if(count($results) > 0){
   	$this->setData($results[0]);
   }
   
}
}


?>