<?php

abstract class Strip_Search extends Strip_Base {
	
	public $type = 'search';
	protected $search_url = null;
	protected $search_pattern = null;

	public function __construct() {
		parent::__construct();
		
		$s_location = strftime($this->search_url, R()->today);
		$s_pattern = strftime($this->search_pattern, R()->today);
		
		$s_l_data = Util::cfgc($s_location);
		
		if (!trim($s_l_data)) {
			$this->log('failed to get search page', PEAR_LOG_WARNING);
		} else {
			if (preg_match($s_pattern, $s_l_data, $matches)) {
				$this->location = $matches[1];
			} else {
				$this->log('no match', PEAR_LOG_WARNING);
			}
		}
		
		
	}
}
