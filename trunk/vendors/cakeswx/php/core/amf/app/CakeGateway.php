<?php
/**
 * The Gateway class is the main facade for the AMFPHP remoting service.
 * 
 * The developer will instantiate a new gateway instance and will interface with
 * the gateway instance to control how the gateway processes request, securing the
 * gateway with instance names and turning on additional functionality for the gateway
 * instance.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright (c) 2003 amfphp.org
 * @package flashservices
 * @subpackage app
 * @author Musicman  original design 
 * @author Justin Watkins  Gateway architecture, class structure, datatype io additions 
 * @author John Cowen  Datatype io additions, class structure, 
 * @author Klaasjan Tukker Modifications, check routines, and register-framework 
 * @version $Id: CakeGateway.php 63 2006-12-20 03:54:05Z gwoo $
 */

/**
 * AMFPHP_BASE is the location of the flashservices folder in the files system.  
 * It is used as the absolute path to load all other required system classes.
 */

App::import('Vendor', 'gateway', array('file' => 'swx'.DS.'php'.DS.'core'.DS.'amf'.DS.'app'.DS.'Gateway.php'));
App::import('Vendor', 'cake_basic_actions', array('file' => 'cakeswx'.DS.'php'.DS.'core'.DS.'shared'.DS.'app'.DS.'CakeBasicActions.php'));
App::import('Vendor', 'cake_actions', array('file' => 'cakeswx'.DS.'php'.DS.'core'.DS.'amf'.DS.'app'.DS.'CakeActions.php'));

define("SERVICE_BASE", realpath(dirname(dirname(dirname(dirname(__FILE__))))) . "/");

/**
 * required classes for the application
 */

class CakeGateway extends Gateway {

	var $actions;	

	/**
	 * Create the chain of actions
	 * Subclass gateway and overwrite to create a custom gateway
	 */
	function registerActionChain()
	{
		
		$this->actions['adapter'] = 'CakeAdapterAction';
		$this->actions['class'] = 'CakeClassLoaderAction';
		$this->actions['security'] = 'securityAction';
		$this->actions['exec'] = 'CakeExecutionAction';
	}
	
}
?>