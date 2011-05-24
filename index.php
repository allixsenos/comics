<?php

$anncookie = 'hideannouncement-comics20';
$annexpires = strtotime('2007-12-15');
$anntext = "Welcome to Comics 2.0. This is still in testing mode.<br />If you notice any bugs, contact info is at the bottom of the page.<br />Old archive will be imported soon.";

require_once 'env.php';

if (isset($_COOKIE[$anncookie]) and $_COOKIE[$anncookie])
	$hideann = true;
else
	$hideann = false;

$date = Util::arg_g('date');
if (!$date) $date = $r->dbc->getOne("SELECT date FROM comics ORDER BY date DESC LIMIT 1;");
$date = date('Y-m-d', strtotime($date));

if (Util::arg_g('hide', 0)) {
	setcookie($anncookie, 1, $annexpires);
	Util::httpredirect("?date={$date}");
}

if (time() > $annexpires)
	$hideann = true;

$cache = new Cache();

if (($data = $cache->get($date))==false) {
	$data = array();
	$data['strips'] = $r->dbc->getAll("SELECT * FROM comics WHERE date = ? ORDER BY lastmod DESC", null, array($date));
	$data['firstdate'] = $r->dbc->getOne("SELECT date FROM comics ORDER BY date ASC LIMIT 1;");
	$data['previousdate'] = $r->dbc->getOne("SELECT date FROM comics WHERE date < ? ORDER BY date DESC LIMIT 1;", null, array($date));
	$data['nextdate'] = $r->dbc->getOne("SELECT date FROM comics WHERE date > ? ORDER BY date ASC LIMIT 1;", null, array($date));
	$data['lastdate'] = $r->dbc->getOne("SELECT date FROM comics ORDER BY date DESC LIMIT 1;");
	
	$cache->save($data, $date);
}

$day = date('D', strtotime($date));

$r->pagetitle = "Daily Comics for {$day} {$date}";
$r->pagerss = true;

include 'head.php';

if (!$hideann)
	echo "<div class=\"announcement\">{$anntext} <a href=\"?date={$date}&hide=true\">hide this</a></div>";

nav($data,$date);

echo "<h1 style='clear:both;'>Daily Comics for {$day} {$date}</h1>";

$quicklinks = array('<a href="#">back to top</a>');
foreach ($data['strips'] as $s) {
	$quicklinks[] = '<a href="#'.$s['handler'].'">'.$s['comic'].'</a>';
}
echo $quicklinks = '<small>'.implode(' | ', $quicklinks) . '</small>';
echo '<br /><br />';

foreach ($data['strips'] as $s) {
	echo "<h2>{$s['comic']} <small><a name=\"{$s['handler']}\" href=\"?date={$date}#{$s['handler']}\" title=\"permalink\">#</a></small></h2>";
	echo "<p><img src='/data/{$s['filename']}' /><br />{$quicklinks}</p>";
}

echo '<br />';

nav($data,$date);

include 'foot.php';



/****/

function nav ($data,$date) {
	echo '<div style="clear:both;float:left;">';
	
	if ($data['previousdate']) {
		echo '<a href="?date='.$data['firstdate'].'" title="'.$data['firstdate'].'">&lt;&lt;</a> | <a href="?date='.$data['previousdate'].'">&larr; '.$data['previousdate'].'</a>';
	} else
		echo '&lt;&lt; | &larr; ' . $date;
	
	echo '</div><div style="float:right;">';
	
	if ($data['nextdate']) {
		echo '<a href="?date='.$data['nextdate'].'">'.$data['nextdate'].' &rarr;</a> | <a href="?" title="'.$data['lastdate'].'">&gt;&gt;</a>';
	} else
		echo $date . ' &rarr; | &gt;&gt;';
	
	echo '</div>';
	echo '<a href="/rss1.php" class="feed">daily feed</a> | <a href="/rss2.php" class="feed">alternative feed</a>';
}
