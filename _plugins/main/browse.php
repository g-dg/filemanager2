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

	MainUiTemplate::header('/' . $path, '<link rel="stylesheet" href="' . Router::getHtmlReadyUri('/resource/main/browse.css') . '" type="text/css" />');
	echo '		<div class="overflow"><table class="u-full-width listing"><thead><tr><th></th><th>Name</th><th>Last Modified</th><th>Size</th><th>Download</th></tr></thead><tbody>';
	$dirlist = Filesystem::scandir($path);
	if ($dirlist) {
		natcasesort($dirlist);
		if ($path !== '') {
			echo '<tr><td><img src="'.Router::getHtmlReadyUri('/resource/main/icons/back.png').'" alt="[..]" /></td><td><a href="..">[Parent Directory]</a></td><td></td><td></td><td></td></tr>';
		}
		foreach ($dirlist as $filename) {
			if (substr($filename, 0, 1) !== '.') {
				$file = $path.'/'.$filename;
				echo '<tr>';

				if (Filesystem::is_file($file)) {
					echo '<td><img src="'.Router::getHtmlReadyUri('/resource/main/icons/generic.png').'" alt="[FILE]" /></td>';
				} else if (Filesystem::is_dir($file)) {
					echo '<td><img src="'.Router::getHtmlReadyUri('/resource/main/icons/folder.png').'" alt="[DIR]" /></td>';
				} else {
					echo '<td><img src="'.Router::getHtmlReadyUri('/resource/main/icons/unknown.png').'" alt="[?]" /></td>';
				}

				echo '<td>';
				if (Filesystem::is_dir($file)) {
					echo '<a href="'.htmlspecialchars($filename).'/">'.htmlspecialchars($filename).'/</a><br/>';
				} else {
					echo '<a href="'.Router::getHtmlReadyUri('/file/'.Session::getSessionId().'/'.$file).'" target="_blank">'.htmlspecialchars($filename).'</a><br/>';
				}
				echo '</td>';

				echo '<td>';
				echo date('Y-m-d H:i', Filesystem::filemtime($file));
				echo '</td>';

				echo '<td>';
				if (Filesystem::is_dir($file)) {
					echo Filesystem::fileCount($file);
				} else {
					echo Filesystem::filesize($file);
				}
				echo '</td>';

				echo '<td>';
				if (Filesystem::is_file($file)) {
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
Resources::register('main/icons/unknown.png', function(){
	Resources::serveFile('_plugins/main/resources/icons/unknown.png');
});
Resources::register('main/icons/video.png', function(){
	Resources::serveFile('_plugins/main/resources/icons/video.png');
});
