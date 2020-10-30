<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class User extends Model {

	const SESSION = "User";
	const SECRET = "Hcodephp7_Secret";
  const SECRET2 = "Hcodephp7_Secret2"; 
  
    /*
2 - O método checkLogin() é usado junto com o verifyLogin(). Ele tem o objetivo de realizar a verificação se o usuário está logado por analisar suas informações e conferir se estão na sessão corrente
  */

  //verifica se há sessão e também seta e retorna os dados do usuário



  public static function getFromSession()
  {
      $user = new User(); 

      if(isset($_SESSION[User::SESSION]) && (int)$_SESSION[User::SESSION]['iduser'] > 0)  {

        $user->setData($_SESSION[User::SESSION]);
       
      }

      return $user;
  }

  public static function checkLogin($inadmin = true)
  {
     if(
      !isset($_SESSION[User::SESSION])
      ||
      !$_SESSION[User::SESSION]
      ||
      !(int)$_SESSION[User::SESSION]["iduser"] > 0
     ){
      //Não logado
       return false;

     }else{

       if ($inadmin === true && (bool)$_SESSION[User::SESSION]['inadmin'] === true){

            return true;

     }else if($inadmin === false){

          return true; 
     }
  }
}
  
	public static function login($login, $password)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
			":LOGIN"=>$login

		)); 

		if (count($results) === 0)
		{
			throw new \Exception("Usuário inexistente ou senha inválida");
		}

		$data = $results[0];

		if (password_verify($password, $data["despassword"]) === true)
		{

			$user = new User();

			$user->setData($data);

			$_SESSION[User::SESSION] = $user->getValues();

		    return $user;

		} else {
			
      throw new \Exception("Usuário inexistente ou senha inválida");
		}

	}

	public static function verifyLogin($inadmin = true)
	{

		if(
      !isset($_SESSION[User::SESSION])
      ||
      !$_SESSION[User::SESSION]
      ||
      !(int)$_SESSION[User::SESSION]["iduser"] > 0
      ||
      (bool)$_SESSION[User::SESSION]['inadmin'] !== $inadmin
    ){
			header("Location:/admin/login");
			exit;

		}

	}

	public static function logout()
	{
    $_SESSION[User::SESSION] = NULL;
    //session_destroy();
  }

   //Lista todos os usuários do banco de dados na tela users
	public static function listAll()
	{

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");
	}

    //salva novos usuários no banco de dados
	public function save()
	{
       $sql = new Sql();

       $results = $sql->select("CALL sp_users_save(:desperson,:deslogin,:despassword,:desemail,:nrphone,:inadmin)",array(

         ":desperson"=>$this->getdesperson(),
         ":deslogin"=>$this->getdeslogin(),
         ":despassword"=>$this->getdespassword(),
         ":desemail"=>$this->getdesemail(),
         ":nrphone"=>$this->getnrphone(),
         ":inadmin"=>$this->getinadmin(), 
       ));

       if(count($results) > 0){

       	$this->setData($results[0]); //configura os dados do usuário na variável $values da classe Model
       }
	}

  //seleciona do banco de dados o registro passado como parâmetro
	public function get($iduser)
	{ 

     $sql = new Sql();

     $results = $sql->select("SELECT* FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser",array(

     	":iduser"=>$iduser, 
     ));

     if(count($results) > 0){
     	$this->setData($results[0]);//Configura os dados do usuário nos variável $values no case : set da classe model
     }

	}

  //Atualiza usuários no banco de dados
	public function update()
	{
       $sql = new Sql();

       $results = $sql->select("CALL sp_usersupdate_save(:iduser,:desperson,:deslogin,:despassword,:desemail,:nrphone,:inadmin)",array(

         ":iduser"=>$this->getiduser(),
         ":desperson"=>$this->getdesperson(),
         ":deslogin"=>$this->getdeslogin(),
         ":despassword"=>$this->getdespassword(),
         ":desemail"=>$this->getdesemail(),
         ":nrphone"=>$this->getnrphone(),
         ":inadmin"=>$this->getinadmin(), 
       ));

       if(count($results) > 0){
       	$this->setData($results[0]); //Configura os dados do usuário nos variável $values da classe model

       }
  
	}
  
  //Apaga dados do banco de dados
	public function delete()
	{
     $sql = new Sql();

     $sql->query("CALL sp_users_delete(:iduser)",array(
     	":iduser"=>$this->getiduser(),
      
     ));
	}
    
    //Recuperação de password
	public static function getForgot($email)
	{
        
        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_persons INNER JOIN tb_users  WHERE desemail = :email;",array(

        	":email"=>$email,
        ));
         if(count($results) === 0)
         {
            throw new \Exception("Não foi possível recuperar a senha");
         }
         else
         {
          $data = $results[0];

          $results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser,:desip)",array(
             ":iduser" => $data["iduser"],
             ":desip" => $_SERVER["REMOTE_ADDR"]

          ));

          if(count($results2) === 0)
          {
             throw new \Exception("Não foi possível recuperar a senha");
          }
          else
          {
            $dataRecovery = $results2[0];

           $code = base64_encode(openssl_encrypt($dataRecovery["idrecovery"], "AES-256-CBC", User::SECRET,0,User::SECRET));

           $link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$code";

           $mailer = new Mailer($data["desemail"],$data["deslogin"],"Redefinir senha da Hcode Store","forgot",array(
              "name"=>$data["desperson"],
               "link"=>$link
           ));

           $mailer->send();

           return $data;

          }


         }
     
           
   }

   public static function validForgotDecrypt($code){

    $idrecovery = openssl_decrypt(base64_decode($code), "AES-256-CBC", User::SECRET,0,User::SECRET);

    $sql = new Sql();

   $results = $sql->select("SELECT * FROM tb_userspasswordsrecoveries  a INNER JOIN tb_users b USING(iduser) INNER JOIN tb_persons c USING(idperson) WHERE a.idrecovery = :idrecovery AND a.dtrecovery IS NULL AND DATE_ADD(a.dtregister,INTERVAL 1 HOUR) >= NOW()",array(

       ":idrecovery" => $idrecovery,

    ));

    if(count($results) === 0)

    {
     throw new \Exception("Não foi possível recuperar a senha");
     
    }
    else
    {

       return $results[0];
    }

   }


   public static function setForgotUsed($idrecovery)
   {
     $sql = new Sql();

     $sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery =  NOW() WHERE idrecovery = :idrecovery",array(
      
       "idrecovery" => $idrecovery,

     ));
   }

   public function setPassword($password)
   {
     
     $sql = new Sql();
     
     $sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser",array(

      ":password" => $password,
      ":iduser" => $this->getiduser(),
     ));

   }

}

?>