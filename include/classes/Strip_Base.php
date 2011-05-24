<?php

abstract class Strip_Base {
	public $pref = 'data/';
	
	public $name = null;
	public $fileprefix = null;
	
	public $type = null;
	
	public $location = null;
	public $referer = null;
	public $baseurl = null;
	public $force_ext = null;
	public $minimum_size = 0;
	
	public $data = null;
	public $target_file = null;
	
	public function __construct() {}
	
	public function get() {
		if (is_null($this->location)) {
			$this->log('no location', PEAR_LOG_WARNING);
			return false;
		}
		
		if ($this->baseurl)
			$this->location = $this->baseurl . $this->location;
		
		$this->log('trying ' . $this->location);
		
		if (!($this->data = Util::cfgc($this->location, $this->referer))) {
			$this->log('download failed', PEAR_LOG_WARNING);
			return false;
		}
		
		if (!$this->check()) {
			$this->log("doesn't check out", PEAR_LOG_WARNING);
			return false;
		}
		
		$this->log("download OK, data appears OK");
		
		$this->target_file = $this->get_target();
		
		if (file_exists($this->pref.$this->target_file)) {
			if ($this->data == file_get_contents($this->pref.$this->target_file)) {
				$this->log('target file already exists but no change detected, skipping');
				$this->save_db(true); /* save to db without updating if exists */
				return false;
			} else {
				$this->log('target file already exists but is different. clobbering!');
			}
		}
		
		$this->save_file();
		$this->save_db();
		
		return true;
	}
	
	public function check() {
		if (is_null($this->data))
			return false;
		
		if (empty($this->data))
			return false;
		
		if (trim($this->data) == "")
			return false;
		
		if (stristr($this->data, '<html')!==false)
			return false;
		
		if ($this->minimum_size)
			if (strlen($this->data)<$this->minimum_size)
				return false;
		
		return true;
	}
	
	public function get_target() {
		$pathinfo = pathinfo($this->location);
		$ext = ($this->force_ext)?$this->force_ext:$pathinfo['extension'];

		$today = R()->today;
		$comic = $this->fileprefix;
		
		$filename = strftime("comics/{$comic}/%Y/%Y%m/{$comic}-%Y%m%d.{$ext}", $today);
		
		$this->log("target filename: {$filename}");
		
		return $filename;
	}
	
	public function save_file() {
		$this->ensure_dir_exists(dirname($this->pref.$this->target_file));
		file_put_contents($this->pref.$this->target_file, $this->data);
	}
	
	public function save_db($no_update = false) {
		$dbc = R()->dbc;
		
		if ($no_update) {
			$sql = "INSERT IGNORE INTO comics (`comic`, `handler`, `date`, `lastmod`, `filename`) VALUES (?,?,?,?,?);";
		} else {
			$sql = "INSERT INTO comics (`comic`, `handler`, `date`, `lastmod`, `filename`) VALUES (?,?,?,?,?)
				ON DUPLICATE KEY UPDATE `lastmod` = VALUES(lastmod), `filename` = VALUES(filename);";
		}
		
		$dbc->getAll($sql, null, array($this->name, $this->fileprefix, date('Y-m-d', R()->today), time(), $this->target_file));
	}
	
	
	public function log($msg, $level = PEAR_LOG_INFO) {
		R()->log($this->name, $msg, $level);
	}
	
	public function ensure_dir_exists($dir,$mode = 0777) {
		if (!is_dir(dirname($dir)))
			$this->ensure_dir_exists(dirname($dir), $mode);
		
		if (!file_exists($dir)) {
			mkdir($dir);
		}
		return(chmod($dir,$mode));
	}

}
