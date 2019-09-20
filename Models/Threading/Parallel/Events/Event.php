<?php
//© 2019 Martin Peter Madsen
namespace MTM\Async\Models\Threading\Parallel\Events;

class Event extends \MTM\Async\Models\Threading\Parallel\Base
{
	protected $_initTime=null;
	protected $_rawObj=null;
	protected $_targetObj=null;
	protected $_value=null;
	protected $_typeObj=null;
	
	public function setValue($data)
	{
		$this->_value	= $data;
		return $this;
	}
	public function getValue()
	{
		return $this->_value;
	}
	public function setType($obj)
	{
		$this->_typeObj	= $obj;
		return $this;
	}
	public function getType()
	{
		return $this->_typeObj;
	}
	public function setTarget($obj)
	{
		$this->_targetObj	= $obj;
		return $this;
	}
	public function getTarget()
	{
		return $this->_targetObj;
	}
	public function get()
	{
		if ($this->_rawObj === null) {
			$rObj	= new \parallel\Events\Event();
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
			throw new \Exception("Event already initialized cannot set");
		}
	}
}