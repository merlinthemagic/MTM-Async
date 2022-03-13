<?php
//© 2022 Martin Peter Madsen
namespace MTM\Async\Models\Processes\V1\Process;

abstract class Initialize extends Commands
{
	protected $_procFile=null;
	
	public function initialize()
	{
		if ($this->_isInit === false) {
			
			if ($this->getGuid() === null) {
				
				$filePath	= $this->getFilePath();
				if (is_readable($filePath) === false) {
					throw new \Exception("Cannot initialize, file is not readable or does not exist");
				} elseif (is_string($this->getClassName()) === false) {
					throw new \Exception("Class path is invalid or missing");
				} elseif (is_string($this->getMethodName()) === false) {
					throw new \Exception("Method name is invalid or missing");
				}
				
				$this->setGuid(\MTM\Utilities\Factories::getGuids()->getV4()->get(false));
				$this->initializeByObject();
				$this->_isInit	= true;
				
			} else {
				//pick up existing process
				//$data	= $this->readData("setup"); //fill the object with this data
				
				throw new \Exception("NOt ready to pick up persistent processes yet");
			}
			
		} else {
			throw new \Exception("Cannot initialize again");
		}
		return $this;
	}
	protected function initializeByObject()
	{
		$scriptPath			= array(MTM_ASYNC_BASE_PATH, "Models", "Processes", "V1", "Scripts", "LaunchByObject.php");
		$scriptPath			= realpath(implode(DIRECTORY_SEPARATOR, $scriptPath));
		
		//in this case the filepath is to be treated as a php boot strap
		$phpTool			= \MTM\Utilities\Factories::getSoftware()->getPhpTool();
		$phpPath			= $phpTool->getExecutablePath();
		
		$shellObj			= \MTM\Utilities\Factories::getProcesses()->getBashShell();
		$maxCmd				= $shellObj->getMaxArg();

		$this->_procFile	= sys_get_temp_dir().DIRECTORY_SEPARATOR.$this->getGuid();
		if (file_exists($this->_procFile) === true) {
			throw new \Exception("Process file already exists");
		} else {
			touch($this->_procFile);
		}
		
		$procData			= base64_encode(serialize(array("procFile" => $this->_procFile, "bootStrap" => $this->getFilePath(), "class" => $this->getClassName(), "method" => $this->getMethodName(), "args" => serialize($this->getArguments()))));
		file_put_contents($this->_procFile, "setup:|MTM|:".$procData."\n");
		
		$strCmd1			= "(";
		$strCmd1			.= " nohup sh -c '";
		$strCmd1			.= $phpPath." -f \"".$scriptPath."\" ".$this->getGuid()." ".$procData;
		$strCmd1			.= " ' & ) > /dev/null 2>&1; echo -en \"MtmAsyncOL\"";

		$cmdLen				= strlen($strCmd1);
		if ($cmdLen > $maxCmd) {
			//put it in a file/sh_mem and tell the process to pick it up from there
			throw new \Exception("Not handled for commands that exceed the shell max arg");
		}
		$shellObj->execute($strCmd1, "MtmAsyncOL");
		
		//find the pid of the process
		$strCmd2	= "ps -ef | grep -v grep | grep ".$this->getGuid()." | awk '{ print $2 }'; echo -en \"MtmAsyncPid\"";
		$tTime		= time() + 5;
		while (true) {
			$data		= $shellObj->execute($strCmd2, "MtmAsyncPid");
			if (preg_match("/([0-9]+)/", $data, $raw) === 1) {
				$this->_pId	= intval($raw[1]);
				break;
			} elseif ($tTime < time()) {
				throw new \Exception("Failed to determine pid of child process");
			} else {
				usleep(100000);
			}
		}
		$tTime		= time() + 5;
		while (true) {
			$data	= $this->readData("launching");
			if (preg_match("/([0-9]+)/", $data, $raw) === 1) {
				$pId	= intval($raw[1]);
				if ($pId !== $this->_pId) {
					throw new \Exception("Process launched with a different PId than we thought: ".$pId);
				} else {
					break;
				}
			} elseif ($tTime < time()) {
				$this->getReturn(true);
				throw new \Exception("Process failed to return launch data");
			} else {
				usleep(100000);
			}
		}
		return $this;
	}
}