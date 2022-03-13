<?php
//© 2022 Martin Peter Madsen
namespace MTM\Async\Models\Processes\V1\Process;

abstract class Terminate extends Initialize
{
	public function terminate()
	{
		if ($this->_isTerm === false) {
			$this->_isTerm	= true;
			
			if ($this->_pFp !== null) {
				fclose($this->_pFp);
				$this->_pFp	= null;
			}
		
			if ($this->getPersistence() === false) {
				if (file_exists($this->_procFile) === true) {
					unlink($this->_procFile);
				}
				if ($this->isRunning() === true) {
					\MTM\Utilities\Factories::getSoftware()->getOsTool()->sigKillPid($this->getPid());
				}
			}
		}
	}
}