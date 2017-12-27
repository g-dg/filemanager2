<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

Router::registerPage('properties', function($path) {
	Auth::authenticate();
	MainUiTemplate::header('Properties of /'.$path, '<link rel="stylesheet" href="' . Router::getHtmlReadyUri('/resource/main/css/properties.css') . '" type="text/css" />');

	echo '<a href="'.Router::getHtmlReadyUri(pathinfo('/browse/'.$path)['dirname']).'/">&lt; Back</a>';

	echo '<fieldset><legend>Properties</legend>';
	echo '<h3 style="margin-top: .5em;">'.htmlspecialchars('/'.$path).'</h3>';

	echo '<table class="border"><thead><tr><th>Property</th><th>Value</th></tr></thead><tbody>';


	echo '<tr><td>Type:</td><td>';
	$filetype = Filesystem::filetype($path);
	switch ($filetype) {
		case 'file':
			echo 'File';
			break;
		case 'dir':
			echo 'Folder';
			break;
		default:
			echo 'Other/Unknown ('.htmlspecialchars($filetype).')';
			break;
	}
	echo '</td></tr>';


	echo '<tr><td>Size:</td><td>';
	if (Filesystem::is_dir($path)) {
		$filecount = Filesystem::fileCount($path);
		if ($filecount === 1) {
			echo '1 file';
		} else if ($filecount < 1000) {
			echo $filecount . ' files';
		} else if ($filecount < 1000000) {
			echo sprintf("%01.1f K files (%s files)", ($filecount / 1000), number_format($filecount, 0, '.', ','));
		} else if ($filecount < 1000000000) {
			echo sprintf("%01.1f M files (%s files)", ($filecount / 1000000), number_format($filecount, 0, '.', ','));
		} else {
			echo sprintf("%01.1f G files (%s files)", ($filecount / 1000000000), number_format($filecount, 0, '.', ','));
		}
	} else {
		$filesize = Filesystem::filesize($path);
		if ($filesize !== false) {
			if ($filesize != 1) {
				echo '<span id="file_size" title="'.htmlspecialchars(number_format($filesize, 0, '.', ',')).' bytes" onclick="alert($(\'#file_size\').attr(\'title\'));">';
			} else {
				echo '<span id="file_size" title="'.htmlspecialchars($filesize).' byte" onclick="alert($(\'#file_size\').attr(\'title\'));">';
			}
			if ($filesize === 1) {
				echo '1 byte';
			} else if ($filesize < 1024) {
				echo $filesize . ' bytes';
			} else if ($filesize < 1048576) {
				echo sprintf("%01.1f KiB (%s bytes)", ($filesize / 1024), number_format($filesize, 0, '.', ','));
			} else if ($filesize < 1073741824) {
				echo sprintf("%01.1f MiB (%s bytes)", ($filesize / 1048576), number_format($filesize, 0, '.', ','));
			} else if ($filesize < 1099511627776) {
				echo sprintf("%01.1f GiB (%s bytes)", ($filesize / 1073741824), number_format($filesize, 0, '.', ','));
			} else {
				echo sprintf("%01.1f TiB (%s bytes", ($filesize / 1099511627776), number_format($filesize, 0, '.', ','));
			}
		} else {
			echo 'Unknown';
		}
	}
	echo '</td></tr>';


	echo '<tr><td>Last Modified:</td><td>';
	$mtime = Filesystem::filemtime($path);
	if ($mtime !== false) {
		$mtimediff = time() - $mtime;
		if ($mtimediff < 0) {
			echo 'In the future';
		} else if ($mtimediff < 60) {
			$seconds = $mtimediff;
			if ($seconds != 1) {
				echo $seconds . ' seconds ago';
			} else {
				echo '1 second ago';
			}
		} else if ($mtimediff < 3600) {
			$minutes = floor($mtimediff / 60);
			if ($minutes != 1) {
				echo $minutes . ' minutes ago';
			} else {
				echo '1 minute ago';
			}
		} else if ($mtimediff < 86400) {
			$hours = floor($mtimediff / 3600);
			if ($hours != 1) {
				echo $hours . ' hours ago';
			} else {
				echo '1 hour ago';
			}
		} else if ($mtimediff < 2592000) {
			$days = floor($mtimediff / 86400);
			if ($days != 1) {
				echo $days . ' days ago';
			} else {
				echo '1 day ago';
			}
		} else if ($mtimediff < 31536000) {
			$months = floor($mtimediff / 2592000);
			if ($months != 1) {
				echo $months . ' months ago';
			} else {
				echo '1 month ago';
			}
		} else {
			$years = floor($mtimediff / 31536000);
			if ($years != 1) {
				echo $years . ' years ago';
			} else {
				echo '1 year ago';
			}
		}
		echo ' ('.htmlspecialchars(date('l, F j, Y - g:i:s A', $mtime)).')';
	} else {
		echo 'Unknown';
	}
	echo '</td></tr>';


	echo '<tr><td>Readable:</td><td>';
	if (Filesystem::is_readable($path)) {
		echo 'Yes';
	} else {
		echo 'No';
	}
	echo '</td></tr>';


	echo '<tr><td>Writable:</td><td>';
	if (Filesystem::is_writable($path)) {
		echo 'Yes';
	} else {
		echo 'No';
	}
	echo '</td></tr>';


	Hooks::exec('_main.properties.propertytable');


	echo '</tbody></table></fieldset>';


	MainUiTemplate::footer();
});

Resources::register('main/css/properties.css', function(){
	Resources::serveFile('_extensions/main/resources/css/properties.css');
});
