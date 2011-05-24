<?php

abstract class Strip_UnitedMedia extends Strip_Search {
	public $um = '';
	
	protected $search_url = 'http://www.unitedmedia.com/comics/{um}/archive/{um}-%Y%m%d.html';
	protected $search_pattern = '@<img.+src="(/comics/{um}/archive/images/{um}.+)"@Uis';
	
	public $baseurl = 'http://www.unitedmedia.com';

	public function __construct() {
		$this->search_url = str_replace("{um}", $this->um, $this->search_url);
		$this->search_pattern = str_replace("{um}", $this->um, $this->search_pattern);
		
		parent::__construct();
	}

}
