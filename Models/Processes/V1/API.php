<?php
//© 2022 Martin Peter Madsen
namespace MTM\Async\Models\Processes\V1;

class API
{
	protected $_procObjs=array();
	
	public function getNewProcess($filePath, $className=null, $methodName=null, $args=array())
	{
		$rObj	= new \MTM\Async\Models\Processes\V1\Process\Zstance();
		$rObj->setAPI($this)->setFilePath($filePath);
		$rObj->setClassName($className)->setMethodName($methodName);
		$rObj->setArguments($args);
		$this->_procObjs[$rObj->getGuid()]	= $rObj;
		return $rObj;
	}	
}