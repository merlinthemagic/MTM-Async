<?php
//© 2019 Martin Peter Madsen
namespace MTM\Async\Models\Threading\Parallel;

class Api extends Base
{
	protected $_ctrlChanObj=null;
	protected $_threadObjs=array();
	protected $_channelObjs=array();
	protected $_eventLoopObjs=array();

	public function getNewThread($bootStrap=null)
	{
		$rObj	= new \MTM\Async\Models\Threading\Parallel\Thread();
		$rObj->setParent($this)->setBootStrap($bootStrap);
		$this->_threadObjs[$rObj->getGuid()]	= $rObj;
		return $rObj;
	}
	public function getNewChannel($name=null, $size=null)
	{
		$rObj	= new \MTM\Async\Models\Threading\Parallel\Channel();
		$rObj->setParent($this)->setName($name)->setSize($size);
		$this->_channelObjs[$rObj->getGuid()]	= $rObj;
		return $rObj;
	}
	public function getNewEventLoop()
	{
		$rObj	= new \MTM\Async\Models\Threading\Parallel\EventLoop();
		$rObj->setParent($this);
		$this->_eventLoopObjs[$rObj->getGuid()]	= $rObj;
		return $rObj;
	}
	public function getNewInputEvent($data=null)
	{
		$rObj	= new \MTM\Async\Models\Threading\Parallel\Events\Input();
		$rObj->setParent($this);
		if ($data !== null) {
			$rObj->setData($data);
		}
		return $rObj;
	}
	public function getNewEvent()
	{
		$rObj	= new \MTM\Async\Models\Threading\Parallel\Events\Event();
		$rObj->setParent($this);
		return $rObj;
	}
	public function getEventTypeById($id)
	{
		$sId	= __FUNCTION__ . $id;
		if (array_key_exists($sId, $this->_cStore) === false) {
			$rObj	= new \MTM\Async\Models\Threading\Parallel\EventType($id);
			$rObj->setParent($this);
			$this->_cStore[$sId]	= $rObj;
		}
		return $this->_cStore[$sId];
	}
	public function getThreads()
	{
		return array_values($this->_threadObjs);
	}
	public function getChannels()
	{
		return array_values($this->_channelObjs);
	}
	public function getChannelByName($name, $throw=false)
	{
		foreach ($this->getChannels() as $eObj) {
			if ($eObj->getName() == $name) {
				return $eObj;
			}
		}
		if ($throw === false) {
			return null;
		} else {
			throw new \Exception("Channel: " . $name . ", does not exist");
		}
	}
	public function removeThread($threadObj)
	{
		if (array_key_exists($threadObj->getGuid(), $this->_threadObjs) === true) {
			unset($this->_threadObjs[$threadObj->getGuid()]);
			$threadObj->terminate();
		}
		return $this;
	}
	public function removeChannel($channelObj)
	{
		if (array_key_exists($channelObj->getGuid(), $this->_channelObjs) === true) {
			unset($this->_channelObjs[$channelObj->getGuid()]);
			$channelObj->terminate();
		}
		return $this;
	}
	public function removeEventLoop($evLoopObj)
	{
		if (array_key_exists($evLoopObj->getGuid(), $this->_eventLoopObjs) === true) {
			unset($this->_eventLoopObjs[$evLoopObj->getGuid()]);
			$evLoopObj->terminate();
		}
		return $this;
	}
	public function terminate()
	{
		if ($this->getTerminationStatus() === false) {
			$this->_termStatus	= true;
			
			foreach ($this->getThreads() as $thObj) {
				try {
					$this->removeThread($thObj);
				} catch (\Exception $e) {
				}
			}
			
			foreach ($this->getChannels() as $chObj) {
				try {
					$this->removeChannel($chObj);
				} catch (\Exception $e) {
				}
			}
		}
	}
	public function setTreadCtrl($channelObj)
	{
		//control channel for a spawned thread
		$this->_ctrlChanObj		= $channelObj;
		return $this;
	}
	public function getTreadCtrl()
	{
		return $this->_ctrlChanObj;
	}
}