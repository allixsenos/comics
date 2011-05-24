<?php

$strips_toload = array('UF', 'PA', 'CAD', 'Dilbert', 'Garfield', 'WizardOfId', 'BC', 'WorkingDaze', 'PhD', 'xkcd', 'ExtraLife', 'Hagar', 'CAH', 'WTR');
$strips_loaded = array();

require_once 'env.php';

R()->today = (Util::arg_g('date'))?strtotime(Util::arg_g('date')):time();

foreach ($strips_toload as $s) {
	if (@include_once ("classes/Strip_{$s}.php")) {
		$strips_loaded[] = "Strip_{$s}";
	} else
		R()->log('core', "failed to load Strip_{$s}", PEAR_LOG_WARNING);
}

R()->log('core', 'Loaded ' . implode(', ', $strips_loaded));

R()->log('core', 'starting for ' . date('Y-m-d', R()->today));

if (Util::arg_g('comics')) {
	$comics_to_process = array_intersect($strips_loaded, explode(',', Util::arg_g('comics')));
} else {
	$comics_to_process = $strips_loaded;
}

R()->log('core', 'Getting ' . implode(', ', $comics_to_process));

foreach ($comics_to_process as $s) {
	flush();
	
	set_time_limit(30);
	
	$x = new $s();
	
	$x->get();
}

$cache = new Cache();
$cache->clean();

R()->log('core', 'finished for ' . date('Y-m-d', R()->today));