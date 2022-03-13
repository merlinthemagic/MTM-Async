<?php
//© 2019 Martin Madsen
if (defined("MTM_ASYNC_EXAMPLES_BASE_PATH") === false) {
	
	define("MTM_ASYNC_EXAMPLES_BASE_PATH", __DIR__ . DIRECTORY_SEPARATOR);
	
	if (defined("MTM_ASYNC_BASE_PATH") === false || defined("MTM_UTILITIES_BASE_PATH") === false) {
		
		//include your own project composer autoload.php, then we dont have to find Async and Utilities
		$vendorPath	= explode(DIRECTORY_SEPARATOR, MTM_ASYNC_EXAMPLES_BASE_PATH);
		$vendorPath	= implode(DIRECTORY_SEPARATOR, array_slice($vendorPath, 0, -5)) . DIRECTORY_SEPARATOR;

		if (defined("MTM_ASYNC_BASE_PATH") === false) {
			//composer has not loaded MTM/Async, we need to include
			$reqPath	= $vendorPath . "mtm-async" . DIRECTORY_SEPARATOR . "Enable.php";
			if (is_readable($reqPath) === true) {
				require_once $reqPath;
			} else {
				throw new \Exception("MTM-Async autoloader missing");
			}
		}
		if (defined("MTM_UTILITIES_BASE_PATH") === false) {
			//composer has not loaded MTM/Utilities, we need to include
			$reqPath	= $vendorPath . "mtm-utilities" . DIRECTORY_SEPARATOR . "Enable.php";
			if (is_readable($reqPath) === true) {
				require_once $reqPath;
			} else {
				throw new \Exception("MTM-Utilities autoloader missing");
			}
		}
	}
}