<?php

	define('EUROPARL_VIDEO_API_CACHE_MAXAGE', 3600);

	function europarl_video_langs() {
		return array(
			"bg" => "български",
			"es" => "español",
			"cs" => "čeština",
			"da" => "dansk",
			"de" => "Deutsch",
			"et" => "eesti keel",
			"el" => "ελληνικά",
			"en" => "English",
			"fr" => "français",
			"it" => "italiano",
			"lv" => "latviešu valoda",
			"lt" => "lietuvių kalba",
			"hu" => "magyar",
			"mt" => "Malti",
			"nl" => "Nederlands",
			"pl" => "Polski",
			"pt" => "português",
			"ro" => "română",
			"sk" => "slovenčina",
			"sl" => "slovenščina",
			"fi" => "suomi",
			"sv" => "svenska"
		);
	}

	function europarl_video_surname_mep($mep) {
		$words = explode(' ', $mep);
		$ret = '';
		for ($i = 0; $i < count($words); $i++) {
			$words[$i] = str_replace('.', '', $words[$i]);
			if (substr($words[$i], 0, 2) == 'Mc') {
				$words[$i] = 'MC' . substr($words[$i], 2);
			}
			if (strlen($words[$i]) == 1) continue;
			if ($words[$i] == strtoupper($words[$i])) {
				$ret .= $words[$i] . ' ';
			}
		}
		return trim($ret);
	}

	function europarl_video_sort_meps($a, $b) {
		$sa = europarl_video_surname_mep($a);
		$sb = europarl_video_surname_mep($b);
		return strcasecmp($sa, $sb);
	}

	function europarl_video_mep_select() {
		$meps = json_decode(file_get_contents('meps.json'), true);
		uasort($meps, 'europarl_video_sort_meps');
		$html = '<select name="mep">';
		foreach($meps as $code => $name) {
			$html .= '<option value="' . $code . '">' . $name . '</option>';
		}
		$html .= '</select>';
		return $html;
	}

	function europarl_video_lang_select() {
		$langs = europarl_video_langs();
		$html = '<select name="lang">';
		foreach($langs as $code => $name) {
			$html .= '<option value="' . $code . (($code == 'en') ? '" selected="selected' : '') . '">' . $code . ' &ndash; ' . $name . '</option>';
		}
		$html .= '</select>';
		return $html;
	}

	function europarl_video_html($function) {
		$res = europarl_video_api($function);
		if ($res == null) { ?>
			<h2>Search plenary by date:</h2>
			<form action="#" method="get">
				<input type="hidden" name="api" value="europarl-video"><input type="hidden" name="function" value="search-plenary-by-date"><input type="hidden" name="output" value="html">
				Date: <input type="text" name="date"> (Format: <b>YYYY-MM-DD</b>, e.g. 2012-12-21)<br>
				Language: <?php echo europarl_video_lang_select(); ?><br>
				<div class="warning" style="font-size: 100%;">If the result isn't cached yet, this request may take very long (sometimes even a few minutes), please be patient
				<input type="submit" value="Search"></div>
			</form>
			<h2>Search plenary by keyword:</h2>
			<form action="#" method="get">
				<input type="hidden" name="api" value="europarl-video"><input type="hidden" name="function" value="search-plenary-by-keyword"><input type="hidden" name="output" value="html">
				Subject: <input type="text" name="subject"><br>
				Language: <?php echo europarl_video_lang_select(); ?><br>
				<div class="warning" style="font-size: 100%;">If the result isn't cached yet, this request may take very long (sometimes even a few minutes), please be patient
				<input type="submit" value="Search"></div>
			</form>
			<h2>Search plenary by MEP:</h2>
			<form action="#" method="get">
				<input type="hidden" name="api" value="europarl-video"><input type="hidden" name="function" value="search-plenary-by-mep"><input type="hidden" name="output" value="html">
				MEP: <?php echo europarl_video_mep_select(); ?><br>
				Language: <?php echo europarl_video_lang_select(); ?><br>
				<div class="warning" style="font-size: 100%;">If the result isn't cached yet, this request may take very long (sometimes even a few minutes), please be patient
				<input type="submit" value="Search"></div>
			</form>
		<? } else {
			echo '<table class="span12 hovertable pullthleft"><tr><th>Title</th><th style="width: 12em">Length</th><th style="width: 10em">Download</th></tr>';
			$lasttopic = '';
			foreach($res as $line) {
				if (isset($line['topic']) && ($line['topic'] != $lasttopic)) {
					echo '<tr><th colspan=2><a href="' . htmlspecialchars($line['topic-url']) . '">' . htmlspecialchars($line['topic']) . '</a></th><th>' . (is_null($line['topic-download-url']) ? '' : '<a href="' . $line['topic-download-url'] . '">Download Video</a>') . '</th></tr>';
					$lasttopic = $line['topic'];
				}
				echo '<tr><td>' . $line['title'] . '</td><td>' . europarl_video_getNiceDuration((int) $line['attributes']['endInterv'] - (int) $line['attributes']['startInterv']) . '</td><td><a href="' . htmlspecialchars($line['url']) . '">Download Video</a></td></tr>';
			}
			echo '</table>';
		}
	}
	function europarl_video_json($function) {
		;
	}
	function europarl_video_xml($function) {
		;
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
	function europarl_video_do_cli_search($url, $isFile = false) {
		if ($isFile) {
			return json_decode(shell_exec('./download.sh -f ' . escapeshellarg($url)), true);
		} else {
			return json_decode(shell_exec('./download.sh ' . escapeshellarg($url)), true);
		}
	}
	function europarl_video_get_all_discussions($url) {
		$content = file_get_contents($url);
		preg_match_all('/name="player_final_content_sb:list_discussion_sb:discussionsTable:(\d+):menu-home"\s+href="([^"]+)"><[^>]+>([^<]+)/mi', $content, $pat);
		$matches = $pat[2];
		$result = array();
		foreach($matches as $k => $match) {
			$match = hed($match);
			if (substr($match, 0, 1) == '/') {
				$match = preg_replace('/^(http:\/\/[^\/]+)\/.*$/i', '\1' . $match, $url);
			}
			$thistitle = hed($pat[3][$k]);
			$matchcontent = file_get_contents($match);
			$tempnam = tempnam(sys_get_temp_dir(), "europarlvid");
			file_put_contents($tempnam, $matchcontent);
			$thismatch = europarl_video_do_cli_search($tempnam, true);
			$topicURL = null;
			if (preg_match('/"(http:\/\/[^"]+vodchapter[^"]+)"/im', $matchcontent, $subpat) !== false) {
				$topicURL = hed($subpat[1]);
			}
			foreach ($thismatch as $thismatchkey => $thismatchitem) {
				$thismatch[$thismatchkey]['topic'] = hed($thistitle);
				$thismatch[$thismatchkey]['topic-url'] = $match;
				$thismatch[$thismatchkey]['topic-download-url'] = $topicURL;
			}
			$result = array_merge($result, $thismatch);
		}
		return $result;
	}

// http://stackoverflow.com/questions/6534490/formatting-duration-time-in-php
function europarl_video_getNiceDuration($durationInSeconds) {

  $duration = '';
  $days = floor($durationInSeconds / 86400);
  $durationInSeconds -= $days * 86400;
  $hours = floor($durationInSeconds / 3600);
  $durationInSeconds -= $hours * 3600;
  $minutes = floor($durationInSeconds / 60);
  $seconds = $durationInSeconds - $minutes * 60;

  if($days > 0) {
    $duration .= $days . ' days';
  }
  if($hours > 0) {
    $duration .= ' ' . $hours . ' hours';
  }
  if($minutes > 0) {
    $duration .= ' ' . $minutes . ' minutes';
  }
  if($seconds > 0) {
    $duration .= ' ' . $seconds . ' seconds';
  }
  return $duration;
}

