<?php

class Strip_WTR extends Strip_Search {
	
	public $name = 'We The Robots';
	public $fileprefix = 'WTR';
	
	protected $search_url = 'http://feeds.feedburner.com/WeTheRobots';
	protected $search_pattern = '@<img src=["\'](http://www.wetherobots.com/comics/%Y-%m-%d-.*)["\']@Uis';

}
