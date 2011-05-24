<?php

require_once 'env.php';

$cache = new Cache();

if (($data = $cache->get('stats'))==false) {
    $data = array();

    $data['general'] = $r->dbc->getRow("SELECT COUNT(DISTINCT handler) handlers, COUNT(DISTINCT date) dates, COUNT(DISTINCT id) comics FROM comics;");
    $data['comictypes'] = $r->dbc->getAll("SELECT handler,comic FROM comics GROUP BY handler;");
    $data['oldestcomic'] = $r->dbc->getRow("SELECT * FROM comics ORDER BY date ASC, lastmod ASC LIMIT 1;");
    $data['newestcomic'] = $r->dbc->getRow("SELECT * FROM comics ORDER BY date DESC, lastmod DESC LIMIT 1;");
    $data['mostcomics'] = $r->dbc->getAll("SELECT DISTINCT (handler), comic, COUNT(DISTINCT id) count FROM comics GROUP BY comic ORDER BY count DESC;");
    $data['busiestday'] = $r->dbc->getRow("SELECT DISTINCT (date), COUNT(DISTINCT id) count FROM comics GROUP BY date ORDER BY count DESC, date DESC LIMIT 1;");
    $data['latestcomics'] = $r->dbc->getAssoc("SELECT comic, MAX(date) max_date FROM comics GROUP BY comic ORDER BY max_date DESC;");
    
    $cache->save($data, 'stats');
}

$r->pagetitle = "Daily Comics - stats";
$r->pagerss = false;

include 'head.php';

echo "<h1>{$r->pagetitle}</h1><small><a href=\"/\">back to comics</a></small>";

?>

<p>I'm tracking <b><?=$data['general']['handlers'];?></b> different comic types with <b><?=$data['general']['comics'];?></b> individual comics over <b><?=$data['general']['dates'];?></b> days, which means a <b><?=printf('%01.2f', $data['general']['comics']/$data['general']['dates']);?></b> comics per day average. Busiest day ever was <b><?=$data['busiestday']['date'];?></b> with <b><?=$data['busiestday']['count'];?></b> comics. Every day I try to get
    <? $ctc = count($data['comictypes']); foreach ($data['comictypes'] as $k=>$d) {
        if ($k) {echo ($k == $ctc-1)?' and ':', ';}
        echo "<b>{$d['comic']}</b>";}
    ?> for you to enjoy. By type, I have the most comics from 
    <? $ctc = count($data['mostcomics']); foreach ($data['mostcomics'] as $k=>$d) {
        if ($k) {echo ($k == $ctc-1)?' and ':', ';}
        echo "<b>{$d['comic']} ({$d['count']})</b>";}
    ?>.</p>

<p>
    <table>
        <tr><th>comic</th><th>last downloaded</th></tr>
    <? foreach ($data['latestcomics'] as $comic=>$max_date) { ?>
        <tr><td><?=$comic?></td><td><?=$max_date?></td></tr>
    <? } ?>
    </table>
    
</p>

<p>The oldest comic I'm aware of is <b><?=$data['oldestcomic']['comic'];?></b> from <b><?=$data['oldestcomic']['date'];?></b>. It went something like this:<br /><img src="/data/<?=$data['oldestcomic']['filename'];?>" /></p>

<p>The newest comic I got for you is <b><?=$data['newestcomic']['comic'];?></b> for <b><?=$data['newestcomic']['date'];?></b> and it looks like this:<br /><img src="/data/<?=$data['newestcomic']['filename'];?>" /></p>

<small><a href="/">back to comics</a></small>

<?

include 'foot.php';