<?php

class Strip_CAH extends Strip_Search {
	
	public $name = 'Cyanide and Happiness';
	public $fileprefix = 'cah';
	
	protected $search_url = 'http://www.explosm.net/comics';
	protected $search_pattern = '@%m\.%d\.%Y.*src="(http://www.explosm.net/db/files/Comics/.*\..+)"@Uis';
}
