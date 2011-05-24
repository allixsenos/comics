<?php

class Strip_Hagar extends Strip_Generate {
	
	public $name = 'Hagar the Horrible';
	public $fileprefix = 'hagar';
	
	public $referer = 'http://www.kingfeatures.com/features/comics/hagar/aboutMaina.php';
	
	public $force_ext = 'gif';
	public $minimum_size = 3072; // 3 kB
	
	protected $generate = 'http://est.rbma.com/content/Hagar_The_Horrible?date=%Y%m%d';
}
