<?
/**********************************************************
* Util
* @package		Util
* @access		public
* @author 		Luka Kladaric <luka@kladaric.net>
***********************************************************/

/**
 * Util
 * 
 * general purpose functions
 * 
 * @access 		public
 **/

class Util {

	static function time_to_s($time) {
		$sep = "[\.\:]";
		
		if (is_array($time)) {
			// array format
			if (count($time) != 3) return 0;
			$h = $time[0];
			$m = $time[1];
			$s = $time[2];
		} elseif (preg_match("/^([0-9]+)$sep([0-9]+)$sep([0-9]+)$/",$time,$x)) {
			// hh:mm:ss format
			$h = $x[1];
			$m = $x[2];
			$s = $x[3];
		} elseif (preg_match("/^([0-9]+)$sep([0-9]+)$/",$time,$x)) {
			// mm:ss format
			$h = 0;
			$m = $x[1];
			$s = $x[2];
		} elseif (preg_match("/^([0-9]+)$/",$time,$x)) {
			// ss format
			$h = 0;
			$m = 0;
			$s = $x[1];
		} else {
			return 0;
		}
		
		return $h*3600 + $m*60 + $s;
	}
	
	static function time_to_ms ($time) {
		$time = self::time_to_s($time);
		
		$m = $time/60;
		$s = $time%60;
		
		return sprintf("%02d:%02d", $m, $s);
	}
	
    static function time_to_hms($time) {
    	$time = self::time_to_hms_a($time);

		return sprintf('%02d:%02d:%02d', $time[0], $time[1], $time[2]);
	}
	
	static function time_to_hms_a ($time) {
		$time = self::time_to_s($time);
		
		$durn_hh = (int)($time / 3600);									// calculate hours in total (3600 seconds in an hour)		
		$durn_mm = (int)(($time - ($durn_hh * 3600)) / 60);				// calculate remainder and split into minutes		
		$durn_ss = (int)($time - ($durn_hh * 3600) - ($durn_mm * 60));	// calculate remainder and split into seconds
		
		return array($durn_hh, $durn_mm, $durn_ss);	
	}
	
	static function midnight_today () {
		return mktime(0,0,0);
	}

	static function round($num) {
		return (ceil($num*100))/100;
	}
	
	static function token() {
		return uniqid(md5(rand()), true);
	}
	
	static function arg($name, $default = null) {
		return (self::arg_p($name, self::arg_g($name, $default)));
	}
	
	static function arg_g($name, $default = null) {
		return (isset($_GET[$name]))?$_GET[$name]:$default;
	}
	
	static function arg_p($name, $default = null) {
		return (isset($_POST[$name]))?$_POST[$name]:$default;
	}
	
	static function httpredirect($url) {
		if (!headers_sent()) {
			header("Location: {$url}");
		} else {
			echo "<a href=\"{$url}\">click here to continue</a>";
		}
		die();
	}
	
	static function httpstatus($code) {
		if (headers_sent())
			return false;

		$s = array(
			'200' => 'OK',
			'403' => 'Forbidden',
			'404' => 'File Not Found',
			'500' => 'Internal Server Error',
			
		);
		
		if (isset($s[$code])) {
			$desc = $s[$code];
			header("HTTP/1.1 {$code} {$desc}");
			header("Status:  {$code} {$desc}");
		} else {
			header(' ', true, $code);
		}
	}
	
	static function sql_placeholders ($items) {
		return trim(str_repeat("?,", count($items)), ",");
	}
	
	static function filesize_readable ($size, $retstring = null) {
		// adapted from code at http://aidanlister.com/repos/v/function.size_readable.php
		$sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		if ($retstring === null) { $retstring = '%01.2f %s'; }
		$lastsizestring = end($sizes);
		foreach ($sizes as $sizestring) {
			if ($size < 1024) { break; }
			if ($sizestring != $lastsizestring) { $size /= 1024; }
		}
		if ($sizestring == $sizes[0]) { $retstring = '%01d %s'; } // Bytes aren't normally fractional
		return sprintf($retstring, $size, $sizestring);
	}
	
	static function list_folder ($folder, $weeds = array('.', '..', '.svn')) {
		return array_diff(scandir($folder), $weeds);
	}
	
	static function move_overwrite ($src, $dest) {
		if (!is_readable($src)) {
			error_log ("source is not a readable file");
			return false;
		}
		
		if (is_file($dest)) {
			if (is_writable($dest)) {
				if (!unlink ($dest)) {
					error_log ("could not delete existing destination (1)");
					return false;
				}
			} else {
				error_log ("could not delete existing destination (2)");
				return false;
			}
		}
		
		return rename ($src, $dest);
	}
	
	static function kill_magic_quotes() {
		if (get_magic_quotes_gpc()) {
		   $_GET = self::undoMagicQuotes($_GET);
		   $_POST = self::undoMagicQuotes($_POST);
		   $_COOKIE = self::undoMagicQuotes($_COOKIE);
		   $_REQUEST = self::undoMagicQuotes($_REQUEST);
		}
	}
	
	static function undoMagicQuotes($array, $topLevel=true) {
	   $newArray = array();
	   foreach($array as $key => $value) {
	       if (!$topLevel) {
	           $key = stripslashes($key);
	       }
	       if (is_array($value)) {
	           $newArray[$key] = self::undoMagicQuotes($value, false);
	       }
	       else {
	           $newArray[$key] = stripslashes($value);
	       }
	   }
	   return $newArray;
	}
	
	static function cfgc($url, $referer=null) {
		//Initialize the Curl session
		$ch = curl_init();
		
		//Set curl to return the data instead of printing it to the browser.
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//Set the URL
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

		if ($referer)
			curl_setopt($ch, CURLOPT_REFERER, $referer);

		//Execute the fetch
		$data = curl_exec($ch);
		//Close the connection
		curl_close($ch);
		
		//$data now contains the contents of $URL
		return $data;
	}	

}
