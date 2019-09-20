<?php
//© 2019 Martin Peter Madsen
namespace MTM\Async\Models\Threading\Parallel;

class Base
{
	protected $_cStore=array();
	protected $_guid=null;
	protected $_termStatus=null;
	protected $_parentObj=null;
	
	public function __destruct()
	{
		$this->terminate();
	}
	public function setGuid($guid)
	{
		$this->_guid	= $guid;
		return $this;
	}
	public function getGuid()
	{
		if ($this->_guid === null) {
			$this->setGuid(\MTM\Utilities\Factories::getGuids()->getV4()->get(false));
		}
		return $this->_guid;
	}
	public function setParent($obj)
	{
		$this->_parentObj	= $obj;
		return $this;
	}
	public function getParent()
	{
		return $this->_parentObj;
	}
	public function getTerminationStatus()
	{
		return $this->_termStatus;
	}
	public function terminate()
	{
		//child calls
	}
}