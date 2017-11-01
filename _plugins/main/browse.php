<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

Router::registerPage('browse', function($path) {
	Auth::authenticate();
	MainUiTemplate::header('/' . $path);
	echo '		<div class="overflow"><table class="u-full-width"><thead><tr><th>Name</th><th>Last Modified</th><th>Size</th><th>Download</th></tr></thead><tbody>';
	$dirlist = Filesystem::scandir($path);
	if ($dirlist) {
		natcasesort($dirlist);
		if ($path !== '') {
			echo '<tr><td><a href="..">[Parent Directory]</a></td><td></td><td></td><td></td></tr>';
		}
		foreach ($dirlist as $file) {
			if (substr($file, 0, 1) !== '.') {
				echo '<tr>';

				echo '<td>';
				if (Filesystem::is_dir($path.'/'.$file)) {
					echo '<a href="'.Router::getHtmlReadyUri('/browse/'.$path.'/'.$file).'/">'.htmlspecialchars($file).'</a><br/>';
				} else {
					echo '<a href="'.Router::getHtmlReadyUri('/file/'.Session::getSessionId().'/'.$path.'/'.$file).'" target="_blank">'.htmlspecialchars($file).'</a><br/>';
				}
				echo '</td>';

				echo '<td>';
				echo date('Y-m-d H:i', Filesystem::filemtime($path.'/'.$file));
				echo '</td>';

				echo '<td>';
				if (Filesystem::is_dir($path.'/'.$file)) {
					echo Filesystem::fileCount($path.'/'.$file);
				} else {
					echo Filesystem::filesize($path.'/'.$file);
				}
				echo '</td>';

				echo '<td>';
				if (Filesystem::is_file($path.'/'.$file)) {
					echo '<a href="'.Router::getHtmlReadyUri('/download/'.$path.'/'.$file).'">Download</a>';
				}
				echo '</td>';

				echo '</tr>';
			}
		}
	}
	echo '</tbody></table></div>';
	MainUiTemplate::footer();
});
