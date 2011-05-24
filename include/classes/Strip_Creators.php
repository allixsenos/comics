<?php

abstract class Strip_Creators extends Strip_Search {
	
	protected $search_url = null;
	protected $search_pattern = '@<a href="(/comics/[0-9]+/[0-9]+_image.gif)"@Uis';
	public $baseurl = "http://featurepage.creators.com/";
	
	protected $special_search_pattern = '@<guid.*<link>([^<]+)</link>[^<]+<pubDate>%a, %d %b %Y 00:00:00 -0800</pubDate>@Uis';
	
	public function __construct() {
		$rss = Util::cfgc($this->special_search_url);
		
		$rss_pattern = strftime($this->special_search_pattern, R()->today);
		
		if (!trim($rss))
			$this->log('failed to get RSS!', PEAR_LOG_WARNING);
		else {
			if (preg_match($rss_pattern, $rss, $matches)) {
				$this->search_url = $matches[1];
			} else {
				$this->log('no match in RSS', PEAR_LOG_WARNING);
			}
		}
		
		if ($this->search_url)
			parent::__construct();
	}
}
