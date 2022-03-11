<?php
//© 2022 Martin Peter Madsen
namespace MTM\Async\Models\Processes\V1\Process;

abstract class Initialize extends Commands
{
	protected $_isInit=false;
	protected $_childPid=null;
	protected $_childLid=null;
	
	public function initialize()
	{
		if ($this->_isInit === false) {
			$filePath	= $this->getFilePath();
			if (is_readable($filePath) === false) {
				throw new \Exception("Cannot initialize, file is not readable or does not exist");
			}
			$className		= $this->getClassName();
			if ($className === null) {
				//build a wrapper that lets us launch anything...
				throw new \Exception("Not handled yet without object");
			} else {
				$this->initializeByObject();
			}
			$this->_isInit	= true;
		}
		return $this;
	}
	protected function initializeByObject()
	{
		$scriptPath		= array(MTM_ASYNC_BASE_PATH, "Models", "Processes", "V1", "Scripts", "LaunchByObject.php");
		$scriptPath		= realpath(implode(DIRECTORY_SEPARATOR, $scriptPath));
		
		//in this case the filepath is to be treated as a php boot strap
		$phpTool		= \MTM\Utilities\Factories::getSoftware()->getPhpTool();
		$phpPath		= $phpTool->getExecutablePath();
		
		$shellObj		= \MTM\Utilities\Factories::getProcesses()->getBashShell();
		$maxCmd			= $shellObj->getMaxArg();

		$filePath		= $this->getFilePath();
		$className		= $this->getClassName();
		$methodName		= $this->getMethodName();
		$methodArgs		= $this->getArguments();

		$this->_childLid	= \MTM\Utilities\Factories::getStrings()->getRandomByRegex(24, "A-Za-z0-9");; //unique id so we can find the process again
		$strCmd				= "(";
		$strCmd				.= " nohup sh -c '";
		$strCmd				.= $phpPath." -f \"".$scriptPath."\" ".$this->_childLid." ".base64_encode($filePath)." ".base64_encode($className)." ".base64_encode($methodName)." ".base64_encode(serialize($methodArgs));
		$strCmd				.= " ' & ) > /dev/null 2>&1; echo -en \"MtmAsyncOL\"";
		
		$cmdLen				= strlen($strCmd);
		if ($cmdLen > $maxCmd) {
			//put it in a file and tell the process to pick it up from there
			throw new \Exception("Not handled for commands that exceed the shell max arg");
		}
		$shellObj->execute($strCmd, "MtmAsyncOL");
		
		//find the pid of the process
		$strCmd		= "ps -ef | grep -v grep | grep ".$this->_childLid." | awk '{ print $2 }'; echo -en \"MtmAsyncPid\"";
		$tTime		= time() + 5;
		while (true) {
			$data		= $shellObj->execute($strCmd, "MtmAsyncPid");
			if (preg_match("/([0-9]+)/", $data, $raw) === 1) {
				$this->_childPid	= intval($raw[1]);
				break;
			} elseif ($tTime < time()) {
				throw new \Exception("Failed to determine pid of child process");
			} else {
				usleep(100000);
			}
		}
		return $this;
	}
}