<?
/**********************************************************
* LKLog
* @package		LKLog
* @access		public
* @author 		Luka Kladaric <luka@kladaric.net>
***********************************************************/

/**
 * LKLog
 * 
 * @access 		public
 **/

class LKLog {
	
	static function create() {
		$composite = Log::singleton('composite');

		$composite->addChild(Log::singleton('file', 'data/logs/'.date('Y-m/Y-m-d').'.log'));
		
		if (R()->isweb)
			$composite->addChild(Log::singleton('display'));
		else
			$composite->addChild(Log::singleton('console'));
			

		return $composite;
	}

}
