<?php
//© 2019 Martin Peter Madsen
namespace MTM\Async\Models\Threading\Parallel;

class EventLoop extends Base
{
	protected $_initTime=null;
	protected $_rawObj=null;
	protected $_targetObjs=array();
	protected $_eventObjs=array();
	
	public function sendData($data)
	{
		//will send data to all channel targets from this event loop
		if ($data !== null) {
			$evObj	= $this->getParent()->getNewInputEvent($data);
			foreach ($this->getTargets() as $tObj) {
				if ($tObj instanceof \MTM\Async\Models\Threading\Parallel\Channel === true) {
					$evObj->addTarget($tObj);
				}
			}
			if (count($evObj->getTargets()) > 0) {
				$this->setInput($evObj);
			} else {
				//there are no channels on this event loop
			}
			return $this;
			
		} else {
			throw new \Exception("Data cannot be null");
		}
	}
	public function poll()
	{
		$rawObj	= $this->get()->poll();
		if (is_object($rawObj) === true) {
			if ($rawObj->object instanceof \parallel\Channel === true) {
				$targetObj	= $this->getParent()->getChannelByName($rawObj->source, true);
			} else {
				//need to add future
				throw new \Exception("Not handled for event object class: " . get_class($rawObj->object));
			}
			$evObj	= $this->getParent()->getNewEvent();
			$evObj->set($rawObj)->setTarget($targetObj)->setValue($rawObj->value);
			$evObj->setType($this->getParent()->getEventTypeById($rawObj->type));
			$this->_eventObjs[$evObj->getGuid()]	= $evObj;
			return $evObj;
			
		} elseif ($rawObj === null) {
			return null;
		} else {
			throw new \Exception("Not handled for event return");
		}
	}
	public function getEvents($timeoutMs=0)
	{
		$tTime	= \MTM\Utilities\Factories::getTime()->getMicroEpoch(true) + ($timeoutMs / 1000);
		while(true) {
			$evObj	= $this->poll();
			if (
				is_object($evObj) === false
				&& \MTM\Utilities\Factories::getTime()->getMicroEpoch(true) > $tTime
			) {
				break;
			}
		}
		return array_values($this->_eventObjs);
	}
	public function addTarget($tObj)
	{
		if (
			$tObj instanceof \MTM\Async\Models\Threading\Parallel\Channel === false
			&& $tObj instanceof \MTM\Async\Models\Threading\Parallel\Future === false
		) {
			throw new \Exception("Invalid Target");
		} elseif (array_key_exists($tObj->getGuid(), $this->_targetObjs) === false) {
			$this->_targetObjs[$tObj->getGuid()]	= $tObj;
			
			if ($tObj instanceof \MTM\Async\Models\Threading\Parallel\Channel === true) {
				$this->get()->addChannel($tObj->get());
			} elseif ($tObj instanceof \MTM\Async\Models\Threading\Parallel\Future === true) {
				$this->get()->addFuture($tObj->get());
			}
		}
		return $this;
	}
	public function setInput($evObj)
	{
		$this->get()->setInput($evObj->get());
		$this->poll();
		return $this;
	}
	public function setBlocking($bool)
	{
		$this->get()->setBlocking($bool);
		return $this;
	}
	public function setTimeout($microSecs)
	{
		$this->get()->setTimeout($microSecs);
		return $this;
	}
	public function get()
	{
		if ($this->_rawObj === null) {
			$rObj	= new \parallel\Events();
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
			throw new \Exception("Event Loop already initialized cannot set");
		}
	}
	public function getTargets()
	{
		return array_values($this->_targetObjs);
	}
	public function terminate()
	{
		if ($this->getTerminationStatus() === false) {
			$this->_termStatus	= true;
			$this->getParent()->removeEventLoop($this);
			parent::terminate();
		}
	}
}