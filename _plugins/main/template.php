<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

class MainUiTemplate
{
	const COPYRIGHT_NOTICE = 'Copyright &copy; 2017 Garnet DeGelder.';

	public static function header($title = null, $head_html = '')
	{
		if (is_null($title)) {
			$title = 'Garnet DeGelder\'s File Manager ' . GARNETDG_FILEMANAGER_VERSION;
		} else {
			$title .= ' - Garnet DeGelder\'s File Manager ' . GARNETDG_FILEMANAGER_VERSION;
		}
		echo '<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="' . Router::getHttpReadyUri('/resource/main/main.css') . '" type="text/css" />
	<link rel="icon" href="' . Router::getHttpReadyUri('/resource/main/favicon.ico') . '" />
	<title>' . htmlspecialchars($title) . '</title>
	' . $head_html . '
</head>
<body>
';
	}
	public static function footer()
	{
		echo '
	<footer>
		<hr />
		<p>Garnet DeGelder\'s File Manager ' . htmlspecialchars(GARNETDG_FILEMANAGER_VERSION) . '<p>
		<p>' . self::COPYRIGHT_NOTICE . '</p>
	</footer>
</body>
</html>
';
	}
}

Resources::register('main/favicon.ico', function() {
	Resources::serveFile('_plugins/main/resources/favicon.ico');
});

Resources::register('main/main.css', function() {
	Resources::serveFile('_plugins/main/resources/main.css');
});

