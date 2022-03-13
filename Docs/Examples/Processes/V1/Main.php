<?php
//© 2022 Martin Peter Madsen
namespace MTM\Async\Docs\Examples\Processes\V1\Simple;

class Main
{
	public function successChild()
	{
		$procFact		= \MTM\Async\Factories::getProcesses()->getV1();
		$bootstrapFile	= realpath(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Bootstrap.php");
		$class			= "\MTM\Async\Docs\Examples\Processes\V1\Child";
		$method			= "startProcess";
		
		$arg1			= "Main is pid: " . getmypid();
		$arg2			= "Some data";
		$procObj		= $procFact->getNewProcess($bootstrapFile, $class, $method, array($arg1, $arg2));
		$procObj->initialize();
		
		$throwError		= true;
		$timeoutMs		= 10000;
		
		$rData			= $procObj->getReturn($timeoutMs, $throwError);
		echo print_r($rData, true);
	}
	public function errorChild()
	{
		$procFact		= \MTM\Async\Factories::getProcesses()->getV1();
		$bootstrapFile	= realpath(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Bootstrap.php");
		$class			= "\MTM\Async\Docs\Examples\Processes\V1\Child";
		$method			= "startError";
		
		$arg1			= "Main is pid: " . getmypid();
		$arg2			= "Some data";
		$procObj		= $procFact->getNewProcess($bootstrapFile, $class, $method, array($arg1, $arg2));
		$procObj->initialize();
		
		$throwError		= true;
		$timeoutMs		= 10000;
		
		$rData			= $procObj->getReturn($timeoutMs, $throwError);
		echo print_r($rData, true);
	}
}