<?php
use GarnetDG\FileManager as Main;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

/*************************************************************************************\
*                                                                                     *
*  This extension requires at least version 2.2.0 of Garnet DeGelder's File Manager.  *
*                                                                                     *
\*************************************************************************************/

Main\Hooks::register('_main.browse.head', function($path) {
	echo '<li>';
	echo '<input id="search_text" type="search" placeholder="Search" style="margin: 0px; vertical-align: middle; display: inline; width: 15em;" maxlength="'.Main\GlobalSettings::get('search.max_length', 127).'" />';
	echo '<input id="search_api_uri" type="hidden" value="'.Main\Router::getHtmlReadyUri('/searchapi').'" />';
	echo '<input id="search_path" type="hidden" value="'.htmlspecialchars($path).'" />';
	echo '<div id="search_modal" class="modal">';
		echo '<div id="search_content" class="content">';
			echo '<img id="search_close" src="'.Main\Router::getHtmlReadyUri('/resource/main/img/close.png').'" alt="Close" title="Close" class="close" />';
			echo '<div class="overflow">';
				echo '<h1 id="search_results_header">Search Results for <code id="search_results_header_search_string"></code></h1>';
				echo '<div id="search_results"></div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
	echo '<script src="'.Main\Router::getHtmlReadyUri('/resource/search/search.js').'"></script>';
	echo '</li>';
});

// Note: this only currently works properly for the first bit of the Latin character set.
// no wildcard support
function searchScore($pattern, $string) {
	$pattern = strtolower(substr($pattern, 0, 255));
	$string = strtolower(substr($string, 0, 255));

	$string = str_replace(str_split('ÀÁÂÃÄÅàáâãäå'), 'a', $string);
	$string = str_replace(str_split('Ææ'), 'ae', $string);
	$string = str_replace(str_split('Çç'), 'c', $string);
	$string = str_replace(str_split('ÈÉÊËèéêë'), 'e', $string);
	$string = str_replace(str_split('ÌÍÎÏìíîï'), 'i', $string);
	$string = str_replace(str_split('Ðð'), 'd', $string);
	$string = str_replace(str_split('Ññ'), 'n', $string);
	$string = str_replace(str_split('ÒÓÔÕÖØòóôõöø'), 'o', $string);
	$string = str_replace(str_split('ÙÚÛÜùúûü'), 'u', $string);
	$string = str_replace(str_split('Ýý'), 'y', $string);

	$original_string = $string;

	$string = str_replace(str_split('!"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~'), ' ', $string);
	$string_array = explode(' ', $string);

	$pattern = str_replace(str_split('ÀÁÂÃÄÅàáâãäå'), 'a', $pattern);
	$pattern = str_replace(str_split('Ææ'), 'ae', $pattern);
	$pattern = str_replace(str_split('Çç'), 'c', $pattern);
	$pattern = str_replace(str_split('ÈÉÊËèéêë'), 'e', $pattern);
	$pattern = str_replace(str_split('ÌÍÎÏìíîï'), 'i', $pattern);
	$pattern = str_replace(str_split('Ðð'), 'd', $pattern);
	$pattern = str_replace(str_split('Ññ'), 'n', $pattern);
	$pattern = str_replace(str_split('ÒÓÔÕÖØòóôõöø'), 'o', $pattern);
	$pattern = str_replace(str_split('ÙÚÛÜùúûü'), 'u', $pattern);
	$pattern = str_replace(str_split('Ýý'), 'y', $pattern);

	$original_pattern = $pattern;

	$pattern = str_replace(str_split('!#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~'), ' ', $pattern); // don't remove double quotes

	// generate array of search tokens
	$pattern_length = strlen($pattern);
	$in_quote = false;
	$current_token = '';
	$pattern_array = [];
	for ($i = 0; $i < $pattern_length; $i++) {
		$char = $pattern[$i];
		if ($in_quote) {
			if ($char === '"') {
				$pattern_array[] = $current_token;
				$current_token = '';
				$in_quote = false;
			} else {
				$current_token .= $char;
			}
		} else if ($char === '"') {
			if ($current_token !== '') {
				$pattern_array[] = $current_token;
				$current_token = '';
			}
			$in_quote = true;
		} else if ($char !== ' ') {
			$current_token .= $char;
		} else {
			if ($current_token !== '') {
				$pattern_array[] = $current_token;
				$current_token = '';
			}
		}
	}
	if ($current_token !== '') {
		$pattern_array[] = $current_token;
	}

	$pattern = str_replace('"', '', $pattern);

	$original_pattern_length = strlen($original_pattern);
	$in_quote = false;
	$current_token = '';
	$original_pattern_array = [];
	for ($i = 0; $i < $original_pattern_length; $i++) {
		$char = $original_pattern[$i];
		if ($in_quote) {
			if ($char === '"') {
				$original_pattern_array[] = $current_token;
				$current_token = '';
				$in_quote = false;
			} else {
				$current_token .= $char;
			}
		} else if ($char === '"') {
			if ($current_token !== '') {
				$original_pattern_array[] = $current_token;
				$current_token = '';
			}
			$in_quote = true;
		} else if ($char !== ' ') {
			$current_token .= $char;
		} else {
			if ($current_token !== '') {
				$original_pattern_array[] = $current_token;
				$current_token = '';
			}
		}
	}
	if ($current_token !== '') {
		$original_pattern_array[] = $current_token;
	}

	$original_pattern = str_replace('"', '', $original_pattern);

	// run the actual checks
	$score = 0;

	// to avoid weirdness like divide by zero
	if (strlen($string) > 0 && strlen($original_string) > 0) {

		// the number of times each term appears
		foreach ($pattern_array as $pattern_token) {
			$score += @substr_count($string, $pattern_token) * sqrt(strlen($pattern_token) / strlen($string)) * 0.75;
		}

		// the number of times each unprocessed term appears
		foreach ($original_pattern_array as $original_pattern_token) {
			$score += @substr_count($original_string, $original_pattern_token) * sqrt(strlen($original_pattern_token) / strlen($original_pattern));
		}

		// the metaphone similarity of each term
		foreach ($pattern_array as $pattern_token) {
			foreach ($string_array as $string_token) {
				if (metaphone($string_token) === metaphone($pattern_token)) {
					$score += (sqrt(strlen($pattern_token) + 1) - 1) * 0.5;
				}
			}
		}

		// the levenshtein distance between each term
		foreach ($pattern_array as $pattern_token) {
			foreach($string_array as $string_token) {
				if (strlen($string_token) > 0) {
					$levenshtein = @levenshtein($pattern_token, $string_token);
					if ($levenshtein <= 1 && $levenshtein >= 0) {
						$score += (1 / ($levenshtein + 1))  * sqrt(strlen($pattern_token) / strlen($string_token))* 0.5;
					}
				}
			}
		}

		// the closeness of the whole processed search string and the whole processed compared string
		$levenshtein = @levenshtein($pattern, $string);
		if ($levenshtein <= 1 && $levenshtein >= 0) {
			$score += (1 / ($levenshtein + 1)) * (sqrt(strlen($pattern) + 1) - 1) * 0.5;
		}

	}

	return $score;
}

function searchGetDirectoryScores($searchString, $baseDirectory, $nestedDirectory = '') {
	static $nestLevel = 1;

	if ($nestLevel > Main\GlobalSettings::get('search.max_dir_recursion', 3)) {
		return [];
	}

	$nestLevel++;

	$show_hidden = (Main\UserSettings::get('_main.browse.show_hidden', 'false') === 'true');
	$results = [];

	foreach (Main\Filesystem::scandir(trim($baseDirectory, '/').'/'.trim($nestedDirectory, '/')) as $filename) {
		if (connection_aborted()) {
			return [];
		}
		if (($show_hidden && $filename !== '.' && $filename !== '..') || substr($filename, 0, 1) !== '.') {
			$score = searchScore($searchString, $filename);
			if ($score > 0) {
				$results[trim(trim($nestedDirectory, '/').'/'.$filename, '/')] = $score;
			}
			if ($GLOBALS['search.start_time'] + Main\GlobalSettings::get('search.time_limit', 30) > time()) {
				$nextNestDir = trim($baseDirectory, '/').str_replace('//', '/', '/'.trim($nestedDirectory, '/').'/').$filename;
				if (Main\Filesystem::is_readable($nextNestDir) && Main\Filesystem::is_dir($nextNestDir)) {
					$results = array_merge($results, searchGetDirectoryScores($searchString, trim($baseDirectory, '/'), trim($nestedDirectory, '/').'/'.$filename));
				}
			}
		}
	}

	$nestLevel--;

	return $results;
}

Main\Router::registerPage('searchapi', function() {
	Main\Auth::authenticate();

	if (!isset($_POST['searchPath'], $_POST['searchString'])) {
		http_response_code(400);
		exit();
	}

	if (Main\Session::get('search.searching', false)) {
		http_response_code(429);
		exit();
	}
	Main\Session::set('search.searching', true);

	$searchString = substr($_POST['searchString'], 0, Main\GlobalSettings::get('search.max_length', 127));

	$GLOBALS['search.start_time'] = time();
	$results = searchGetDirectoryScores($searchString, rtrim($_POST['searchPath'], '/'));
	unset($GLOBALS['search.start_time']);

	// sort the results by score
	arsort($results);
	$result_array = [];

	$audio_icon_path = Main\Router::getHtmlReadyUri('/resource/main/img/audio.png');
	$image_icon_path = Main\Router::getHtmlReadyUri('/resource/main/img/image.png');
	$text_icon_path = Main\Router::getHtmlReadyUri('/resource/main/img/text.png');
	$video_icon_path = Main\Router::getHtmlReadyUri('/resource/main/img/video.png');
	$generic_icon_path = Main\Router::getHtmlReadyUri('/resource/main/img/generic.png');
	$folder_icon_path = Main\Router::getHtmlReadyUri('/resource/main/img/folder.png');
	$inaccessible_icon_path = Main\Router::getHtmlReadyUri('/resource/main/img/inaccessible.png');
	$unknown_icon_path = Main\Router::getHtmlReadyUri('/resource/main/img/unknown.png');

	foreach ($results as $filename => $score) {
		$file = $_POST['searchPath'].'/'.$filename;
		$file = '/'.trim($file, '/');

		if (Main\Filesystem::is_readable($file)) {
			if (Main\Filesystem::is_file($file)) {
				switch (explode('/', Main\Filesystem::getContentType($file, false, true), 2)[0]) {
					case 'audio':
						$type = 'audio';
						$icon = $audio_icon_path;
						break;
					case 'image':
						$type = 'image';
						$icon = $image_icon_path;
						break;
					case 'text':
						$type = 'text';
						$icon = $text_icon_path;
						break;
					case 'video':
						$type = 'video';
						$icon = $video_icon_path;
						break;
					default:
						$type = 'file';
						$icon = $generic_icon_path;
						break;
				}
				$result_array[] = [
					//'relativeName'=>$filename,
					'htmlReadyRelativeName'=>str_replace(' ', '&nbsp;', htmlspecialchars($filename)),
					'uri'=>Main\Router::getHtmlReadyUri('/file/'.Main\Session::getSessionId().'/'.$file),
					'type'=>$type,
					'icon'=>$icon
				];
			} else if (Main\Filesystem::is_dir($file)) {
				$result_array[] = [
					//'relativeName'=>$filename,
					'htmlReadyRelativeName'=>str_replace(' ', '&nbsp;', htmlspecialchars($filename)).'/',
					'uri'=>Main\Router::getHtmlReadyUri('/browse/'.$file).'/',
					'type'=>'dir',
					'icon'=>$folder_icon_path
				];
			} else {
				$result_array[] = [
					//'relativeName'=>$filename,
					'htmlReadyRelativeName'=>str_replace(' ', '&nbsp;', htmlspecialchars($filename)),
					'uri'=>Main\Router::getHtmlReadyUri('/file/'.Main\Session::getSessionId().'/'.$file),
					'type'=>'unknown',
					'icon'=>$unknown_icon_path
				];
			}
		} else {
			$result_array[] = [
				//'relativeName'=>$filename,
				'htmlReadyRelativeName'=>str_replace(' ', '&nbsp;', htmlspecialchars($filename)),
				'uri'=>'',
				'type'=>'inaccessible',
				'icon'=>$inaccessible_icon_path
			];
		}
	}
	header('Content-type: text/json');
	array_splice($result_array, Main\GlobalSettings::get('search.max_results', 100));
	echo json_encode($result_array);
	Main\Session::unset('search.searching');
});

Main\Resources::register('search/search.js', function() {
	Main\Resources::serveFile('_extensions/search/search.js');
});
