<?php

require_once 'rss.inc.php';

$dd = array();

foreach ($strips as $s) {
	if (!isset($dd[$s['date']])) {
		$dd[$s['date']] = array(
			'title' => "Daily Comics for " . date('D', strtotime($s['date'])) . ' ' . $s['date'],
			'link' => BASE_HREF.'?date='.$s['date'],
			'guid' => BASE_HREF.'?date='.$s['date'],
			'description' => '',
			'pubDate' => date("r", $s['lastmod']),
		);
	}
	
	$dd[$s['date']]['description'] .= '<h2>'.$s['comic'].' <a href="'.BASE_HREF.'?date='.$s['date'].'#'.$s['handler'].'" title="permalink">#</a></h2>';
	$dd[$s['date']]['description'] .= '<img src="'.BASE_HREF.'data/'.$s['filename'].'" />';
	$dd[$s['date']]['description'] .= '<br /><br />';
}

foreach ($dd as $d)
	array_push($xml['channel'], $d);

rss_finish();