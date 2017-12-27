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
				Hooks::exec('_main.admin.page');
				echo '</ul>';
				echo '</fieldset>';
				MainUiTemplate::footer();
				break;


			case 'users':
				require('admin/users.php');
				break;

			case 'users_in_groups':
				require('admin/users_in_groups.php');
				break;

			case 'groups':
				require('admin/groups.php');
				break;

			case 'shares_in_groups':
				require('admin/shares_in_groups.php');
				break;

			case 'shares':
				require('admin/shares.php');
				break;

			case 'settings':
				require('admin/settings.php');
				break;

			case 'apply':
				if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === Session::get('_csrf_token')) {
					require('admin/apply.php');
				} else {
					Router::execErrorPage(403);
				}
				break;

			default:
				Router::execErrorPage(404);
				break;
		}
	}
});

Resources::register('main/css/admin.css', function() {
	Resources::serveFile('_extensions/main/resources/css/admin.css');
});
