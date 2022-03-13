<?php
//© 2022 Martin Peter Madsen
namespace MTM\Async\Models\Processes\V1\Process;

abstract class Base
{
	protected $_isInit=false;
	protected $_isTerm=false;
	protected $_guid=null;
	protected $_apiObj=null;
	protected $_filePath=null;
	protected $_className=null;
	protected $_methodName=null;
	protected $_args=array();
	protected $_persist=false;
	protected $_procGuid=null;
	
	public function __construct()
	{
		register_shutdown_function(array($this, "__destruct"));
	}
	public function __destruct()
	{
		$this->terminate();
	}
	public function setGuid($guid)
	{
		//set to pick up an existing process
		if (is_string($guid) === false) {
			throw new \Exception("Guid must be string");
		} elseif ($this->_isInit !== false) {
			throw new \Exception("Guid cannot be set on a process that has initialized");
		}
		$this->_guid	= $guid;
		return $this;
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
	public function setPersistence($bool)
	{
		if (is_bool($bool) === false) {
			throw new \Exception("Persistance must be boolean");
		}
		$this->_persist	= $bool;
		return $this;
	}
	public function getPersistence()
	{
		return $this->_persist;
	}
}