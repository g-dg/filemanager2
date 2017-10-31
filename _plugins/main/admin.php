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
				echo '<li><a href="'.Router::getHtmlReadyUri('/admin/users').'">Users</a></li>';
				echo '<li><a href="'.Router::getHtmlReadyUri('/admin/users_in_groups').'">Users in Groups</a></li>';
				echo '<li><a href="'.Router::getHtmlReadyUri('/admin/groups').'">Groups</a></li>';
				echo '<li><a href="'.Router::getHtmlReadyUri('/admin/shares_in_groups').'">Shares in Groups</a></li>';
				echo '<li><a href="'.Router::getHtmlReadyUri('/admin/shares').'">Shares</a></li>';
				echo '<li><a href="'.Router::getHtmlReadyUri('/admin/settings').'">Global Settings</a></li>';
				echo '</ul>';
				echo '</fieldset>';
				MainUiTemplate::footer();
				break;


			case 'users':
				require_once('admin/views/users.php');
				mainUiAdminUsers();
				break;

			case 'users_in_groups':
				require_once('admin/views/users_in_groups.php');
				mainUiAdminUsersInGroups();
				break;

			case 'groups':
				require_once('admin/views/groups.php');
				mainUiAdminGroups();
				break;

			case 'shares_in_groups':
				require_once('admin/views/shares_in_groups.php');
				mainUiAdminSharesInGroups();
				break;

			case 'shares':
				require_once('admin/views/shares.php');
				mainUiAdminShares();
				break;

			case 'settings':
				require_once('admin/views/settings.php');
				mainUiAdminSettings();
				break;

			case 'action':
				if (isset($subpage_array[1])) {
					switch ($subpage_array[1]) {
						case 'users':
							require('admin/controllers/users.php');
							break;

						case 'users_in_groups':
							require('admin/controllers/users_in_groups.php');
							break;

						case 'groups':
							require('admin/controllers/groups.php');
							break;

						case 'shares_in_groups':
							require('admin/controllers/shares_in_groups.php');
							break;

						case 'shares':
							require('admin/controllers/shares.php');
							break;

						case 'settings':
							require('admin/controllers/settings.php');
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
