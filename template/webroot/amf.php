<?php
/*
 * amfphp gateway for cake controllers
 * Created By Wouter Verweirder
 * Based on the work of Patrick Mineault (amfphp), gwoo (cakephp) & Aral Balkan (swx)
 */
require_once('cake_gateway_init.php');

App::import('Vendor', 'cake_gateway', array('file' => 'cakeswx'.DS.'php'.DS.'core'.DS.'amf'.DS.'app'.DS.'CakeGateway.php'));
App::import('Controller', 'App');
//You can set this constant appropriately to disable traces and debugging headers
//You will also have the constant available in your classes, for changing
//the mysql server info for example
define("PRODUCTION_SERVER", false);

//Include things that need to be global, for integrating with other frameworks
//include "globals.php";

//Include framework
//include "core/amf/app/Gateway.php";

$gateway = new CakeGateway();

//Set where the services classes are loaded from, *with trailing slash*
$gateway->setClassPath(CONTROLLERS);

//Set where class mappings are loaded from (ie: for VOs)
//$voPath defined in globals.php
//$gateway->setClassMappingsPath($voPath);

//Read above large note for explanation of charset handling
//The main contributor (Patrick Mineault) is French,
//so don't be afraid if he forgot to turn off iconv by default!
$gateway->setCharsetHandler("utf8_decode", "ISO-8859-1", "ISO-8859-1");

//Error types that will be rooted to the NetConnection debugger
$gateway->setErrorHandling(E_ALL ^ E_NOTICE);

if(PRODUCTION_SERVER)
{
	//Disable profiling, remote tracing, and service browser
	$gateway->disableDebug();
}

//If you are running into low-level issues with corrupt messages and
//the like, you can add $gateway->logIncomingMessages('path/to/incoming/messages/');
//and $gateway->logOutgoingMessages('path/to/outgoing/messages/'); here
//$gateway->logIncomingMessages('in/');
//$gateway->logOutgoingMessages('out/');

//Explicitly disable the native extension if it is installed
//$gateway->disableNativeExtension();

//Enable gzip compression of output if zlib is available,
//beyond a certain byte size threshold
$gateway->enableGzipCompression(25*1024);

//Service now
$gateway->service();

?>