<?php
function deserializationAction(&$body)
{
	$args = $body->getValue();

	$baseClassPath = $GLOBALS['amfphp']['classPath'];

	$trunced = $args['serviceClass'];
	$lpos = strrpos($trunced, ".");
	if ($lpos === false) {
		$classname = $trunced;
		$uriclasspath = $trunced . ".php";
		$classpath = $baseClassPath . $trunced . ".php";
	} else {
		$classname = substr($trunced, $lpos + 1);
		$classpath = $baseClassPath . str_replace(".", "/", $trunced) . ".php"; // removed to strip the basecp out of the equation here
		$uriclasspath = str_replace(".", "/", $trunced) . ".php"; // removed to strip the basecp out of the equation here
	}
	
	// Security: Check that only allowed characters are present
	// in the class and method names.
	$classNameDisallowedCharacters = preg_replace(LETTERS_AND_NUMBERS_ONLY, '', $classname);
	
	if ($classNameDisallowedCharacters !== '')
	{
		// Error: Invalid class name.
		trigger_error("The supplied class name ($class) is invalid (it must only contain letters, numbers, and underscores)", E_USER_ERROR);
	}
	
	$methodNameDisallowedCharacters = preg_replace(LETTERS_AND_NUMBERS_ONLY, '', $args['method']);
	
	if ($methodNameDisallowedCharacters !== '')
	{
		// Error: Invalid method name.
		trigger_error("The supplied method name ($method) is invalid (it must only contain letters, numbers, and underscores)", E_USER_ERROR);
	}

	$body->methodName = $args['method'];
	$body->className = $classname;
	$body->classPath = $classpath;
	$body->uriClassPath = $uriclasspath;

	//Now deserialize the arguments

	$data = 'array()';
	// Were any arguments passed?
	if (array_key_exists('args', $args))
	{
		error_log('[SWX] INFO Arguments: ' . $args['args']);
		$data = $args['args'];
	}
	// Strip slashes in data
	$dataAsPhp = stripslashes($data);

	// Convert undefined and null to NULL
	$dataAsPhp = str_replace('undefined', 'NULL', $dataAsPhp);
	$dataAsPhp = str_replace('null', 'NULL', $dataAsPhp);

	// Massage special characters back (is there a better
	// way to do this?)
	$dataAsPhp = str_replace('\\t', '\t', $dataAsPhp);
	$dataAsPhp = str_replace('\\n', '\n', $dataAsPhp);
	$dataAsPhp = str_replace("\\'", "'", $dataAsPhp);

	// Convert the passed JSON data to a PHP array structure.
	$dataAsPhp = json_decode($dataAsPhp);

	$body->setValue($dataAsPhp);
}
function serializationAction(& $body)
{
	//Take the raw response
	$rawResponse = & $body->getResults();

	error_log ("[SWX] INFO Method call result = $rawResponse");

	adapterMap($rawResponse);
	
	$swxAssembler = null;
	
	$defaultFormat = 'as2';
	if (isset($_POST['format']))
		$defaultFormat = $_POST['format'];
	elseif (isset($_GET['format']))
		$defaultFormat = $_GET['format'];
		
	error_log('encoding with ' . $defaultFormat);

	if($defaultFormat == 'as3')
	{
		require_once(SWX_BASE . 'SwxAssembler3.php');
		$swxAssembler = new SwxAssembler3();	
	}
	else
	{
		require_once(SWX_BASE . 'SwxAssembler.php');
		$swxAssembler = new SwxAssembler();
	}

	ob_start();

	$swxAssembler->writeSwf($rawResponse, $GLOBALS['swx']['debug'], 4, $GLOBALS['swx']['url']);

	$rawResponse = ob_get_contents();
	ob_end_clean();

	$body->setResults($rawResponse);
}
function executionAction(& $body)
{
	$classConstruct = &$body->getClassConstruct();
	$methodName = $body->methodName;
	$args = $body->getValue();
	
	$output = Executive::doMethodCall($body, $classConstruct, $methodName, $args);
	
	if($output !== "__amfphp_error")
	{
		$body->setResults($output);
	}
}
if(!function_exists("json_encode"))
{
	include_once(AMFPHP_BASE . "shared/util/JSON.php");
	
	function json_encode($val)
	{
		$json = new Services_JSON();
		return $json->encode($val);
	}
	
	function json_decode($val, $asAssoc = FALSE)
	{
		if($asAssoc)
		{
			$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		}
		else
		{
			$json = new Services_JSON();
		}
		return $json->decode($val);
	}
}
function CakeDeserializationAction(&$body)
{
	$args = $body->getValue();

	$baseClassPath = $GLOBALS['amfphp']['classPath'];

	$trunced = $args['serviceClass'];
	$lpos = strrpos($trunced, ".");
	if ($lpos === false) {
		$classname = $trunced;
		$trunced = Inflector::underscore($trunced);
		$uriclasspath = $trunced . ".php";
		$classpath = $baseClassPath . $trunced . ".php";
	} else {
		$classname = substr($trunced, $lpos + 1);
		$trunced = Inflector::underscore($trunced);
		$classpath = $baseClassPath . str_replace(".", "/", $trunced) . ".php"; // removed to strip the basecp out of the equation here
		$uriclasspath = str_replace(".", "/", $trunced) . ".php"; // removed to strip the basecp out of the equation here
	}
	
	// Security: Check that only allowed characters are present
	// in the class and method names.
	$classNameDisallowedCharacters = preg_replace(LETTERS_AND_NUMBERS_ONLY, '', $classname);
	
	if ($classNameDisallowedCharacters !== '')
	{
		// Error: Invalid class name.
		trigger_error("The supplied class name ($class) is invalid (it must only contain letters, numbers, and underscores)", E_USER_ERROR);
	}
	
	$methodNameDisallowedCharacters = preg_replace(LETTERS_AND_NUMBERS_ONLY, '', $args['method']);
	
	if ($methodNameDisallowedCharacters !== '')
	{
		// Error: Invalid method name.
		trigger_error("The supplied method name ($method) is invalid (it must only contain letters, numbers, and underscores)", E_USER_ERROR);
	}

	$body->methodName = $args['method'];
	$body->className = $classname;
	$body->classPath = $classpath;
	$body->uriClassPath = $uriclasspath;

	//Now deserialize the arguments

	$data = 'array()';
	// Were any arguments passed?
	if (array_key_exists('args', $args))
	{
		error_log('[SWX] INFO Arguments: ' . $args['args']);
		$data = $args['args'];
	}
	// Strip slashes in data
	$dataAsPhp = stripslashes($data);

	// Convert undefined and null to NULL
	$dataAsPhp = str_replace('undefined', 'NULL', $dataAsPhp);
	$dataAsPhp = str_replace('null', 'NULL', $dataAsPhp);

	// Massage special characters back (is there a better
	// way to do this?)
	$dataAsPhp = str_replace('\\t', '\t', $dataAsPhp);
	$dataAsPhp = str_replace('\\n', '\n', $dataAsPhp);
	$dataAsPhp = str_replace("\\'", "'", $dataAsPhp);

	// Convert the passed JSON data to a PHP array structure.
	$dataAsPhp = json_decode($dataAsPhp);

	$body->setValue($dataAsPhp);
}
?>