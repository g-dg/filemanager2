<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

Router::registerPage('logout', function($subpage) {
	switch ($subpage) {
		case '':
			MainUiTemplate::header('Log Out');
			echo '<ul>
	<li><a href="'.Router::getHttpReadyUri('/logout/switchuser').'">Switch User</a></li>
	<li><a href="'.Router::getHttpReadyUri('/logout/logout').'">Log Out</a></li>
</ul>';
			MainUiTemplate::footer();
			break;
		case 'logout':
			Auth::logout(false);
			Router::redirect('/');
			break;
		case 'switchuser':
			Auth::logout(true);
			Router::redirect('/');
			break;
		default:
			Router::execErrorPage(404);
	}
});
