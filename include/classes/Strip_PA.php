<?php

class Strip_PA extends Strip_Generate {
	
	public $name = 'Penny Arcade';
	public $fileprefix = 'penny-arcade';
	
	protected $generate = 'http://www.penny-arcade.com/images/%Y/%Y%m%d.jpg';
	public $minimum_size = 2048; // 3 kB
}
