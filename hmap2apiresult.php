#!/usr/bin/php5
<?php
	$json = '';
	while(!feof(STDIN)) {
		$json .= fread(STDIN, 32 * 1024);
	}
	$data = json_decode($json, true);
	$result = array();
	$keys = array_keys($data);
	foreach($data[$keys[0]] as $k=>$_) {
		$result[$data['hmapId'][$k]] = array(
			"title" => "{$data['hmapNameInterv'][$k]} ({$data['hmapRoleInterv'][$k]})",
			"author" => $data['hmapNameInterv'][$k],
			"url" => $data['hmapUrlInterv'][$k],
			"attributes" => array (
				"startInterv" => $data['hmapStartInterv'][$k],
				"endInterv" => $data['hmapEndInterv'][$k]
			)
		);
	}
	echo json_encode($result);
