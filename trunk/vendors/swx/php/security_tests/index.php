<?php
	// SWX: SWF Exchange Format - PHP Security Test Suite
	// Author: Aral Balkan (http://aralbalkan.com)
	// Copyright (c) 2007 Aral Balkan. 
	//
	// These tests try and attack the SWX gateway
	// with malicious code. 
	//
	// Tail the php error log and run this PHP file
	// in the browser to run the tests.
	
	error_log('FOLLOWING SECURITY CHECK(S) SHOULD FAIL:');
	
	check('Simple', 'echoData', 'doesnotbeginwitharray');
	check('Simple', 'echoData', 'array()doesnotendcorrectly');
	check('Simple', 'echoData', 'array($_SERVER[\'SERVER_SOFTWARE\'])');
	check('Simple', 'echoData', 'array($somevar)');
	check('Simple', 'echoData', 'array(A_CONSTANT)');
	check('Simple', 'echoData', 'array(functionCall())');
	check('Simple', 'echoData', 'array(functioncall())');
	check('Simple', 'echoData', 'array(functioncall(with, args))');
	check('Simple', 'echoData', 'array(functiondef(){})');
	check('Simple', 'echoData', 'array(); phpInfo()');	
	check('Simple', 'echoData', 'array(); sub_str()');	
	check('NonExistentClass', 'echoData', 'array()');
	check('Simple', 'nonExistentMethod', 'array()');
	check('Simple', 'privateMethod', 'array()');
	
	error_log('FOLLOWING SECURITY CHECK(S) SHOULD PASS:');

	check('Simple', 'echoData', 'array()');				
	
	// Helper method that gets a passed url
	function check($class, $method, $dataString = '')
	{
		error_log('Security check: ' . urlencode($dataString));
		
		$postFields = array('data' => $dataString, 'className' => $class, 'method' => $method, 'debug' => 'true');
		
		$ch = curl_init('http://localhost:8888/swx/trunk/php/swx.php');
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 120);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt ($ch, CURLOPT_REFERER, 'http://localhost:8888');
		curl_setopt($ch, CURLOPT_POST, 1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields); 

		$response = curl_exec($ch);
		
		error_log('--- End current security check ---');
	}	
	
	
?>