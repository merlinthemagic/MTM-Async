<?php
//© 2019 Martin Peter Madsen
namespace MTM\Async\Docs\Examples\Parallel\Simple;

class Thread
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
	public function startError()
	{
		throw new \Exception("this exception was thrown in the thread");
	}
}