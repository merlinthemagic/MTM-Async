<?php
//© 2019 Martin Peter Madsen
namespace MTM\Async\Models\Threading\Parallel;

class Future extends Base
{
	protected $_initTime=null;
	protected $_rawObj=null;
	
	public function set($obj)
	{
		if ($this->_rawObj === null) {
			$this->_rawObj		= $obj;
			$this->_initTime	= \MTM\Utilities\Factories::getTime()->getMicroEpoch(true);
			return $this;
		} else {
			throw new \Exception("Future already initialized cannot set");
		}
	}
	public function get()
	{
		//return raw parallel obj
		return $this->_rawObj;
	}
	public function getValue()
	{
		try {
			//return value from thread, wait
			return $this->get()->value();
			
		} catch (\parallel\Future\Error\Killed $e) {
			throw new \Exception("Thread was killed: " . $e->getMessage(), $e->getCode());
		}
	}
	public function isDone()
	{
		//has the thread completed?
		return $this->get()->done();
	}
	public function isCancelled()
	{
		//was the thread cancelled?
		return $this->get()->cancelled();
	}
	public function cancel()
	{
		if (
			$this->isDone() === false 
			&& $this->isCancelled() === false
			&& $this->get()->cancel() === false
		) {
			throw new \Exception("Failed to cancel");
		}
		return $this;
	}
	public function terminate()
	{
		if ($this->getTerminationStatus() === false) {
			$this->_termStatus	= true;
			$this->getParent()->terminate();
			parent::terminate();
		}
	}
}