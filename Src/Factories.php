<?php
//© 2019 Martin Peter Madsen
namespace MTM\Async;

class Factories
{
	private static $_cStore=array();
	
	//USE: $aFact		= \MTM\Async\Factories::$METHOD_NAME();
	
	public static function getServices()
	{
		if (array_key_exists(__FUNCTION__, self::$_cStore) === false) {
			self::$_cStore[__FUNCTION__]	= new \MTM\Async\Factories\Services();
		}
		return self::$_cStore[__FUNCTION__];
	}
	public static function getThreading()
	{
		if (array_key_exists(__FUNCTION__, self::$_cStore) === false) {
			self::$_cStore[__FUNCTION__]	= new \MTM\Async\Factories\Threading();
		}
		return self::$_cStore[__FUNCTION__];
	}
	public static function getProcesses()
	{
		if (array_key_exists(__FUNCTION__, self::$_cStore) === false) {
			self::$_cStore[__FUNCTION__]	= new \MTM\Async\Factories\Processes();
		}
		return self::$_cStore[__FUNCTION__];
	}
}