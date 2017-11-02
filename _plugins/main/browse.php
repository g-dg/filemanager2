<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

Router::registerPage('browse', function($path) {
	// require a slash on the end
	if (substr($_SERVER['REQUEST_URI'], strlen($_SERVER['REQUEST_URI']) - 1, 1) !== '/') {
		header('Location: ' . $_SERVER['REQUEST_URI'] . '/');
		exit();
	}

	Auth::authenticate();

	if (!Filesystem::file_exists($path)) {
		http_response_code(404);
	}

	if (Filesystem::is_file($path)) {
		Router::redirect('/file/'.Session::getSessionId().'/'.$path);
	}

	MainUiTemplate::header('/' . $path, '<link rel="stylesheet" href="' . Router::getHtmlReadyUri('/resource/main/browse.css') . '" type="text/css" />');
	echo '		<div class="overflow"><table class="u-full-width listing"><thead><tr><th></th><th>Name</th><th>Last Modified</th><th>Size</th><th></th></tr></thead><tbody>';
	$dirlist = Filesystem::scandir($path);
	if ($dirlist) {
		natcasesort($dirlist);
		if ($path !== '') {
			echo '<tr><td><img src="'.Router::getHtmlReadyUri('/resource/main/icons/back.png').'" alt="[PARENTDIR]" /></td><td><a href="..">[Parent Directory]</a></td><td></td><td></td><td></td></tr>';
		}
		$file_id = 0;
		foreach ($dirlist as $filename) {
			$file_id++;
			if (substr($filename, 0, 1) !== '.') {
				$file = $path.'/'.$filename;
				echo '<tr>';


				// icon
				if (Filesystem::is_readable($file) && Filesystem::is_file($file)) {
					$mime_type = Filesystem::getMimeType($file);
					switch (explode('/', $mime_type, 2)[0]) {
						case 'audio':
							echo '<td><img src="'.Router::getHtmlReadyUri('/resource/main/icons/audio.png').'" alt="[SND]" /></td>';
							break;
						case 'image':
							echo '<td><img src="'.Router::getHtmlReadyUri('/resource/main/icons/image.png').'" alt="[IMG]" /></td>';
							break;
						case 'text':
							echo '<td><img src="'.Router::getHtmlReadyUri('/resource/main/icons/text.png').'" alt="[TXT]" /></td>';
							break;
						case 'video':
							echo '<td><img src="'.Router::getHtmlReadyUri('/resource/main/icons/video.png').'" alt="[VID]" /></td>';
							break;
						default:
							echo '<td><img src="'.Router::getHtmlReadyUri('/resource/main/icons/generic.png').'" alt="[   ]" /></td>';
							break;
					}
				} else if (Filesystem::is_readable($file) && Filesystem::is_dir($file)) {
					echo '<td><img src="'.Router::getHtmlReadyUri('/resource/main/icons/folder.png').'" alt="[DIR]" /></td>';
				} else {
					echo '<td><img src="'.Router::getHtmlReadyUri('/resource/main/icons/unknown.png').'" alt="[ ? ]" /></td>';
				}


				// filename
				echo '<td>';
				if (Filesystem::is_readable($file)) {
					if (Filesystem::is_dir($file)) {
						echo '<a href="'.htmlspecialchars($filename).'/">'.htmlspecialchars($filename).'/</a>';
					} else {
						echo '<a href="'.Router::getHtmlReadyUri('/file/'.Session::getSessionId().'/'.$file).'" target="_blank">'.htmlspecialchars($filename).'</a>';
					}
				} else {
					if (Filesystem::is_dir($file)) {
						echo '<a class="disabled" href="">'.htmlspecialchars($filename).'/</a>';
					} else {
						echo '<a class="disabled" href="">'.htmlspecialchars($filename).'</a>';
					}
				}
				echo '</td>';


				// last modified
				echo '<td>';
				$mtime = Filesystem::filemtime($file);
				echo '<span id="file_mtime_'.$file_id.'" title="'.htmlspecialchars(date('l, F j, Y - g:i:s A', Filesystem::filemtime($file))).'" onclick="alert(document.getElementById(\'file_mtime_'.$file_id.'\').getAttribute(\'title\'));">';
				$mtimediff = time() - $mtime;
				if ($mtimediff < 60) {
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
				if (Filesystem::is_dir($file)) {
					$filecount = Filesystem::fileCount($file);
					if ($filecount != 1) {
						echo '<span id="file_size_'.$file_id.'" title="'.htmlspecialchars($filecount).' files" onclick="alert(document.getElementById(\'file_size_'.$file_id.'\').getAttribute(\'title\'));">';
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
						echo '<span id="file_size_'.$file_id.'" title="'.htmlspecialchars($filesize).' bytes" onclick="alert(document.getElementById(\'file_size_'.$file_id.'\').getAttribute(\'title\'));">';
					} else {
						echo '<span id="file_size_'.$file_id.'" title="'.htmlspecialchars($filesize).' byte" onclick="alert(document.getElementById(\'file_size_'.$file_id.'\').getAttribute(\'title\'));">';
					}
					if ($filesize < 1024) {
						echo sprintf("%d B", $filesize);
					} else if ($filesize < 1048576) {
						echo sprintf("%01.1f KiB", ($filesize / 1024));
					} else if ($filesize < 1073741824) {
						echo sprintf("%01.1f MiB", ($filesize / 1048576));
					} else {
						echo sprintf("%01.1f GiB", ($filesize / 1073741824));
					}
				}
				echo '</span>';
				echo '</td>';


				// download
				echo '<td>';
				if (Filesystem::is_file($file) && Filesystem::is_readable($file)) {
					echo '<a href="'.Router::getHtmlReadyUri('/download/'.$file).'">Download</a>';
				}
				echo '</td>';


				echo '</tr>';
			}
		}
	}
	echo '</tbody></table></div>';
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
