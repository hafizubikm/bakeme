<?php

App::import('Vendor', 'gateway', array('file' => 'swx'.DS.'php'.DS.'core'.DS.'json'.DS.'app'.DS.'Gateway.php'));
App::import('Vendor', 'cake_basic_actions', array('file' => 'cakeswx'.DS.'php'.DS.'core'.DS.'shared'.DS.'app'.DS.'CakeBasicActions.php'));
App::import('Vendor', 'cake_actions', array('file' => 'cakeswx'.DS.'php'.DS.'core'.DS.'json'.DS.'app'.DS.'CakeActions.php'));

class CakeGateway extends Gateway{
	function createBody()
	{
		$GLOBALS['amfphp']['encoding'] = 'json';
		$body = & new MessageBody();

		$uri = __setUri();
		$elements = explode('/json.php', $uri);

		if(strlen($elements[1]) == 0)
		{
			echo("The JSON gateway is installed correctly. Call like this: json.php/MyClazz.myMethod/arg1/arg2");
			exit();
		}
		$args = substr($elements[1], 1);
		$rawArgs = explode('/', $args);

		if(isset($GLOBALS['HTTP_RAW_POST_DATA']))
		{
			$rawArgs[] = $GLOBALS['HTTP_RAW_POST_DATA'];
		}

		$body->setValue($rawArgs);
		return $body;
	}

	/**
	 * Create the chain of actions
	 */
	function registerActionChain()
	{
		$this->actions['deserialization'] = 'CakeDeserializationAction';
		$this->actions['classLoader'] = 'CakeClassLoaderAction';
		$this->actions['security'] = 'securityAction';
		$this->actions['exec'] = 'executionAction';
		$this->actions['serialization'] = 'serializationAction';
	}
}
?>