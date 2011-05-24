<html>
<head>
	<title><?=$r->pagetitle;?></title>
	<link rel="stylesheet" type="text/css" href="/style.css" />
	<!--[if IE]><link rel="stylesheet" type="text/css" href="/style-ie.css" /><![endif]-->
	<link rel="shortcut icon" href="/favicon.ico" type="image/vnd.microsoft.icon">
<? if ($r->pagerss) { ?>
	<link rel="alternate" type="application/rss+xml" title="daily feed" href="/rss1.php" />
	<link rel="alternate" type="application/rss+xml" title="alternative feed" href="/rss2.php" />
<? } ?>
</head>
<body>
<div class="container">