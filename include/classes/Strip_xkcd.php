<?php

class Strip_xkcd extends Strip_Search {
	
	public $name = 'xkcd';
	public $fileprefix = 'xkcd';
	
	protected $search_url = 'null';
	protected $search_pattern = '@<img src="(http://imgs.xkcd.com/comics/.+\.(png|jpg))"@Uis';

	protected $special_search_url = 'http://xkcd.com/rss.xml';
	protected $special_search_pattern = '@<item><title>[^<]+</title><link>([^<]+)</link><description>[^<]+</description><pubDate>%a, %d %b %Y [^<]+</pubDate><guid>[^<]+</guid></item>@Uis';
	
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
