<?php
//© 2022 Martin Peter Madsen
namespace MTM\Async\Models\Processes\V1\Process;

abstract class Base
{
	protected $_guid=null;
	protected $_apiObj=null;
	protected $_filePath=null;
	protected $_className=null;
	protected $_methodName=null;
	protected $_args=array();
	
	public function __construct()
	{
		$this->_guid	= \MTM\Utilities\Factories::getGuids()->getV4()->get(false);
	}
	public function getGuid()
	{
		return $this->_guid;
	}
	public function setAPI($obj)
	{
		$this->_apiObj	= $obj;
		return $this;
	}
	public function getAPI()
	{
		return $this->_apiObj;
	}
	public function setFilePath($value)
	{
		$this->_filePath	= $value;
		return $this;
	}
	public function getFilePath()
	{
		return $this->_filePath;
	}
	public function setClassName($value)
	{
		$this->_className	= $value;
		return $this;
	}
	public function getClassName()
	{
		return $this->_className;
	}
	public function setMethodName($value)
	{
		$this->_methodName	= $value;
		return $this;
	}
	public function getMethodName()
	{
		return $this->_methodName;
	}
	public function setArguments($arr)
	{
		$this->_args	= $arr;
		return $this;
	}
	public function getArguments()
	{
		return $this->_args;
	}
}