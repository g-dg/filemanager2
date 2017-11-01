<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

if ( // create group
	isset($_POST['create'], $_POST['name'],  $_POST['enabled'], $_POST['comment']) &&
	($_POST['enabled'] === 'enabled' || $_POST['enabled'] === 'disabled')
) {
	switch ($_POST['enabled']) {
		case 'enabled':
			$enabled = true;
			break;
		case 'disabled':
			$enabled = false;
			break;
	}
	Session::set('_main_admin_status', Groups::create($_POST['name'], $enabled, $_POST['comment']));


} else if ( // update name
	isset($_POST['update_name'], $_GET['group'], $_POST['name'])
) {
	Session::set('_main_admin_status', Groups::setName($_GET['group'], $_POST['name']));


} else if ( // update enabled
	isset($_POST['update_enabled'], $_GET['group'], $_POST['enabled']) &&
	($_POST['enabled'] === 'enabled' || $_POST['enabled'] === 'disabled')
) {
	switch ($_POST['enabled']) {
		case 'enabled':
			$enabled = true;
			break;
		case 'disabled':
			$enabled = false;
			break;
	}
	Session::set('_main_admin_status', Groups::setEnabled($_GET['group'], $enabled));


} else if ( // update comment
	isset($_POST['update_comment'], $_GET['group'], $_POST['comment'])
) {
	Session::set('_main_admin_status', Groups::setComment($_GET['group'], $_POST['comment']));


} else if ( // delete group
	isset($_POST['delete'], $_GET['group'])
) {
	Session::set('_main_admin_status', Groups::delete($_GET['group']));


} else {
	Session::set('_main_admin_status', false);
}
Router::redirect('/admin/groups');
