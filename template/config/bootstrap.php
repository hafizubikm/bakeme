<?php
	define('DEFAULT_LANGUAGE', 'dut');
	
	function error_print_r($o)
	{
		ob_start();
		print_r($o);
		error_log(ob_get_clean());
	}
?>