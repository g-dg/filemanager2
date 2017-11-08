<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

if (
	isset($_GET['user'], $_GET['group'], $_POST['csrf_token']) &&
	(isset($_POST['add']) || $_POST['remove'])
) {
	if (isset($_POST['add'])) {
		Session::set('_main_admin_status', Groups::addUser($_GET['group'], $_GET['user']));
	} else if (isset($_POST['remove'])) {
		Session::set('_main_admin_status', Groups::removeUser($_GET['group'], $_GET['user']));
	}
} else {
	Session::set('_main_admin_status', false);
}
Router::redirect('/admin/users_in_groups');
