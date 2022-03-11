<?php
//© 2022 Martin Peter Madsen
file_put_contents("/dev/shm/merlinFFF.txt", "Start: " .getmypid()."\n", FILE_APPEND);
$aCount	= count($argv);
if ($aCount === 6) {
	$argObj					= new \stdClass();
	$argObj->bootStrap		= realpath(base64_decode($argv[2]));
	if ($argObj->bootStrap === false) {
		die("Invalid bootstrap file path");
	}
	//load the boot strap before anything else to ensure objects can be instanciated
	require_once $argObj->bootStrap;
	
	$argObj->class		= base64_decode($argv[3]);
	$argObj->method		= base64_decode($argv[4]);
	$argObj->args		= unserialize(base64_decode($argv[5]));
	
	try {
		call_user_func(array(new $argObj->class(), $argObj->method), $argObj->args);
	} catch (\Exception $e) {
		//signal we are done
		throw $e;
	}
} else {
	//invalid argument count, how to signal that back?
	die("Invalid argument input count");
}
file_put_contents("/dev/shm/merlinFFF.txt", "End: " .getmypid()."\n", FILE_APPEND);
