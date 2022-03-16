<?php
//© 2022 Martin Peter Madsen
try {
	if (isset($argv[1]) === false) {
		//we cannot report anything
		die("Missing process ID");
	} elseif (isset($argv[2]) === false) {
		//we cannot report anything
		die("Missing process data");
	}

	$argObj					= new \stdClass();
	$argObj->procId			= $argv[1];
	$procData				= unserialize(base64_decode($argv[2]));
	if (is_array($procData) === false) {
		die("Invalid process data");
	} elseif (array_key_exists("procFile", $procData) === false) {
		throw new \Exception("Missing process file path", 39331);
	}
	
	$argObj->procFile		= realpath($procData["procFile"]);
	if ($argObj->procFile === false) {
		//we cannot report anything
		die("Invalid process file path");
	}
	
	if (array_key_exists("bootStrap", $procData) === false) {
		throw new \Exception("Missing boot strap file path", 39332);
	} elseif (array_key_exists("class", $procData) === false) {
		throw new \Exception("Missing class", 39333);
	} elseif (array_key_exists("method", $procData) === false) {
		throw new \Exception("Missing method", 39334);
	} elseif (array_key_exists("args", $procData) === false) {
		throw new \Exception("Missing arguments", 39335);
	}
	
	$argObj->bootStrap		= realpath($procData["bootStrap"]);
	if ($argObj->bootStrap === false) {
		throw new \Exception("Invalid bootstrap file path: '".$procData["bootStrap"]."'", 39336);
	} else {
		//load the boot strap before anything else to ensure objects can be instantiated
		require_once $argObj->bootStrap;
	}
	if (is_string($procData["class"]) === false || class_exists($procData["class"]) === false) {
		throw new \Exception("Class: '".$procData["class"]."', does not exist or is invalid", 39337);
	} else {
		$argObj->class	= new $procData["class"]();
	}
	if (is_string($procData["method"]) === false || method_exists($argObj->class, $procData["method"]) === false) {
		throw new \Exception("Class: '".$procData["class"]."', does not have method: '".$procData["method"]."'", 39338);
	} else {
		$argObj->method		= $procData["method"];
	}
	
	//args are serialized once more so we can load the boot strap before unpacking potential objects
	$procData["args"]		= unserialize($procData["args"]);
	if (is_array($procData["args"]) === false) {
		throw new \Exception("Arguments must be in array", 39341);
	} else {
		$argObj->args		= $procData["args"];
	}

	$refObj 	= new \ReflectionMethod($argObj->class, $argObj->method);
	if ($refObj->isPublic() === false) {
		throw new \Exception("Class: '".$procData["class"]."' method: '".$procData["method"]."' is not public", 39339);
	}
	if ($refObj->getNumberOfRequiredParameters() > count($argObj->args)) {
		throw new \Exception("Class: '".$procData["class"]."' method: '".$procData["method"]."' expects: '".$refObj->getNumberOfRequiredParameters()."' arguments, only: '".count($argObj->args)."' provided", 39340);
	}

	//tell the parent process we are launching
	if (file_exists($argObj->procFile) === true) {
		file_put_contents($argObj->procFile, "launching:|MTM|:".base64_encode(serialize(getmypid()))."\n", FILE_APPEND);
	}
	
	$rData	= call_user_func_array(array($argObj->class, $argObj->method), $argObj->args);
	
	if (file_exists($argObj->procFile) === true) {
		file_put_contents($argObj->procFile, "return:|MTM|:".base64_encode(serialize((object) array("time" => time, "data" => $rData)))."\n", FILE_APPEND);
	}

} catch (\Exception $e) {
	if (file_exists($argObj->procFile) === true) {
		file_put_contents($argObj->procFile, "exception:|MTM|:".base64_encode(serialize(array("message" => $e->getMessage(), "code" => $e->getCode(), "trace" => $e->getTraceAsString())))."\n", FILE_APPEND);
	}
}
