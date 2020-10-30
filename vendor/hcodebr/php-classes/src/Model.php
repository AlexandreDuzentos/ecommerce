<?php 

namespace Hcode;

class Model {

	private $values = [];

	public function __call($name, $args)
	{

		$method = substr($name, 0, 3);
		$fieldName = substr($name, 3, strlen($name));

		switch ($method)
		{

			case "get":
				return isset($this->values[$fieldName]) ? $this->values[$fieldName] : NULL;
			break;

			case "set":
				$this->values[$fieldName] = isset($args[0]) ? $args[0] : NULL ; //OS valores passados como paramêtros nos setters são passados como argumento nesse metodo
			break;

		}

	}

	public function setData($data = array())
	{

		foreach ($data as $key => $value) {

			$this->{"set".$key}($value);

		}

	}


	public function getValues()
	{

		return $this->values;

	}

}

 ?> 