<?php
//constants
define("SWX_BASE", realpath(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/swx/php') . "/");
define("AMFPHP_BASE", realpath(SWX_BASE . 'core') . "/");
define("CAKESWX_BASE", realpath(dirname(dirname(dirname(dirname(__FILE__))))) . "/");
define("CAKEAMFPHP_BASE", realpath(CAKESWX_BASE . 'core') . "/");
define('LETTERS_AND_NUMBERS_ONLY', '/[a-zA-Z0-9_]/');
//includes
require_once(SWX_BASE . 'swx_config.php');
require_once(SWX_BASE . 'lib/Validate.php');
require_once(AMFPHP_BASE . "shared/app/BasicGateway.php");
require_once(AMFPHP_BASE . "shared/util/MessageBody.php");
require_once(AMFPHP_BASE . "shared/util/functions.php");
require_once(CAKEAMFPHP_BASE . "shared/app/CakeBasicActions.php");
require_once(CAKEAMFPHP_BASE . "swx/app/CakeActions.php");

//class definition
class CakeGateway extends BasicGateway{
	function Gateway(){
		
		if(AMFPHP_PHP5)
		{
			error_log("PHP5");
			//Set gloriously nice error handling
			include_once(AMFPHP_BASE . "shared/app/php5Executive.php");
			include_once(AMFPHP_BASE . "swx/exception/php5Exception.php");
		}
		else
		{
			error_log("PHP4");
			//Cry
			include_once(AMFPHP_BASE . "shared/app/php4Executive.php");
			include_once(AMFPHP_BASE . "swx/exception/php4Exception.php");
		}
		$this->registerActionChain();
	}
	function createBody()
	{
		
		$GLOBALS['amfphp']['encoding'] = 'swx';
		$body = & new MessageBody();

		if (array_key_exists('serviceClass', $_GET))
		{
			// GET
			error_log('[SWX] INFO Using GET.');
			$args = $_GET;
		}
		else
		{
			// POST
			error_log('[SWX] INFO Using POST.');
			$args = $_POST;
		}
		
		// Check the class name
		if (!array_key_exists('serviceClass', $args))
		{
			// Error: Service class argument is missing.
			trigger_error('The \'serviceClass\' argument is missing (no class name was supplied)', E_USER_ERROR);
		}
	
		// Check the method name
		if (!array_key_exists('method', $args))
		{
			// Error: Method argument is missing.
			trigger_error('The \'method\' argument is missing (no method name was supplied)', E_USER_ERROR);
		}
		
		// Debug mode?
		if (array_key_exists('debug', $args))
		{
			$GLOBALS['swx']['debug']  = ($args['debug'] === 'true');
		}
		else
		{
			// If no debug parameter is passed, debug defaults to false.
			$GLOBALS['swx']['debug']  = false;
		}
		
		// TODO: Implement as part of the new security 
		// model in the next Beta. 
		//
		// Get the url that we are being called from 
		// (for cross-domain support)
		if (array_key_exists('url', $args))
		{
			$GLOBALS['swx']['url'] = urldecode($args['url']);
			
			// Firefox/Flash (at least, and tested only on a Mac), sends 
			// file:/// (three slashses) in the URI and that fails the validation
			// so replacing that with two slashes instead.
			$GLOBALS['swx']['url'] = str_replace('///', '//', $GLOBALS['swx']['url']);
			
			if (LOG_ALL) error_log('[SWX] INFO: SWX gateway called from '.$GLOBALS['swx']['url']);
		}
		else
		{
			error_log('[SWX] Warning: No referring URL received from Flash. Cross-domain will not be supported on this call regardless of allowDomain setting.');
		}
		
		// Security: Check that only allowed characters are present in the URL.
		$v = new Validate();
	
		error_log('Validating: '.$GLOBALS['swx']['url']);
		$urlValid = $v->uri($GLOBALS['swx']['url']);
		
		if ($urlValid != 1)
		{
			error_log('[SWX] Non-fatal error: URL is not valid. Cross-domain access will not work. ' . $GLOBALS['swx']['url']);
		}
		else
		{
			// URL is valid
			if (LOG_ALL) error_log('[SWX] INFO: The referring URL is valid.');
		}

		$body->setValue($args);
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