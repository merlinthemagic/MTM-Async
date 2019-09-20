<?php
//© 2019 Martin Peter Madsen
namespace MTM\Async\Models\Services;

abstract class Base
{
	protected $_subObjs=array();
	protected $_errorCb=null;

	public function getSubscriptions()
	{
		return $this->_subObjs;
	}
	public function addSubscription($subObj)
	{
		$guid					= $subObj->getGuid();
		$this->_subObjs[$guid]	= $subObj;
		return $this;
	}
	public function removeSubscription($subObj)
	{
		$guid	= $subObj->getGuid();
		if (array_key_exists($guid, $this->_subObjs) === true) {
			unset($this->_subObjs[$guid]);
		}
		return $this;
	}
	public function setErrorCallback($obj, $method)
	{
		if (is_object($obj) === true && is_string($method) === true) {
			$this->_errorCb	= array($obj, $method);
		}
		return $this;
	}
	public function run()
	{
		while (true) {
			if ($this->runOnce() === 0) {
				break;
			} else {
				//if you dont want this delay, then make your own loop
				usleep(10000);
			}
		}
		return $this;
	}
	public function runOnce()
	{
		$subObjs	= $this->getSubscriptions();
		$subCount	= count($subObjs);
		if ($subCount > 0) {
			foreach ($subObjs as $subObj) {
				
				try {
					$subObj->call();
				} catch (\Exception $e) {
					//we dont deal with the errors, thats the user's responsibillity
					$this->removeSubscription($subObj);
					$this->callError($subObj, $e);
				}
			}
		}
		return $subCount;
	}
	public function callError($subObj, $e)
	{
		if ($this->_errorCb !== null) {
			try {
				call_user_func_array($this->_errorCb, array($subObj, $e));
			} catch (\Exception $e) {
				//The function set must deal with errors
			}
		}
	}
}