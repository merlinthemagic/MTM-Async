<?php
//© 2019 Martin Peter Madsen
namespace MTM\Async\Factories;

class Services extends Base
{
	public function getLoop()
	{
		//simple loop
		if (array_key_exists(__FUNCTION__, $this->_s) === false) {
			$rObj	= new \MTM\Async\Models\Services\Loop();
			//override the error call back on return
			$rObj->setErrorCallback($this, "defaultErrorCatch");
			$this->_s[__FUNCTION__]	= $rObj;
		}
		return $this->_s[__FUNCTION__];
	}
	public function defaultErrorCatch($subObj, $e)
	{
		throw $e;
	}
}