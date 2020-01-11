<?php
use GarnetDG\FileManager as Main;

Main\Hooks::register('_main.browse.head', function($path) {
	echo '<li><a href="'.Main\Router::getHtmlReadyUri('/m3u_playlist/' . Main\Session::getSessionId() . $path . '.m3u').'" target="_blank">Playlist</a></li>';
});

Main\Router::registerPage('m3u_playlist', function($path) {
	// get session ID
	$path_array = explode('/', trim($path, '/'));
	Main\Session::start($path_array[0]);
	Main\Auth::authenticate();
	array_shift($path_array);
	$path = implode('/', $path_array);

	// remove ".m3u" from path
	$path = substr($path, 0, strlen($path) - 4);

	$dirlist = Main\Filesystem::scandir($path);
	if ($dirlist) {
		natcasesort($dirlist);

		$playlist = '#EXTM3U'.PHP_EOL;

		foreach ($dirlist as $filename) {
			$file = $path.'/'.$filename;
			if (substr($file, 0, 1) !== '.' && Main\Filesystem::is_file($file)) {
				$filetype = explode('/', Main\Filesystem::getContentType($file, false, true), 2)[0];
				if ($filetype === 'audio' || $filetype === 'video') {
					$playlist .= '#EXTINF:-1, - ' . pathinfo($filename)['filename'] . PHP_EOL;
					$playlist .= $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . Main\Router::getHttpReadyUri('/file/' . Main\Session::getSessionId() . '/' . $file) . PHP_EOL;
				}
			}
		}
		header('Content-Type: audio/x-mpegurl');
		header('Content-Length: ' . strlen($playlist));
		echo $playlist;
	} else {
		http_response_code(404);
	}
});
