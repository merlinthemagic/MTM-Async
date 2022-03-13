<?php
//© 2022 Martin Peter Madsen
namespace MTM\Async\Docs\Examples\Processes\V1\Simple;

class Child
{
	public function startProcess($firstArg, $secondArg)
	{
		$result		= array();
		$result[]	= "First arg: '" . $firstArg . "'";
		usleep(500);
		$result[]	= "Thread has pid: " . getmypid();
		usleep(500);
		$result[]	= "Second arg: '" . $secondArg . "'";
		return $result;
	}
	public function startError($firstArg, $secondArg)
	{
		throw new \Exception("This exception was thrown in the process");
	}
}