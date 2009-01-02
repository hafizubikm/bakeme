<?php
function CakeClassLoaderAction (&$amfbody) {
	if(!$amfbody->noExec)
	{ 
		// change to the gateway.php script directory
		// now change to the directory of the classpath.  Possible relative to gateway.php
		$dirname = dirname($amfbody->classPath);
		if(is_dir($dirname))
		{
			chdir($dirname);
		}
		else
		{
			$ex = new MessageException(E_USER_ERROR, "The classpath folder {" . $amfbody->classPath . "} does not exist. You probably misplaced your service." , __FILE__, __LINE__, "AMFPHP_CLASSPATH_NOT_FOUND");
			MessageException::throwException($amfbody, $ex);
			return false;
		}
	   
		$fileExists = @file_exists(basename($amfbody->classPath)); // see if the file exists
		if(!$fileExists)
		{
				$ex = new MessageException(E_USER_ERROR, "The class {" . $amfbody->className . "} could not be found under the class path {" . $amfbody->classPath . "}" , __FILE__, __LINE__, "AMFPHP_FILE_NOT_FOUND");
				MessageException::throwException($amfbody, $ex);
				return false;
		}
				
		$fileIncluded = Executive::includeClass($amfbody, "./" . basename($amfbody->classPath));
	
		if (!$fileIncluded) 
		{ 
			$ex = new MessageException(E_USER_ERROR, "The class file {" . $amfbody->className . "} exists but could not be included. The file may have syntax errors, or includes at the top of the file cannot be resolved.", __FILE__, __LINE__, "AMFPHP_FILE_NOT_INCLUDED");
			MessageException::throwException($amfbody, $ex);
			return false;
		}
		
		if (!class_exists($amfbody->className))
		{ // Just make sure the class name is the same as the file name
				
				$ex = new MessageException(E_USER_ERROR, "The file {" . $amfbody->className . ".php} exists and was included correctly but a class by that name could not be found in that file. Perhaps the class is misnamed.", __FILE__, __LINE__, "AMFPHP_CLASS_NOT_FOUND");
				MessageException::throwException($amfbody, $ex);
				return false;
		}

		//Let executive handle building the class
		//The executive can handle making exceptions and all that, that's why
		$classConstruct = Executive::buildClass($amfbody, $amfbody->className);

		if($classConstruct !== '__amfphp_error')
		{
			$amfbody->setClassConstruct($classConstruct);
			setupCakeController($classConstruct);
		}
		else
		{
			return false;
		}
	}
	return true;
}
/**
 * Setup the Cake Controller
 */
function setupCakeController(&$controller) {
	if(!isset($_SERVER["DOCUMENT_ROOT"])) $_SERVER["DOCUMENT_ROOT"]=substr($_SERVER['SCRIPT_FILENAME'] , 0 , -strlen($_SERVER['PHP_SELF']));
	$docrootlen = strlen($_SERVER['DOCUMENT_ROOT']);
	if(substr($_SERVER['DOCUMENT_ROOT'], -1) == '/') $docrootlen--;
	$baseUrl = substr(dirname(WWW_ROOT), $docrootlen);
	
	$controller->base = $baseUrl;
	$controller->here = $_SERVER['PHP_SELF'];
	$controller->webroot = $baseUrl . '/';
	
	$controller->autoRender = false;
	$controller->constructClasses();
	$controller->Component->init($controller);
}
?>