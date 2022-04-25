<?php
//© 2022 Martin Peter Madsen
namespace MTM\Async\Factories;

class Processes extends Base
{
	public function getV1()
	{
		if (array_key_exists(__FUNCTION__, $this->_s) === false) {
			$rObj	= new \MTM\Async\Models\Processes\V1\API();
			$this->_s[__FUNCTION__]	= $rObj;
		}
		return $this->_s[__FUNCTION__];
	}
}