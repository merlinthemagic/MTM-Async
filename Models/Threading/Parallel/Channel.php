<?php
//© 2019 Martin Peter Madsen
namespace MTM\Async\Models\Threading\Parallel;

class Channel extends Base
{
	protected $_initTime=null;
	protected $_rawObj=null;
	protected $_name=null;
	protected $_size=null;
	
	public function getData()
	{
		return $this->get()->recv();
	}
	public function setData($data)
	{
		return $this->get()->send($data);
	}
	public function setName($name)
	{
		if ($this->_initTime === null) {
			if ($name !== null) {
				if ($this->getParent()->getChannelByName($name) !== null) {
					throw new \Exception("Channel: " . $name . ", already in use");
				} else {
					$this->_name	= $name;
				}
			}
		} else {
			throw new \Exception("Channel initialized cannot set name");
		}
		return $this;
	}
	public function getName()
	{
		return $this->_name;
	}
	public function setSize($bytes)
	{
		if ($this->_initTime === null) {
			if ($bytes !== null) {
				if ($bytes < 0) {
					$this->_size	= -1;
				} else {
					$this->_size	= $bytes;
				}
			}
		} else {
			throw new \Exception("Channel initialized cannot set size");
		}
		return $this;
	}
	public function getSize()
	{
		return $this->_size;
	}
	public function get()
	{
		if ($this->_rawObj === null) {
			
			$name	= $this->getName();
			if ($name === null) {
				$name	= $this->getGuid();
			}
			$size	= $this->getSize();
			if ($name === null) {
				$size	= -1;
			}
			$cFact	= new \parallel\Channel();
			$this->set($cFact->make($name, $size));
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
			throw new \Exception("Channel already initialized cannot set");
		}
	}
	public function terminate()
	{
		if ($this->getTerminationStatus() === false) {
			$this->_termStatus	= true;
			$this->getParent()->removeChannel($this);
			parent::terminate();
		}
	}
}