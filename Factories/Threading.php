<?php
//© 2019 Martin Peter Madsen
namespace MTM\Async\Factories;

class Threading extends Base
{
	public function getParallel()
	{
		if (array_key_exists(__FUNCTION__, $this->_s) === false) {
			if (extension_loaded("parallel") === true) {
				$rObj	= new \MTM\Async\Models\Threading\Parallel\Api();
				$this->_s[__FUNCTION__]	= $rObj;
			} else {
				//is the extension added under php.ini?
				//extension=/usr/lib/php/extensions/no-debug-zts-20180731/parallel.so
				throw new \Exception("Parallel extension not loaded");
			}
		}
		return $this->_s[__FUNCTION__];
	}
}