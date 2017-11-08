<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

if (
	isset($_GET['share'], $_GET['group'])
) {
	if (isset($_POST['allow'])) {
		Session::set('_main_admin_status', Groups::addShare($_GET['group'], $_GET['share'], false));
	} else if (isset($_POST['deny'])) {
		Session::set('_main_admin_status', Groups::removeShare($_GET['group'], $_GET['share']));
	} else if (isset($_POST['read-only'])) {
		Session::set('_main_admin_status', Groups::addShare($_GET['group'], $_GET['share'], false));
	} else if (isset($_POST['read-write'])) {
		Session::set('_main_admin_status', Groups::addShare($_GET['group'], $_GET['share'], true));
	} else {
		Session::set('_main_admin_status', false);
	}
} else {
	Session::set('_main_admin_status', false);
}
Router::redirect('/admin/shares_in_groups');
