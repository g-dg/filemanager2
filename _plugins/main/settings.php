<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

Router::registerPage('settings', function($subpage) {
	Auth::authenticate();
	switch ($subpage) {
		case '':
			MainUiTemplate::header('Settings');

			MainUiTemplate::footer();
			break;
		

		case 'action':

			break;
		

		default:
			Router::execErrorPage(404);
			break;
	}
});
