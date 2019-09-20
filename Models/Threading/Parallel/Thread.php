<?php
//© 2019 Martin Peter Madsen
namespace MTM\Async\Models\Threading\Parallel;

class Thread extends Base
{
	protected $_initEntry=false;
	protected $_initTime=null;
	protected $_threadObj=null;
	protected $_channelObj=null;
	protected $_futureObj=null;
	protected $_bootStrap=null;
	
	public function setBootStrap($file)
	{
		$this->_bootStrap	= $file;
		return $this;
	}
	public function getBootStrap()
	{
		if ($this->_bootStrap === null) {
			//async lib must be included, otherwise we cannot recreate the channel
			$this->setBootStrap(MTM_ASYNC_BASE_PATH . "Enable.php");
		}
		return $this->_bootStrap;
	}
	public function getFuture()
	{
		return $this->_futureObj;
	}
	public function initByClassMethod($class, $method, $args=array())
	{
		if ($this->_initEntry === false) {
			if (is_object($class) === true) {
				$class	= get_class($class);
			}
			$init				= new \stdClass();
			$init->type			= "entryByMethod";
			$init->class		= $class;
			$init->method		= $method;
			$init->args			= $args;
			$this->getChannel()->setData($init);
			$this->_initEntry	= true;
			return $this;
			
		} else {
			return new \Exception("Init entry already called");
		}
	}
	public function getChannel()
	{
		if ($this->_channelObj === null) {
			$this->_channelObj	= $this->getParent()->getNewChannel("Ctrl-" . $this->getGuid(), -1);
			//make sure the thread is initialized
			$this->get();
		}
		return $this->_channelObj;
	}
	public function get()
	{
		if ($this->_threadObj === null) {
			$this->_threadObj	= new \parallel\Runtime($this->getBootStrap());
			$this->_futureObj	= new \MTM\Async\Models\Threading\Parallel\Future();
			$this->_futureObj->setParent($this);
			
			//kick off the thread
			$this->_futureObj->set($this->_threadObj->run(function($rawObj) {

				if (defined("MTM_ASYNC_BASE_PATH") === true) {
					
					//recreate the channel object
					$name	= $rawObj->__toString();
					$size	= -1;
					//there is no method to get size currently
					if (preg_match("/\[capacity\]\s\=\>\s(infinite|[0-9]+)/", print_r($rawObj, true), $raw) == 1) {
						if ($raw[1] != "infinite") {
							$size	= $raw[1];
						}
					}
					$pFact		= \MTM\Async\Factories::getThreading()->getParallel();
					$chanObj	= $pFact->getNewChannel($name, $size)->set($rawObj);
					$pFact->setTreadCtrl($chanObj);

					$initObj	= $chanObj->getData();
					if (
						$initObj instanceof \stdClass === true
						&& property_exists($initObj, "type") === true
						&& is_string($initObj->type) === true
					) {
						if ($initObj->type == "entryByMethod") {
							if (class_exists($initObj->class) === true) {
								$obj = new $initObj->class();
								if (method_exists($obj, $initObj->method) === true) {
									return call_user_func_array(array($obj, $initObj->method), $initObj->args);
								} else {
									return new \Exception("Method: " .$initObj->method. ", does not exist on class: " . $initObj->class);
								}
								
							} else {
								return new \Exception("Class does not exist: " . $initObj->class);
							}

						} else {
							return new \Exception("Not handled for type: " . $initObj->type);
						}
						
					} else {
						return new \Exception("Entry data is invalid");
					}
					
				} else {
					return new \Exception("MTM-Async must be Enable by the boot strap");
				}

			}, array($this->getChannel()->get())));
			
			$this->_initTime	= \MTM\Utilities\Factories::getTime()->getMicroEpoch(true);
		}
		return $this->_threadObj;
	}
	public function terminate()
	{
		if ($this->getTerminationStatus() === false) {
			$this->_termStatus	= true;
			if ($this->_initTime !== null) {
				if ($this->getFuture()->isDone() === false) {
					$isValid	= $this->getFuture()->get()->cancel();
					if ($isValid === false) {
						//something went wrong, but we may be shutting down dont throw
					}
				}
			}
			$this->getParent()->removeChannel($this);
			$this->getChannel()->terminate();
			parent::terminate();
		}
	}
}