#!/usr/bin/php5
<?php
	$json = '';
	while(!feof(STDIN)) {
		$json .= fread(STDIN, 32 * 1024);
	}
	$data = json_decode($json, true);
	echo count($data);

