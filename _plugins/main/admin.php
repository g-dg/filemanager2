<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

Router::registerPage('admin', function($subpage) {
	if (Auth::getCurrentUserType() !== Auth::USER_TYPE_ADMIN) {
		Router::execErrorPage(403);
		exit();
	}
	
	$subpage_array = explode('/', trim($subpage, '/'));
	if (isset($subpage_array[0])) {

		if ($subpage_array[0] == '') {
			MainUiTemplate::head('Administration');

			MainUiTemplate::foot();


		} else if ($subpage_array[0] == 'users') {
			MainUiTemplate::head('Users - Administration');

			MainUiTemplate::foot();



		} else if ($subpage_array[0] == 'users_in_groups') {
			MainUiTemplate::head('Users in Groups - Administration');

			MainUiTemplate::foot();



		} else if ($subpage_array[0] == 'groups') {
			MainUiTemplate::head('Groups - Administration');

			MainUiTemplate::foot();



		} else if ($subpage_array[0] == 'shares_in_groups') {
			MainUiTemplate::head('Shares in Groups - Administration');

			MainUiTemplate::foot();



		} else if ($subpage_array[0] == 'shares') {
			MainUiTemplate::head('Shares - Administration');

			MainUiTemplate::foot();



		} else if ($subpage_array[0] == 'apply') {



		} else {
			Router::execErrorPage(404);
		}
	}
});
