<?

putenv ('TZ=Europe/Zagreb'); // time zone
mktime (0,0,0,1,1,1970); // hack to actually *apply* time zone

ini_set("session.gc_maxlifetime",44400);
ini_set("session.use_only_cookies",1);

require_once 'configuration.php';

// include PEAR components
require 'Log.php';
require 'MDB2.php';
require 'Cache/Lite.php';
require 'XML/Serializer.php';


// include local components
require 'classes/LKLog.php';
require 'classes/DBC.php';
require 'classes/Registry.php';
require 'classes/Util.php';
require 'classes/Cache.php';

// include Strips
require 'classes/Strip_Base.php';
require 'classes/Strip_Generate.php';
require 'classes/Strip_Search.php';
require 'classes/Strip_Creators.php';
require 'classes/Strip_UnitedMedia.php';


Util::kill_magic_quotes(); // get rid of magic quotes

if ('cli' == php_sapi_name()) {
	// CLI specific stuff
	$isweb = false;
} else {
	// web specific stuff
	session_start();
	$isweb = true;
}


// initialize objects
$r = R(); // the Registry object, to hold all our app-wide data
$r->approot = $approot;
$r->isweb = $isweb;
$r->logobj = LKLog::create();
$r->dbc = DBC::connect();

$r->pagetitle = '';
$r->pagerss = false;






function print_var ($var) {
	if (is_string($var)) 
		return ('"'.str_replace (array("\x00", "\x0a", "\x0d", "\x1a", "\x09"), array('\0', '\n', '\r', '\Z', '\t'), $var) .'"');
	elseif (is_bool ($var)) {
		if ($var)
			return ('true');
		else
			return ('false');
	} elseif(is_array ($var)) {
		$result = 'array (';
		$comma = '';
		foreach ($var as $key => $val) {
			$result .= $comma.print_var ($key) .' => '.print_var ($val);
			$comma = ', ';
		}
		$result .= ') ';
		return ($result);
	}
	
	return (var_export($var, true)); // anything else, just let php try to print it
}

function trace ($msg) {
	echo "<pre>\n";
	
	//var_export (debug_backtrace()); echo "</pre>\n"; return;    // this line shows what is going on underneath
	
	$trace = array_reverse (debug_backtrace());
	$indent = '';
	$func = '';
	
	echo $msg."\n";
	
	foreach ($trace as $val) {
		echo $indent.$val['file'].' on line '.$val['line'];
		
		if ($func)
			echo ' in function '.$func;
	   
		if ($val['function'] == 'include' ||
			$val['function'] == 'require' ||
			$val['function'] == 'include_once' ||
			$val['function'] == 'require_once')
			$func = '';
		else {
			$func = $val['function'].'(';
			
			if (isset($val['args'][0])) {
				$func .= ' ';
				$comma = '';
				foreach ($val['args'] as $val) {
					$func .= $comma.print_var ($val);
					$comma = ', ';
				}
				$func .= ' ';
			}
			$func .= ')';
		}
		
		echo "\n";
		
		$indent .= "\t";
	}
	
	echo "</pre>\n";
}

function table_dump ($table) {
	if (!is_array($table) or (count($table)==0))
		return;
	
	echo "<table style=\"border-collapse: collapse;\" cellpadding=3 border=1>\n";
	echo "\t<tr>";
	foreach (array_keys($table[0]) as $colname) {
		echo "<th>{$colname}</th>";
	}
	echo "</tr>\n";
	foreach ($table as $row => $cols) {
		echo "\t<tr>";
		foreach ($cols as $coldata) {
			echo "<td>{$coldata}</td>";
		}
		echo "</tr>\n";
	}
	echo "</table>\n";
}

/**
 * validate_email
 * check if an email address is valid (regexp + DNS) (DNS check disabled on windows)
 *
 * @access	public
 * @param	string	$email	email address to check
 * @return	bool
 */
function validate_email ($email) {
//	$exp = "^[a-z\'0-9]+([._-][a-z\'0-9]+)*@([a-z0-9]+([._-][a-z0-9]+))+$";
	$exp = "^[-!#$%&'*+/0-9=?A-Z^_a-z{|}~](\.?[-!#$%&'*+/0-9=?A-Z^_a-z{|}~])*@[a-zA-Z](-?[a-zA-Z0-9])*(\.[a-zA-Z](-?[a-zA-Z0-9])*)+$";
	
	if (eregi($exp,$email)) {
		if (!function_exists('checkdnsrr')) // on a Windows box, checkdnsrr() is not available so the DNS check is not performed
			return true;
		if (
			(checkdnsrr(array_pop(explode("@",$email)),"MX"))
				or
			(checkdnsrr(array_pop(explode("@",$email)),"A"))
			) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

/**
 * get_foreign
 * returns linked data in an ID-indexed array
 *
 * @access	public
 * @param	string	$query	SELECT query to execute
 * @param	string	$id		(optional) name of ID field, default 'id'
 * @return	array[mixed]
 */
function get_foreign($query = '', $idfield = 'id') {
	$return = array();

	$registry = Registry::getInstance();
	$dbc = $registry->get('dbc');

	$tmp = $dbc->getAll($query);

	if (count($tmp))
		foreach ($tmp as $c)
			$return[$c[$idfield]] = $c;

	return $return;
}

/**
 * b64e
 * returns URI-safe base64-encoded data
 *
 * @access	public
 * @param	string	$string	input data to encode
 * @return	string
 */
function b64e($string) {
	$data = base64_encode($string);
	$data = str_replace(array('+','/','='),array('-','_',''),$data);
	return $data;
}

/**
 * b64d
 * returns decoded URI-safe-base64-encoded data
 *
 * @access	public
 * @param	string	$string	b64e data to decode
 * @return	string
 */
function b64d($string) {
	$data = str_replace(array('-','_'),array('+','/'),$string);
	$mod4 = strlen($data) % 4;
	if ($mod4) {
		$data .= substr('====', $mod4);
	}
	return base64_decode($data);
}

/**
 * mysql_enum_values
 * returns list of possible values in a MySQL ENUM or SET field 
 *
 * @access	public
 * @param	object	$dbc	database connection
 * @param	string	$table	table name
 * @param	string	$field	field name
 * @return	string
 */
function mysql_enum_values($dbc, $table, $field, $prependempty = false) {
	$x = $dbc->getCol("DESCRIBE {$table} {$field};",1);
	
	if (count($x)) {
		$y = $x[0];
		
		$y = str_replace(array("set('", "enum('", "')"), '', $y);
		
		$elements = split("','", $y);
		
		if ($prependempty)
			$elements = array_merge(array(''), $elements);
		
		return $elements;
	} else return array();
}

function filterArray($in, $allowedFields = array()) {
	if (!is_array($in))
		return array();
	
	return array_intersect_key($in, array_fill_keys($allowedFields, null));
}

function pre_var_dump() {
	echo "<pre>";
	
	foreach (func_get_args() as $k=>$v) {
		var_dump ($v);
	}
	
	echo "</pre>";
}
