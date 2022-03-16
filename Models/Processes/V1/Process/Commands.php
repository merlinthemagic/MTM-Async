<?php
//© 2022 Martin Peter Madsen
namespace MTM\Async\Models\Processes\V1\Process;

abstract class Commands extends Base
{
	protected $_pId=null;
	protected $_pFp=null;
	protected $_isDone=false;
	protected $_data=null;
	protected $_ex=null;
	protected $_error=null;
	
	public function getPid()
	{
		return $this->_pId;
	}
	public function isRunning()
	{
		$pid	= $this->getPid();
		if ($pid !== null) {
			return \MTM\Utilities\Factories::getSoftware()->getOsTool()->pidRunning($pid);
		} else {
			return false;
		}
	}
	public function getFinal()
	{
		$data	= $this->readData("final");
		if ($data !== null) {
			return $data;
		} else {
			return null;
		}
	}
	public function getReturn($timeoutMs=-1, $throw=true)
	{
		if ($this->_isDone === false) {
			
			$timeFact	= \MTM\Utilities\Factories::getTime();
			$tTime		= ($timeFact->getMicroEpoch() + ($timeoutMs / 1000));
			while ($this->_isDone === false) {
				$data	= $this->readData("exception");
				if ($data === null) {
					$data	= $this->readData("return");
					if ($data !== null) {
						$this->_data	= $data;
						$this->_isDone	= true;
					} elseif ($timeFact->getMicroEpoch() > $tTime) {
						break;
					} else {
						usleep(100000);
					}
				} else {
					$this->_data	= $data["trace"];
					$this->_ex		= new \Exception($data["message"], intval($data["code"]));
					$this->_isDone	= true;
				}
			}
		}
		if ($this->_ex !== null && $throw === true) {
			throw $this->_ex;
		} else {
			return $this->_data;
		}
	}
	protected function readData($type="")
	{
		if ($this->_pFp === null) {
			$this->_pFp		= fopen($this->_procFile, "r");
		} else {
			rewind($this->_pFp);
		}
		$find		= $type.":|MTM|:";
		while(feof($this->_pFp) === false){
			$line	= fgets($this->_pFp);
			if (strpos($line, $find) === 0) {
				$line		= substr($line, strlen($find));
				if ($line != "") {
					$line	= base64_decode($line);
					if ($line != "") {
						$line	= unserialize($line);
						if ($line != "") {
							return $line;
						} else {
							throw new \Exception("Failed to un serialize reading type: ".$type);
						}
					} else {
						throw new \Exception("Failed to decode reading type: ".$type);
					}
				} else {
					//no data, but line does exist
					return "";
				}
			}
		}
		return null;
	}
}