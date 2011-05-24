<?php

require_once 'env.php';

header('Content-type: application/rss+xml', true);

$cache = new Cache();

if (($strips = $cache->get('rss'))==false) {
	$strips = $r->dbc->getAll("SELECT * FROM comics WHERE date >= (SELECT date FROM comics GROUP BY date ORDER BY date DESC LIMIT 10,1) ORDER BY date desc, lastmod DESC");
	$cache->save($strips, 'rss');
}

$options = array(
	"indent"          => "    ",
	"linebreak"       => "\n",
	"typeHints"       => false,
	"addDecl"         => true,
	"encoding"        => "UTF-8",
	"rootName"        => "rss",
	"rootAttributes"  => array("version" => "2.0"),
	"defaultTagName"  => "item",
	"attributesArray" => "_attributes"
);


$xmls = new XML_Serializer($options);

$xml = array();

$xml['channel']['title'] = "AlliX's Daily Comics";
$xml['channel']['link'] = BASE_HREF;
$xml['channel']['description'] = "AlliX's daily comics - for private use only";
$xml['channel']['language'] = "en";
$xml['channel']['managingEditor'] = "allixsenos@gmail.com (Luka Kladaric)";
$xml['channel']['webMaster'] = "allixsenos@gmail.com (Luka Kladaric)";
$xml['channel']['docs'] = "http://blogs.law.harvard.edu/tech/rss";
$xml['channel']['lastBuildDate'] = date("r");

function rss_finish() {
	global $xml, $xmls;
	
	$xmls->serialize($xml);
	echo $xmls->getSerializedData();
}