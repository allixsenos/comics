<?php

class Strip_UF extends Strip_Search {
	
	public $name = 'User Friendly';
	public $fileprefix = 'uf';
	
	protected $search_url = 'http://ars.userfriendly.org/cartoons/?id=%Y%m%d&mode=classic';
	protected $search_pattern = '@<img.+src="(http://www\.userfriendly\.org/cartoons/archives/%y.+/uf.+\.gif)"@Uis';

}
