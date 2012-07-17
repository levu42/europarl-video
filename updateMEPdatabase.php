#!/usr/bin/php
<?php
$url = 'http://parltrack.euwiki.org/meps/';
$contents = file_get_contents($url);
preg_match_all('/a href="\/mep\/([^\/]+)"/mi', $contents, $pat);
$meps = json_decode(file_get_contents('meps.json'), true);
foreach($pat[1] as $name) {
	$url = 'http://parltrack.euwiki.org/mep/' . urlencode($name);
	$content = file_get_contents($url);
	preg_match('/\/meps\/en\/(\d+)\//mi', $content, $subpat);
	$meps[$subpat[1]] = $name;
}
file_put_contents('meps.json', json_encode($meps));
