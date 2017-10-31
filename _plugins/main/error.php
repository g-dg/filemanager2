<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

function mainUiErrorPage($error, $page)
{
	http_response_code($error);

	$server_signature = '<em>Garnet DeGelder\'s File Manager ' . htmlspecialchars(GARNETDG_FILEMANAGER_VERSION) . ' at ' . htmlspecialchars($_SERVER['SERVER_NAME']) . ' port ' . htmlspecialchars($_SERVER['SERVER_PORT']) . '</em>';

	$title_postfix = ' - Garnet DeGelder\'s File Manager ' . htmlspecialchars(GARNETDG_FILEMANAGER_VERSION);

	switch ($error) {
		case 403:
			$title = '403 Forbidden' . $title_postfix;
			$body = '<h1>Forbidden</h1><p>You don\'t have permission to access /' . htmlspecialchars($page) . ' on this server.</p><hr />' . $server_signature;
			break;
		case 404:
			$title = '404 Not Found' . $title_postfix;
			$body = '<h1>Not Found</h1><p>The requested URL /' . htmlspecialchars($page) . ' was not found on this server.</p><hr />' . $server_signature;
			break;
		case 500:
			$title = '500 Internal Server Error' . $title_postfix;
			$body = '<h1>Internal Server Error</h1><p>The server encountered an internal error or misconfiguration and was unable to complete your request.</p><p>Please contact the server administrator at ' . htmlspecialchars($_SERVER['SERVER_ADMIN']) . ' to inform them of the time this error occurred, and the actions you performed just before this error.</p><p>More information about this error may be available in the server error log.</p><hr />' . $server_signature;
			break;
		default:
			$title = 'Error' . $title_postfix;
			$body = '<h1>Error</h1><p>A problem has occurred.</p><hr />' . $server_signature;
			break;
	}

	echo '<!DOCYTPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="ie=edge" />
		<title>' . $title . '</title>
		<link rel="icon" href="' . Router::getHtmlReadyUri('/resource/main/favicon.ico') . '" />
	</head>
	<body>
		' . $body . '
	</body>
</html>
';
	exit();
}

Router::registerErrorPage(403, __NAMESPACE__ . '\\mainUiErrorPage');
Router::registerErrorPage(404, __NAMESPACE__ . '\\mainUiErrorPage');
Router::registerErrorPage(500, __NAMESPACE__ . '\\mainUiErrorPage');
