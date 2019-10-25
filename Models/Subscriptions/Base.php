<?php
//© 2019 Martin Peter Madsen
namespace MTM\Async\Models\Subscriptions;

abstract class Base
{
	protected $_guid=null;
	protected $_parentObj=null;
	protected $_callCb=null;
	protected $_data=null;
	
	public function getGuid()
	{
		if ($this->_guid === null) {
			$this->_guid	= \MTM\Utilities\Factories::getGuids()->getV4()->get(false);
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
	public function set($data)
	{
		$this->_data	= $data;
		return $this;
	}
	public function get()
	{
		return $this->_data;
	}
	public function setCallback($obj, $method)
	{
		if (is_object($obj) === true && is_string($method) === true) {
			$this->_callCb	= array($obj, $method);
		}
		return $this;
	}
	public function call()
	{
		if ($this->_callCb !== null) {
			call_user_func_array($this->_callCb, array($this));
		} else {
			//without a callback there is no reason to stay, we exist in a vacuum
			$this->unsubscribe();
		}
	}
	public function unsubscribe()
	{
		$this->getParent()->removeSubscription($this);
		return $this;
	}
	public function subscribe()
	{
		//allows resubscribe
		$this->getParent()->addSubscription($this);
		return $this;
	}
}