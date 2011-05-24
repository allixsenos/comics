<?php

$approot = dirname(__FILE__);
chdir($approot); // force current working directory to app root

$config_key = trim(@file_get_contents('data/config-key'), " \t\n\r");

if (PHP_OS == "WIN32" || PHP_OS == "WINNT") {
	$approot = str_replace('\\', '/', $approot); // pathfix for windows
    $path_separator = ";";
} else {
    $path_separator = ":";
}

$path_segments_os = array(); // OS-specific paths (just declare var, in case some config segment forgets)

if ($config_key == '') {
	die('Fatal error: configuration key not found');
}

// server-agnostic settings
define('CACHE_DIR', $approot.'/data/cachetmp/');
define('DB_TYPE', 'mysql');

switch ($config_key) {
	case 'win-luka':
		define("CACHE_LIFETIME", 0);
		$path_segments_os = array("c:/wamp/php/pear/smarty/"); // OS-specific paths
		define("DB_PREFIX", "");
		define("DB_HOSTNAME", "localhost");
		define("DB_DBNAME", "allix_comics");
		define("DB_USERNAME", "root");
		define("DB_PASSWORD", "");
		define("BASE_HREF", 'http://comics.local/');
		break;
	case 'site5-comics.allixsenos.net':
		define("CACHE_LIFETIME", null);
		$acc_base = '/home/allix'; // base for external libraries
		$path_segments_os = array($acc_base . "/pear/lib/php"); // OS-specific paths
		define("DB_PREFIX", "allix_");
		define("DB_HOSTNAME", "localhost");
		define("DB_DBNAME", DB_PREFIX . "comics");
		define("DB_USERNAME", DB_PREFIX . "comics");
		define("DB_PASSWORD", "");
		define("BASE_HREF", 'http://comics.allixsenos.net/');
		break;
	default:
		die('Fatal error: unknown configuration key ' . $config_key);
}


$path_segments = array(".", "{$approot}", "{$approot}/include/"); // core include paths

// get default include path -- NOT CONFIGURABLE
$def_include_path = explode($path_separator, trim(ini_get('include_path'), '.'.$path_separator));
// produce final include path array -- NOT CONFIGURABLE
$include_path_final = array_merge($path_segments, $path_segments_os, $def_include_path);
// set include path -- NOT CONFIGURABLE
ini_set("include_path", implode($path_separator, $include_path_final));

