<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

if (
	isset($_POST['update'], $_GET['user'], $_GET['group'], $_POST['user_in_group']) &&
	($_POST['user_in_group'] === 'true' || $_POST['user_in_group'] === 'false')
) {
	if ($_POST['user_in_group'] == 'true') {
		Session::set('_main_admin_status', Groups::addUser($_GET['group'], $_GET['user']));
	} else {
		Session::set('_main_admin_status', Groups::removeUser($_GET['group'], $_GET['user']));
	}
} else {
	Session::set('_main_admin_status', false);
}
Router::redirect('/admin/users_in_groups');
