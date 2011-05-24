<?php

class Strip_bLaugh extends Strip_Search {
	
	public $name = 'bLaugh';
	public $fileprefix = 'blaugh';
	
	protected $search_url = 'http://blaugh.com/';
	protected $search_pattern = '@<IMG.+SRC="(http://blaugh.com/cartoons/%y%m%d.+)"@Uis';
}
