<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

Router::registerPage('logout', function($subpage) {
	switch ($subpage) {
		case '':
			Auth::logout(false);
			break;
		case 'switchuser':
			Auth::logout(true);
			break;
		default:
			Router::execErrorPage(404);
	}
	Router::redirect('/');
});
