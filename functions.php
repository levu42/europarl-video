<?php
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

	function europarl_video_committees() {
		return array(
			"AFET" => "Foreign Affaris",
			"DROI" => "Human Rights",
			"SEDE" => "Security and Defense",
			"DEVE" => "Development",
			"INTA" => "International Trade",
			"BUDG" => "Budgets",
			"CONT" => "Budgetary Control",
			"ECON" => "Economic and Monetary Affairs",
			"EMPL" => "Employment and Social Affairs",
			"ENVI" => "Environment, Public Health and Food Safety",
			"ITRE" => "Industry, Research and Energy",
			"IMCO" => "Internal Market and Consumer Protection",
			"TRAN" => "Transport and Tourism",
			"REGI" => "Regional Development",
			"AGRI" => "Agriculture and Rural Development",
			"PECH" => "Fisheries",
			"CULT" => "Culture and Education",
			"JURI" => "Legal Affairs",
			"LIBE" => "Civil Liberties, Justice and Home Affairs",
			"AFCO" => "Constitutional Affairs",
			"FEMM" => "Women's Rights and Gender Equality",
			"PETI" => "Petitions",
			"CRIM" => "Organised crime, corruption and money laundering"
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

	function europarl_video_mep_select($id = '') {
		$meps = json_decode(file_get_contents('meps.json'), true);
		uasort($meps, 'europarl_video_sort_meps');
		$html = '<select name="mep" id="' . htmlspecialchars($id) . '">';
		foreach($meps as $code => $name) {
			$html .= '<option value="' . $code . '">' . htmlspecialchars($name) . '</option>';
		}
		$html .= '</select>';
		return $html;
	}

	function europarl_video_committee_select($id = '') {
		$langs = europarl_video_committees();
		$html = '<select name="lang" id="' . htmlspecialchars($id) . '">';
		foreach($langs as $code => $name) {
			$html .= '<option value="' . $code . (($code == 'en') ? '" selected="selected' : '') . '">' . $code . ' (' . htmlspecialchars($name) . ')</option>';
		}
		$html .= '</select>';
		return $html;
	}

	function europarl_video_lang_select($id = '') {
		$langs = europarl_video_langs();
		$html = '<select name="lang" id="' . htmlspecialchars($id) . '">';
		foreach($langs as $code => $name) {
			$html .= '<option value="' . $code . (($code == 'en') ? '" selected="selected' : '') . '">' . $code . ' &ndash; ' . htmlspecialchars($name) . '</option>';
		}
		$html .= '</select>';
		return $html;
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
