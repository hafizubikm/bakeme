<?php
// Error handling
function errorHandler($errorNum, $errorStr, $errorFile, $errorLine)
{
	$errorMsg = "Error $errorNum: $errorStr in $errorFile, line $errorLine.";
	$GLOBALS['swxLastErrorMessage'] = $errorMsg;
	
	// Display the error message in the PHP error log
	error_log($errorMsg);
	
	if ($errorNum != E_STRICT && $errorNum != E_NOTICE)
	{
		// On errors and warnings, stop execution and return the
		// error message to the client. This is a far better
		// alternative to failing silently.
		error_log($errorMsg);
		$swxAssembler = new SwxAssembler();
		$swxAssembler->writeSwf($errorMsg, (!empty($GLOBALS['swx']['debug'])) ? $GLOBALS['swx']['debug'] : false);
		exit();
	}
}

// Error handling (unfortunately has to be global to support PHP 4)
set_error_handler('errorHandler');

// Turn on error reporting
error_reporting(E_ALL);

?>