<?php
/**********************************************************
* This code is not in the public domain and must not be 
* copied or modified without authorisation by the author and copyright holders.
* @package		Cache
* @author 		Luka Kladaric <luka@kladaric.net>
* @copyright 	Luka Kladaric &copy; 2007
***********************************************************/

/**
 * Cache
 * 
 * cache layer
 * 
 * @access 		public
 **/
 
class Cache extends Cache_Lite {

	function __construct($options = array()) {
		$defaultoptions = array(
			'cacheDir' => rtrim(CACHE_DIR, '/\\').'/', // TRAILING SLASH!!!!!
			'lifeTime' => CACHE_LIFETIME,
			'automaticSerialization' => true,
			'automaticCleaningFactor' => 200,
			'hashedDirectoryLevel' => 2,
		);
		
		$options = array_merge($defaultoptions, $options);
	
		parent::__construct($options);
	}

}
