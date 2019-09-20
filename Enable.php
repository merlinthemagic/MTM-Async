<?php
//© 2019 Martin Peter Madsen
if (defined("MTM_ASYNC_BASE_PATH") === false) {
	define('MTM_ASYNC_VENDOR_NAME', basename(realpath(__DIR__ . DIRECTORY_SEPARATOR . "..")));
	define("MTM_ASYNC_BASE_NAME", basename(__DIR__));
	define("MTM_ASYNC_BASE_PATH", __DIR__ . DIRECTORY_SEPARATOR);
	define('MTM_ASYNC_CLASS_PATH', implode(DIRECTORY_SEPARATOR, array_slice(explode(DIRECTORY_SEPARATOR, MTM_ASYNC_BASE_PATH), 0, -3)) . DIRECTORY_SEPARATOR);
	spl_autoload_register(function($className) {
		if (class_exists($className) === false) {
			$cPath	= array_values(array_filter(explode("\\", $className)));
			if ($cPath[0] == MTM_ASYNC_VENDOR_NAME && $cPath[1] == MTM_ASYNC_BASE_NAME) {
				$filePath	= MTM_ASYNC_CLASS_PATH . str_replace('\\', DIRECTORY_SEPARATOR, $className) . ".php";
				if (is_readable($filePath) === true) {
					require_once $filePath;
				}
			}
		}
	});
	function loadMtmAsync()
	{
		$basePath		= realpath(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "..") . DIRECTORY_SEPARATOR;
		$deps			= array();
		$deps["MTM"]	= array("Utilities");
		foreach ($deps as $dir => $libs) {
			foreach ($libs as $name) {
				require_once $basePath . $dir . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . "Enable.php";
			}
		}
	}
	loadMtmAsync();
}