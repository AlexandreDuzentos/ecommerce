<?php

 namespace Hcode;

 use Rain\Tpl;

 class Page {

 	private $tpl;
 	private $options;
 	private $defaults = [
         "data"=>[],
         "header"=>true,
         "footer"=>true,
 	];

//Cabeçario do ecommerce
   public function __construct($opts = array(),$tpl_dir = "/views/")
   {
       
       $this->options = array_merge($this->defaults,$opts);

       
       $config = array(
		"tpl_dir"       =>$_SERVER["DOCUMENT_ROOT"].$tpl_dir,
		"cache_dir"     =>$_SERVER["DOCUMENT_ROOT"]."/views-cache/",
		"debug"         => false // set to false to improve the speed

		   );

	 	Tpl::configure( $config );

	 	$this->setData($this->options["data"]);

	 	$this->tpl = new Tpl();

	 	if($this->options[ "header"] === true)$this->tpl->draw("header");
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

    if($this->options["footer"] === true) $this->tpl->draw("footer");
     
   }
   //Final do footer

 }

?>