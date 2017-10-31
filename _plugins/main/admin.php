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

		switch ($subpage_array[0]) {
			case '':
				MainUiTemplate::header('Administration');
				echo '<fieldset><legend>Administration</legend>';
				echo '<ul>';
				echo '<li><a href="'.Router::getHttpReadyUri('/admin/users').'">Users</a></li>';
				echo '<li><a href="'.Router::getHttpReadyUri('/admin/users_in_groups').'">Users in Groups</a></li>';
				echo '<li><a href="'.Router::getHttpReadyUri('/admin/groups').'">Groups</a></li>';
				echo '<li><a href="'.Router::getHttpReadyUri('/admin/shares_in_groups').'">Shares in Groups</a></li>';
				echo '<li><a href="'.Router::getHttpReadyUri('/admin/shares').'">Shares</a></li>';
				echo '</ul>';
				echo '</fieldset>';
				MainUiTemplate::footer();
				break;


			case 'users':
				require('admin/views/users.php');
				break;



			case 'users_in_groups':
				MainUiTemplate::header('Users in Groups - Administration');

				MainUiTemplate::footer();
				break;



			case 'groups':
				require('admin/views/groups.php');
				break;



			case 'shares_in_groups':
				MainUiTemplate::header('Shares in Groups - Administration');

				MainUiTemplate::footer();
				break;



			case 'shares':
				require('admin/views/shares.php');
				break;



			case 'action':
				if (isset($subpage_array[1])) {
					switch ($subpage_array[1]) {
						case 'users':

							break;


						case 'users_in_groups':

							break;


						case 'groups':

							break;


						case 'shares_in_groups':

							break;


						case 'shares':

							break;

						
						default:
							Router::execErrorPage(404);
							break;
					}
				} else {
					Router::execErrorPage(404);
				}
				break;


			default:
				Router::execErrorPage(404);
				break;
		}
	}
});

Resources::register('main/admin.css', function() {
	Resources::serveFile('_plugins/main/resources/admin.css');
});
