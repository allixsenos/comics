<?php

require_once 'rss.inc.php';

foreach ($strips as $s) {
	$x = array();
	
	$x['title'] = $s['comic'] . ' for ' . date('D', strtotime($s['date'])) . ' ' . $s['date'];
	$x['link'] = BASE_HREF.'?date='.$s['date'].'#'.$s['handler'];
	$x['description'] = '<img src="'.BASE_HREF.'data/'.$s['filename'].'" />';
	$x['pubDate'] = date("r", $s['lastmod']);
	$x['guid'] = BASE_HREF.'?date='.$s['date'].'#'.$s['handler'];
	
	$xml['channel'][] = $x;
}

rss_finish();