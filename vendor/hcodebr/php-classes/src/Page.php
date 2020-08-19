<?php

 namespace Hcode;

 use Rain\Tpl;

 class Page {

 	private $tpl;
 	private $options;
 	private $defaults = [
         "data"=>[],
 	];

//Cabeçario do ecommerce
   public function __construct($opts = array())
   {
       
       $this->options = array_merge($this->defaults,$opts);

       
       $config = array(
		"tpl_dir"       =>$_SERVER["DOCUMENT_ROOT"]."/views/",
		"cache_dir"     =>$_SERVER["DOCUMENT_ROOT"]."/views-cache/",
		"debug"         => false // set to false to improve the speed

		   );

	 	Tpl::configure( $config );

	 	$this->setData($this->options["data"]);

	 	$this->tpl = new Tpl();

	 	$this->tpl->draw("header");
   }
   //Final do Cabeçario

     
     //Metodo auxiliar para as outras classes
    private function setData($data = array())
    {

        foreach ($data as $key => $value) {

        	$this->tpl->assign($key,$value);
        }
    }

    //Corpo do ecommerce
   public function setTpl($name,$data = array(),$returnHTML = false)
   {

     $this->setData($data);

     $this->tpl->draw($name,$returnHTML);

   }
   //Final do corpo


    //Footer do ecommerce
   public function __destruct(){

     $this->tpl->draw("footer");
     
   }
   //Final do footer

 }

?>