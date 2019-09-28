<?php
//© 2019 Martin Peter Madsen
namespace MTM\Async\Docs\Examples\Parallel\Simple;

class Main
{
	public function successThread()
	{
		$bootstrapFile	= realpath(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Bootstrap.php");
		$threadObj 		= \MTM\Async\Factories::getThreading()->getParallel()->getNewThread($bootstrapFile);
		
		$workerObj		= new \MTM\Async\Docs\Examples\Parallel\Simple\Thread();
		$arg1			= "Main is pid: " . getmypid();
		$arg2			= "Some data";
		$threadObj->initByClassMethod($workerObj, "startProcess", array($arg1, $arg2));
		$rData			= $threadObj->getFuture()->getValue(); //is blocking
		
		echo print_r($rData, true);
	}
	public function errorThread()
	{
		$bootstrapFile	= realpath(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Bootstrap.php");
		$threadObj 		= \MTM\Async\Factories::getThreading()->getParallel()->getNewThread($bootstrapFile);
		
		$workerObj		= new \MTM\Async\Docs\Examples\Parallel\Simple\Thread();
		$threadObj->initByClassMethod($workerObj, "startError");
		try {
			$rData			= $threadObj->getFuture()->getValue();
		} catch (\Exception $e) {
			echo "Thread threw error: " . $e->getMessage();
		}
		
	}
	
}