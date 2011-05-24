<?php

abstract class Strip_Generate extends Strip_Base {
	
	public $type = 'generate';
	protected $generate = null;

	public function __construct() {
		parent::__construct();
		$this->location = strftime($this->generate, R()->today);
	}
}
