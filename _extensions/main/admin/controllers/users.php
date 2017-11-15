<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

if ( // create user
	isset($_POST['create'], $_POST['name'], $_POST['password1'], $_POST['password2'], $_POST['type'], $_POST['enabled'], $_POST['comment']) &&
	!empty($_POST['name']) &&
	$_POST['password1'] === $_POST['password2'] &&
	($_POST['type'] === 'admin' || $_POST['type'] === 'standard' || $_POST['type'] === 'guest') &&
	($_POST['enabled'] === 'enabled' || $_POST['enabled'] === 'disabled')
) {
	switch ($_POST['type']) {
		case 'admin':
			$type = Users::USER_TYPE_ADMIN;
			break;
		case 'standard':
			$type = Users::USER_TYPE_STANDARD;
			break;
		case 'guest':
			$type = Users::USER_TYPE_GUEST;
			break;
	}
	$enabled = ($_POST['enabled'] == 'enabled');
	Session::set('_main_admin_status', Users::create($_POST['name'], $_POST['password1'], $type, $enabled, $_POST['comment']));


} else if ( // update name
	isset($_POST['update_name'], $_GET['user'], $_POST['name']) &&
	!empty($_POST['name'])
) {
	Session::set('_main_admin_status', Users::setName($_GET['user'], $_POST['name']));


} else if ( // update password
	isset($_POST['update_password'], $_GET['user'], $_POST['password1'], $_POST['password2']) &&
	$_POST['password1'] === $_POST['password2']
) {
	Session::set('_main_admin_status', Users::setPassword($_GET['user'], $_POST['password1']));


} else if ( // update type
	isset($_POST['update_type'], $_GET['user'], $_POST['type']) &&
	($_POST['type'] === 'admin' || $_POST['type'] === 'standard' || $_POST['type'] === 'guest')
) {
	switch ($_POST['type']) {
		case 'admin':
			$type = Users::USER_TYPE_ADMIN;
			break;
		case 'standard':
			$type = Users::USER_TYPE_STANDARD;
			break;
		case 'guest':
			$type = Users::USER_TYPE_GUEST;
			break;
	}
	Session::set('_main_admin_status', Users::setType($_GET['user'], $type));


} else if ( // update enabled
	isset($_POST['update_enabled'], $_GET['user'], $_POST['enabled']) &&
	($_POST['enabled'] === 'enabled' || $_POST['enabled'] === 'disabled')
) {
	$enabled = ($_POST['enabled'] == 'enabled');
	Session::set('_main_admin_status', Users::setEnabled($_GET['user'], $enabled));


} else if ( // update comment
	isset($_POST['update_comment'], $_GET['user'], $_POST['comment'])
) {
	Session::set('_main_admin_status', Users::setComment($_GET['user'], $_POST['comment']));


} else if ( // delete user
	isset($_POST['delete'], $_GET['user'])
) {
	Session::set('_main_admin_status', Users::delete($_GET['user']));


} else {
	Session::set('_main_admin_status', false);
}
Router::redirect('/admin/users');
