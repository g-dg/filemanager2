<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

Router::registerPage('browse', function($path) {
	Auth::authenticate();

	// get sort order
	if (isset($_GET['sort'], $_GET['order'])) {
		if (
			($_GET['sort'] === 'name' || $_GET['sort'] === 'last-modified' || $_GET['sort'] === 'size') &&
			($_GET['order'] === 'asc' || $_GET['order'] === 'desc')
		) {
			Session::set('_main.browse.sort_field', $_GET['sort']);
			Session::set('_main.browse.sort_order', $_GET['order']);
		}
		header('Location: '.Router::getHttpReadyUri('/browse/' . $path . '/'));
		exit();
	} else {
		$sort_field = Session::get('_main.browse.sort_field', null);
		$sort_order = Session::get('_main.browse.sort_order', null);
		if (is_null($sort_field)) {
			$sort_field = UserSettings::get('_main.browse.sort_field', 'name');
			Session::set('_main.browse.sort_field', $sort_field);
		}
		if (is_null($sort_order)) {
			$sort_order = UserSettings::get('_main.browse.sort_order', 'desc');
			Session::set('_main.browse.sort_order', $sort_order);
		}
	}

	// require a slash on the end
	if (substr($_SERVER['REQUEST_URI'], strlen($_SERVER['REQUEST_URI']) - 1, 1) !== '/') {
		header('Location: ' . Router::getHttpReadyUri('/browse/' . $path) . '/');
		exit();
	}

	if ($path !== '' && !(Filesystem::file_exists($path) && Filesystem::is_readable($path))) {
		MainUiTemplate::header('/' . $path, '<link rel="stylesheet" href="' . Router::getHtmlReadyUri('/resource/main/browse.css') . '" type="text/css" />');
		echo '		<h1>Not Found</h1><p>The requested URL '.htmlspecialchars($path).' was not found on this server.</p><hr /><em>Garnet DeGelder\'s File Manager 2.0.0-alpha.6 at phoenix port 80</em>';
		MainUiTemplate::footer();
		exit();
	}

	if (Filesystem::is_file($path)) {
		Router::redirect('/file/'.Session::getSessionId().'/'.$path);
	}

	$dirlist = Filesystem::scandir($path);
	if ($dirlist) {
		// sort the directory listing
		switch ($sort_field) {
			case 'name':
				switch ($sort_order) {
					case 'asc':
						usort($dirlist, function($a, $b){return strnatcasecmp($b, $a);});
						break;
					case 'desc':
						usort($dirlist, function($a, $b){return strnatcasecmp($a, $b);});
					break;
				}
				break;
			case 'last-modified':
				$mtime_dirlist = [];
				foreach ($dirlist as $file) {
					$mtime_dirlist[$file] = Filesystem::filemtime($path.'/'.$file);
				}
				switch ($sort_order) {
					case 'asc':
						uasort($mtime_dirlist, function($a, $b){return ($a < $b)? -1 : 1;});
						break;
					case 'desc':
						uasort($mtime_dirlist, function($a, $b){return ($a > $b)? -1 : 1;});
						break;
				}
				$dirlist = [];
				foreach ($mtime_dirlist as $file => $mtime) {
					$dirlist[] = $file;
				}
				break;
			case 'size':
				$size_dirlist = [];
				foreach ($dirlist as $file) {
					if (Filesystem::is_dir($path.'/'.$file)) {
						$size_dirlist[$file] = Filesystem::fileCount($path.'/'.$file);
					} else {
						$size_dirlist[$file] = Filesystem::filesize($path.'/'.$file);
					}
				}
				switch ($sort_order) {
					case 'asc':
						uasort($size_dirlist, function($a, $b){return ($a > $b)? -1 : 1;});
						break;
					case 'desc':
						uasort($size_dirlist, function($a, $b){return ($a < $b)? -1 : 1;});
						break;
				}
				$dirlist = [];
				foreach ($size_dirlist as $file => $size) {
					$dirlist[] = $file;
				}
				break;
		}
	} else {
		$dirlist = [];
	}


	MainUiTemplate::header('/' . $path, '<link rel="stylesheet" href="' . Router::getHtmlReadyUri('/resource/main/browse.css') . '" type="text/css" />');
	echo '		<div class="overflow">
			<table class="u-full-width listing">
				<thead>
					<tr><th></th>';

	switch ($sort_order) {
		case 'asc':
			$new_order = 'desc';
			$sort_arrow = '&#x25B2;';
			break;
		case 'desc':
			$new_order = 'asc';
			$sort_arrow = '&#x25BC;';
			break;
	}

	$sort_arrow_name = '';
	$sort_arrow_mtime = '';
	$sort_arrow_size = '';
	switch ($sort_field) {
		case 'name':
			$sort_arrow_name = $sort_arrow;
			break;
		case 'last-modified':
			$sort_arrow_mtime = $sort_arrow;
			break;
		case 'size':
			$sort_arrow_size = $sort_arrow;
			break;
	}

	echo '<th><a href="'.Router::getHtmlReadyUri('/browse/'.$path, ['sort'=>'name', 'order'=>$new_order]).'" title="Sort by name">'.$sort_arrow_name.'Name</a></th>';
	echo '<th><a href="'.Router::getHtmlReadyUri('/browse/'.$path, ['sort'=>'last-modified', 'order'=>$new_order]).'" title="Sort by last modified time">'.$sort_arrow_mtime.'Last Modified</a></th>';
	echo '<th><a href="'.Router::getHtmlReadyUri('/browse/'.$path, ['sort'=>'size', 'order'=>$new_order]).'" title="Sort by size">'.$sort_arrow_size.'Size</a></th>';
	Hooks::exec('_main_browse_thead');
	echo '<th></th><th></th></tr>
				</thead>
				<tbody>
';
	if ($path !== '') {
		echo '					<tr><td class="img"><img src="'.Router::getHtmlReadyUri('/resource/main/icons/back.png').'" alt="[PARENTDIR]" title="Parent Folder" /></td><td><a href="..">[Parent Directory]</a></td><td></td><td></td>';
		Hooks::exec('_main_browse_tbody', [Filesystem::sanitizePath($path . '/..')]);
		echo '<td></td><td></td></tr>';
	}
	$file_id = -1;
	$show_hidden = (UserSettings::get('_main.browse.show_hidden', 'false') === 'true');

	// cache various function calls
	$audio_icon_path = Router::getHtmlReadyUri('/resource/main/icons/audio.png');
	$image_icon_path = Router::getHtmlReadyUri('/resource/main/icons/image.png');
	$text_icon_path = Router::getHtmlReadyUri('/resource/main/icons/text.png');
	$video_icon_path = Router::getHtmlReadyUri('/resource/main/icons/video.png');
	$generic_icon_path = Router::getHtmlReadyUri('/resource/main/icons/generic.png');
	$folder_icon_path = Router::getHtmlReadyUri('/resource/main/icons/folder.png');
	$unkown_icon_path = Router::getHtmlReadyUri('/resource/main/icons/unknown.png');

	$download_icon_path = Router::getHtmlReadyUri('/resource/main/icons/download.png');
	$manage_icon_path = Router::getHtmlReadyUri('/resource/main/icons/properties.png');

	$file_serve_prefix = Router::getHtmlReadyUri('/file/'.Session::getSessionId()).'/';
	$download_serve_prefix = Router::getHtmlReadyUri('/download').'/';
	$properties_prefix = Router::getHtmlReadyUri('/properties').'/';

	foreach ($dirlist as $filename) {
		$file_id++;

		if ($show_hidden || substr($filename, 0, 1) !== '.') {
			$file = $path.'/'.$filename;

			$is_readable = Filesystem::is_readable($file);
			$is_writable = Filesystem::is_writable($file);
			$is_dir = Filesystem::is_dir($file);
			$is_file = Filesystem::is_file($file);

			echo '
					<tr>';


			// icon
			echo '<td class="img">';
			if ($is_readable) {
				if ($is_file) {
					$mime_type = Filesystem::getMimeType($file, false, true);
					switch (explode('/', $mime_type, 2)[0]) {
						case 'audio':
							echo '<img src="'.$audio_icon_path.'" alt="[SND]" title="Audio" />';
							break;
						case 'image':
							echo '<img src="'.$image_icon_path.'" alt="[IMG]" title="Image" />';
							break;
						case 'text':
							echo '<img src="'.$text_icon_path.'" alt="[TXT]" title="Text" />';
							break;
						case 'video':
							echo '<img src="'.$video_icon_path.'" alt="[VID]" title="Video" />';
							break;
						default:
							echo '<img src="'.$generic_icon_path.'" alt="[   ]" title="File" />';
							break;
					}
				} else if ($is_dir) {
					echo '<img src="'.$folder_icon_path.'" alt="[DIR]" title="Folder" />';
				}
			} else {
				echo '<img src="'.$unkown_icon_path.'" alt="[ ? ]" title="Inaccessible" />';
			}
			echo '</td>';


			// filename
			echo '<td>';
			if ($is_readable) {
				if ($is_dir) {
					echo '<a href="'.htmlspecialchars($filename).'/">'.htmlspecialchars($filename).'/</a>';
				} else {
					echo '<a href="'.$file_serve_prefix . htmlspecialchars($file).'" target="_blank">'.htmlspecialchars($filename).'</a>';
				}
			} else {
				if ($is_dir) {
					echo '<a class="disabled" href="">'.htmlspecialchars($filename).'/</a>';
				} else {
					echo '<a class="disabled" href="">'.htmlspecialchars($filename).'</a>';
				}
			}
			echo '</td>';


			// last modified
			echo '<td>';
			$mtime = Filesystem::filemtime($file);
			echo '<span id="file_mtime_'.$file_id.'" title="'.htmlspecialchars(date('l, F j, Y - g:i:s A', $mtime)).'" onclick="alert(document.getElementById(\'file_mtime_'.$file_id.'\').getAttribute(\'title\'));">';
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
			echo '</span>';
			echo '</td>';


			// size
			echo '<td>';
			if ($is_dir) {
				$filecount = Filesystem::fileCount($file);
				if ($filecount != 1) {
					echo '<span id="file_size_'.$file_id.'" title="'.htmlspecialchars(number_format($filecount, 0, '.', ',')).' files" onclick="alert(document.getElementById(\'file_size_'.$file_id.'\').getAttribute(\'title\'));">';
				} else {
					echo '<span id="file_size_'.$file_id.'" title="'.htmlspecialchars($filecount).' file" onclick="alert(document.getElementById(\'file_size_'.$file_id.'\').getAttribute(\'title\'));">';
				}
				if ($filecount < 1000) {
					echo sprintf("%d", $filecount);
				} else if ($filecount < 1000000) {
					echo sprintf("%01.1f K", ($filecount / 1000));
				} else if ($filecount < 1000000000) {
					echo sprintf("%01.1f M", ($filecount / 1000000));
				} else {
					echo sprintf("%01.1f G", ($filecount / 1000000000));
				}
			} else {
				$filesize = Filesystem::filesize($file);
				if ($filesize != 1) {
					echo '<span id="file_size_'.$file_id.'" title="'.htmlspecialchars(number_format($filesize, 0, '.', ',')).' bytes" onclick="alert(document.getElementById(\'file_size_'.$file_id.'\').getAttribute(\'title\'));">';
				} else {
					echo '<span id="file_size_'.$file_id.'" title="'.htmlspecialchars($filesize).' byte" onclick="alert(document.getElementById(\'file_size_'.$file_id.'\').getAttribute(\'title\'));">';
				}
				if ($filesize < 1024) {
					echo sprintf("%d B", $filesize);
				} else if ($filesize < 1048576) {
					echo sprintf("%01.1f KiB", ($filesize / 1024));
				} else if ($filesize < 1073741824) {
					echo sprintf("%01.1f MiB", ($filesize / 1048576));
				} else if ($filesize < 1099511627776) {
					echo sprintf("%01.1f GiB", ($filesize / 1073741824));
				} else {
					echo sprintf("%01.1f TiB", ($filesize / 1099511627776));
				}
			}
			echo '</span>';
			echo '</td>';


			Hooks::exec('_main_browse_tbody', [$file]);


			// download
			echo '<td class="img">';
			if ($is_file && $is_readable) {
				echo '<a href="'.$download_serve_prefix . htmlspecialchars($file).'"><img src="'.$download_icon_path.'" alt="Download" title="Download" /></a>';
			}
			echo '</td>';


			// properties
			echo '<td class="img"><a href="'.rtrim($properties_prefix, '/') . htmlspecialchars($file).'"><img src="'.$manage_icon_path.'" alt="Properties" title="Properties" /></a></td>';


			echo '</tr>';
		}
	}
	echo '
				</tbody>
			</table>
		</div>';
	MainUiTemplate::footer();
});


Resources::register('main/browse.css', function(){
	Resources::serveFile('_plugins/main/resources/browse.css');
});

Resources::register('main/icons/audio.png', function(){
	Resources::serveFile('_plugins/main/resources/icons/audio.png');
});
Resources::register('main/icons/back.png', function(){
	Resources::serveFile('_plugins/main/resources/icons/back.png');
});
Resources::register('main/icons/folder.png', function(){
	Resources::serveFile('_plugins/main/resources/icons/folder.png');
});
Resources::register('main/icons/generic.png', function(){
	Resources::serveFile('_plugins/main/resources/icons/generic.png');
});
Resources::register('main/icons/image.png', function(){
	Resources::serveFile('_plugins/main/resources/icons/image.png');
});
Resources::register('main/icons/text.png', function(){
	Resources::serveFile('_plugins/main/resources/icons/text.png');
});
Resources::register('main/icons/unknown.png', function(){
	Resources::serveFile('_plugins/main/resources/icons/unknown.png');
});
Resources::register('main/icons/video.png', function(){
	Resources::serveFile('_plugins/main/resources/icons/video.png');
});

Resources::register('main/icons/download.png', function(){
	Resources::serveFile('_plugins/main/resources/icons/download.png');
});
Resources::register('main/icons/properties.png', function(){
	Resources::serveFile('_plugins/main/resources/icons/properties.png');
});
