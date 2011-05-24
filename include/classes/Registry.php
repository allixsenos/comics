<?php
/**********************************************************
* Registry object to hold application-global data
* @package		Registry
* @access		public
* @author 		Luka Kladaric <luka@kladaric.net>
* @copyright 	Luka Kladaric &copy; 2007
* This code is not in the public domain and must not be 
* copied or modified without authorisation by the author and copyright holders.
***********************************************************/

class Registry {
	// <singleton stuff>
	static private $thisInstance = null;

	// the constructor needs to be private, prevents direct instantiation
	private function __construct() {}
	
	static public function getInstance() {
		if (self::$thisInstance == null) {
			$className = __CLASS__;
			self::$thisInstance = new $className;
		}
		return self::$thisInstance;
	}
	// </singleton stuff>
	
	public $logobj = null;
	
	public function log ($component, $msg, $level = PEAR_LOG_INFO) {
		if (!is_null($this->logobj))
			$this->logobj->log("[{$component}] {$msg}", $level);
	}
}

if (function_exists('R'))
	die ('collision in Registry.php, R() is already defined!');
else {
	function R() {
		return Registry::getInstance();
	}
}
