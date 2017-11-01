<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

if (
	isset($_POST['update'], $_GET['share'], $_GET['group'], $_POST['share_in_group']) &&
	($_POST['share_in_group'] === 'none' || $_POST['share_in_group'] === 'read-only' || $_POST['share_in_group'] === 'read-write')
) {
	switch ($_POST['share_in_group']) {
		case 'none':
			Session::set('_main_admin_status', Groups::removeShare($_GET['group'], $_GET['share']));
			break;
		case 'read-only':
			Session::set('_main_admin_status', Groups::addShare($_GET['group'], $_GET['share'], false));
			break;
		case 'read-write':
			Session::set('_main_admin_status', Groups::addShare($_GET['group'], $_GET['share'], true));
			break;
	}
} else {
	Session::set('_main_admin_status', false);
}
Router::redirect('/admin/shares_in_groups');
