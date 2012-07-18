<?php

	define('EUROPARL_VIDEO_API_CACHE_MAXAGE', 43200);
	
	require_once('functions.php');

	function europarl_video_html($function) {
		$res = europarl_video_api($function);
		if ($res == null) {
			include('searchform.php');
		} else {
			$get = $_GET;
			unset($get['output']);
			$link = 'index.php?';
		  foreach($get as $k=>$v) {
				$link .= urlencode($k) . '=' . urlencode($v) . '&';
			}	
			echo '<p style="text-align: right">Result as: &nbsp; <a href="' . htmlspecialchars($link) . 'output=json">json</a> &nbsp; <a href="' . htmlspecialchars($link) . 'output=xml">XML (RSS)</a></p>';
			echo '<table class="span12 hovertable pullthleft"><tr><th>Title</th><th style="width: 12em">Length</th><th style="width: 10em">Download</th></tr>';
			$lasttopic = '';
			foreach($res as $line) {
				if (isset($line['topic']) && ($line['topic'] != $lasttopic)) {
					echo '<tr><th colspan=2><a target="_blank" href="' . htmlspecialchars($line['topic-url']) . '">' . htmlspecialchars($line['topic']) . '</a></th><th>' . (is_null($line['topic-download-url']) ? '' : '<a href="' . $line['topic-download-url'] . '">Download Video</a>') . '</th></tr>';
					$lasttopic = $line['topic'];
				}
				echo '<tr><td>' . $line['title'] . '</td><td>' . europarl_video_getNiceDuration((int) $line['attributes']['endInterv'] - (int) $line['attributes']['startInterv']) . '</td><td><a href="' . htmlspecialchars($line['url']) . '">Download Video</a></td></tr>';
			}
			echo '</table>';
		}
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
			case 'search-plenary-by-date':
				if (!isset($_GET['date'])) {
					return europarl_video_api(null);
				}
				$date = explode('-', $_GET['date']);
				$time = mktime(0, 0, 0, $date[1], $date[2], $date[0]);
				if ($time > time()) return null;
				$date = date('Ymd', $time);	
				$lang = 'en';
				if (isset($_GET['lang'])) {
					$langs = europarl_video_langs();
					if (isset($langs[$_GET['lang']])) {
						$lang = $_GET['lang'];
					}
				}
				$api = 'europarl-video';
				$parameter = array('lang' => $lang, 'date' => $date);
				$age = age_of_api_cache($api, $function, $parameter);
				if (($age !== false) && ($age <= EUROPARL_VIDEO_API_CACHE_MAXAGE)) {
					return get_api_cache($api, $function, $parameter);
				}
				$result = europarl_video_get_all_discussions('http://www.europarl.europa.eu/ep-live/' . $lang . '/plenary/search-by-date?date=' . urlencode($date));
				set_api_cache($api, $function, $parameter, $result);
				return $result;
				break;
			case 'search-plenary-by-keyword':
				if (!isset($_GET['subject'])) {
					return europarl_video_api(null);
				}
				$subject = $_GET['subject'];
				$lang = 'en';
				if (isset($_GET['lang'])) {
					$langs = europarl_video_langs();
					if (isset($langs[$_GET['lang']])) {
						$lang = $_GET['lang'];
					}
				}
				$api = 'europarl-video';
				$parameter = array('lang' => $lang, 'subject' => $subject);
				$age = age_of_api_cache($api, $function, $parameter);
				if (($age !== false) && ($age <= EUROPARL_VIDEO_API_CACHE_MAXAGE)) {
					return get_api_cache($api, $function, $parameter);
				}
				$result = europarl_video_get_all_discussions('http://www.europarl.europa.eu/ep-live/' . $lang . '/plenary/video?keywords=' . urlencode($subject));
				set_api_cache($api, $function, $parameter, $result);
				return $result;
			case 'search-plenary-by-mep':
				if (!isset($_GET['mep'])) {
					return europarl_video_api(null);
				}
				$mep = $_GET['mep'];
				$meps = json_decode(file_get_contents('meps.json'), true);
				if (!isset($meps[$mep])) return null;
				$lang = 'en';
				if (isset($_GET['lang'])) {
					$langs = europarl_video_langs();
					if (isset($langs[$_GET['lang']])) {
						$lang = $_GET['lang'];
					}
				}
				$api = 'europarl-video';
				$parameter = array('lang' => $lang, 'mep' => $mep);
				$age = age_of_api_cache($api, $function, $parameter);
				if (($age !== false) && ($age <= EUROPARL_VIDEO_API_CACHE_MAXAGE)) {
					return get_api_cache($api, $function, $parameter);
				}
				$result = europarl_video_get_all_discussions('http://www.europarl.europa.eu/ep-live/en/plenary/video?idmep=' . $mep);
				set_api_cache($api, $function, $parameter, $result);
				return $result;
				break;
			default:
				return null;
				return europarl_video_get_all_discussions('http://www.europarl.europa.eu/ep-live/de/plenary/search-by-date?start-date=20120705&end-date=20120706&date=20120705&format=wmv&askedDiscussionNumber=1');
				return europarl_video_do_cli_search('http://www.europarl.europa.eu/ep-live/en/plenary/video?idmep=96736&page=0&format=wmv&askedDiscussionNumber=0');
				break;
		}
	}

