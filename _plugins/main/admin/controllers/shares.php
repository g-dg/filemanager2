<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

if ( // create share
	isset($_POST['create'], $_POST['name'], $_POST['path'], $_POST['enabled'], $_POST['comment']) &&
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
	Session::set('_main_admin_status', Shares::create($_POST['name'], $_POST['path'], $enabled, $_POST['comment']));


} else if ( // update name
	isset($_POST['update_name'], $_GET['share'], $_POST['name'])
) {
	Session::set('_main_admin_status', Shares::setName($_GET['share'], $_POST['name']));


} else if ( // update path
	isset($_POST['update_path'], $_GET['share'], $_POST['path'])
) {
	Session::set('_main_admin_status', Shares::setPath($_GET['share'], $_POST['path']));


} else if ( // update enabled
	isset($_POST['update_enabled'], $_GET['share'], $_POST['enabled']) &&
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
	Session::set('_main_admin_status', Shares::setEnabled($_GET['share'], $enabled));


} else if ( // update comment
	isset($_POST['update_comment'], $_GET['share'], $_POST['comment'])
) {
	Session::set('_main_admin_status', Shares::setComment($_GET['share'], $_POST['comment']));


} else if ( // delete share
	isset($_POST['delete'], $_GET['share'])
) {
	Session::set('_main_admin_status', Shares::delete($_GET['share']));


} else {
	Session::set('_main_admin_status', false);
}
Router::redirect('/admin/shares');
