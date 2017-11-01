<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

if (
	isset($_POST['set'], $_POST['set_key'], $_POST['set_value'])
) {
	GlobalSettings::set($_POST['set_key'], $_POST['set_value']);
} else if (
	isset($_POST['unset'], $_POST['unset_key'])
) {
	GlobalSettings::unset($_POST['unset_key']);
}
Router::redirect('/admin/settings');
