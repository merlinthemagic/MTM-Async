<?php
//© 2019 Martin Peter Madsen
namespace MTM\Async\Factories;

class Threading extends Base
{
	public function getParallel()
	{
		if (array_key_exists(__FUNCTION__, $this->_cStore) === false) {
			if (extension_loaded("parallel") === true) {
				$rObj	= new \MTM\Async\Models\Threading\Parallel\Api();
				$this->_cStore[__FUNCTION__]	= $rObj;
			} else {
				throw new \Exception("Parallel extension not loaded");
			}
		}
		return $this->_cStore[__FUNCTION__];
	}
}