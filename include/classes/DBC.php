<?php
/**********************************************************
* Database helper class
* @package		DBC
* @access		public
* @author 		Luka Kladaric <luka@kladaric.net>
* @copyright 	Luka Kladaric &copy; 2007
* This code is not in the public domain and must not be 
* copied or modified without authorisation by the author and copyright holders.
***********************************************************/

class DBC {
	static function connect($db = null) {
		$options = array(
			//'persistent' => false,
			//'autofree' => false,
		);
		
		$dsn = array(
			'phptype'  => DB_TYPE,
			'hostspec' => DB_HOSTNAME,
			'username' => DB_USERNAME,
			'password' => DB_PASSWORD,
			'database' => is_null($db)?DB_DBNAME:$db,
		);
		
		$db = MDB2::connect($dsn,$options);
		if (PEAR::isError($db)) {
		    die($db->getMessage());
		}
		$db->setFetchMode(MDB2_FETCHMODE_ASSOC);
		$db->loadModule('Extended');
		return $db;
	}
}