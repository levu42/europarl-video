<?php

	define('EUROPARL_VIDEO_API_CACHE_MAXAGE', 43200);
	define('EUROPARL_VIDEO_HTTP_BASE', 'http://www.europarl.europa.eu/ep-live/');
	
	require_once('functions.php');

	function europarl_video_html($function) {
		$res = europarl_video_api($function);
		if ($res == null) {
			include('searchform.php');
			return;
		}

		$get = $_GET;
		unset($get['output']);
		$link = 'index.php?';
		foreach($get as $k=>$v) {
			$link .= urlencode($k) . '=' . urlencode($v) . '&';
		}

		echo '<p style="text-align: right">Result as: &nbsp; <a href="' . htmlspecialchars($link);
		echo 'output=json">json</a> &nbsp; <a href="' . htmlspecialchars($link) . 'output=xml">';
		echo 'XML (RSS)</a></p>';
		echo '<table class="span12 hovertable pullthleft"><tr><th>Title</th>';
		echo '<th style="width: 12em">Length</th><th style="width: 10em">Download</th></tr>';
		$lasttopic = '';
		foreach($res as $line) {
			if (isset($line['topic']) && ($line['topic'] != $lasttopic)) {
				echo '<tr><th colspan=2><a target="_blank" href="' . htmlspecialchars($line['topic-url']);
				echo '">' . htmlspecialchars($line['topic']) . '</a></th><th>';
				echo (is_null($line['topic-download-url']) ? '' : '<a href="' . $line['topic-download-url'] . '">Download Video</a>');
				echo '</th></tr>';
				$lasttopic = $line['topic'];
			}
			echo '<tr><td>' . $line['title'] . '</td><td>';
			echo europarl_video_getNiceDuration((int) $line['attributes']['endInterv'] - (int) $line['attributes']['startInterv']) . '</td><td><a href="' . htmlspecialchars($line['url']);
			echo '">Download Video</a></td></tr>';
		}
		echo '</table>';
	}
	function europarl_video_json($function) {
		$res = europarl_video_api($function);
		if ($res !== null) {
			header("Content-Type: text/json");
			echo json_encode($res);
			die;
		}
	}
	function europarl_video_xml($function) {
		$res = europarl_video_api($function);
		if ($res !== null) {
			$xml = simplexml_load_string('<?xml version="1.0" encoding="utf-8" ?><rss version="2.0"><channel></channel></rss>');
			$xml->channel->addChild('title', 'Videos from the European Parliament - Search Result');
			$lang = 'en';
			if (isset($_GET['lang'])) {
				$langs = europarl_video_langs();
				if (isset($langs[$_GET['lang']])) {
					$lang = $_GET['lang'];
				}
			}
			$xml->channel->addChild('language', $lang);
			$xml->channel->addChild('pubDate', date('r'));
			foreach($res as $line) {
				$item = $xml->channel->addChild('item');
				$item->addChild('title', htmlentities($line['title']));
				$item->addChild('link', htmlentities($line['url']));
				$item->addChild('author', htmlentities($line['author']));
				if (isset($line['topic'])) {
					$topic = $item->addChild('topic');
					$topic->addChild('title', htmlentities($line['topic']));
					$topic->addChild('link', htmlentities($line['topic-url']));
					$topic->addChild('download', htmlentities($line['topic-download-url']));
				}
			}
			echo $xml->asXml();
			die;
		}
	}
	function europarl_video_api($function) {
		switch ($function) {
			case 'search-plenary-by-mep':
			case 'search-plenary-by-keyword':
			case 'search-plenary-by-date':
				$lang = 'en';
				if (isset($_GET['lang'])) {
					$langs = europarl_video_langs();
					if (isset($langs[$_GET['lang']])) {
						$lang = $_GET['lang'];
					}
				}
				$searchByMEP = ($function == 'search-plenary-by-mep');
				$searchByKeyword = ($function == 'search-plenary-by-keyword');
				$searchByDate = ($function == 'search-plenary-by-date');
				if($searchByMEP) {
					$mandatory = 'mep';
				} elseif ($searchByKeyword) {
					$mandatory = 'subject';
				} else {
					$mandatory = 'date';
				}
				if (!isset($_GET[$mandatory])) {
					return europarl_video_api(null);
				}
				$searchBy = $_GET[$mandatory];
				if ($searchByDate) {
					$date = europarl_video_get_useful_date($searchBy);
					if ($date === null) {
						return europarl_video_api(null);
					}
				}	elseif ($searchByMEP) {
					$meps = json_decode(file_get_contents('meps.json'), true);
					if (!isset($meps[$searchBy])) {
						return europarl_video_api(null);
					}
				}	
				$api = 'europarl-video';
				$parameter = array('lang' => $lang);
				if ($searchByDate) {
					$parameter['date'] = $searchBy;
				} elseif ($searchByKeyword) {
					$parameter['subject'] = $searchBy;
				} elseif ($searchByMEP) {
					$parameter['mep'] = $searchBy;
				}
				$age = age_of_api_cache($api, $function, $parameter);
				if ((!isset($_GET['skip-cache'])) && ($age !== false) && ($age <= EUROPARL_VIDEO_API_CACHE_MAXAGE)) {
					return get_api_cache($api, $function, $parameter);
				}
				if ($searchByKeyword) {
					$query = '';
					if (isset($_GET['startdate'])) {
						$query .= '&start-date=' . urlencode(europarl_video_get_useful_date($_GET['startdate']));
					}
					if (isset($_GET['enddate'])) {
						$query .= '&end-date=' . urlencode(europarl_video_get_useful_date($_GET['enddate']));
					}
				}
				$pageUntil = 0;
				if ($searchByDate) {
					$result = 'search-by-date?date=';
				} elseif ($searchByKeyword) {
					$result = 'video?keywords=';
					$pageUntil = europarl_video_get_last_page_number(EUROPARL_VIDEO_HTTP_BASE . $lang . '/plenary/search-by-keyword?keywords=' . urlencode($searchBy) . $query);
				} else {
					$result = 'video?idmep=';
					$pageUntil = europarl_video_get_last_page_number(EUROPARL_VIDEO_HTTP_BASE . $lang . '/plenary/speaker-intervention/?idmep=' . urlencode($searchBy));
				}
				$result .= urlencode($searchBy);
				$result .= $query;
				$result = europarl_video_get_all_discussions(EUROPARL_VIDEO_HTTP_BASE . $lang . '/plenary/' . $result, $pageUntil);
				set_api_cache($api, $function, $parameter, $result);
				return $result;
				break;
		}
	}

