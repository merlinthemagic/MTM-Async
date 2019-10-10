<?php
//© 2019 Martin Peter Madsen
namespace MTM\Async\Models\Threading\Parallel;

class Sync extends Base
{
	protected $_initTime=null;
	protected $_rawObj=null;
	protected $_id=null;
	
	public function setId($int)
	{
		$this->_id	= $int;
		$this->getRaw()->set($this->_id);
		return $this;
	}
	public function getId()
	{
		return $this->_id;
	}
	public function wait()
	{
		$this->getRaw()->wait(); //blocking
		return $this;
	}
	public function notify($all=false)
	{
		$this->get()->notify($all);
		return $this;
	}
	public function getRaw()
	{
		if ($this->_rawObj === null) {
			if ($this->getId() === null) {
				throw new \Exception("Missing sync ID");
			}
			$this->_rawObj	= new \parallel\Sync($this->_id);
		}
		return $this->_rawObj;
	}
	public function terminate()
	{
		if ($this->getTerminationStatus() === false) {
			$this->_termStatus	= true;
			$this->getParent()->removeSync($this);
			parent::terminate();
		}
	}
}