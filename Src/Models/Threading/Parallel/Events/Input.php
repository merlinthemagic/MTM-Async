<?php
//© 2019 Martin Peter Madsen
namespace MTM\Async\Models\Threading\Parallel\Events;

class Input extends \MTM\Async\Models\Threading\Parallel\Base
{
	protected $_initTime=null;
	protected $_rawObj=null;
	protected $_data=null;
	protected $_targetObjs=array();
	
	public function setData($data)
	{
		if ($data !== null) {
			$this->_data	= $data;
			return $this;
		} else {
			throw new \Exception("Data cannot be null");
		}
	}
	public function getData()
	{
		return $this->_data;
	}
	public function addTarget($obj)
	{
		if ($this->getData() !== null) {
			$this->_targetObjs[$obj->getGuid()]	= $obj;
			$this->get()->add($obj->getName(), $this->getData());
			return $this;
		} else {
			throw new \Exception("Data must be set before target can be added");
		}
	}
	public function getTargets()
	{
		return array_values($this->_targetObjs);
	}
	public function get()
	{
		if ($this->_rawObj === null) {
			$rObj	= new \parallel\Events\Input();
			$this->set($rObj);
		}
		return $this->_rawObj;
	}
	public function set($obj)
	{
		if ($this->_rawObj === null) {
			$this->_rawObj		= $obj;
			$this->_initTime	= \MTM\Utilities\Factories::getTime()->getMicroEpoch(true);
			return $this;
		} else {
			throw new \Exception("Input event already initialized cannot set");
		}
	}
}