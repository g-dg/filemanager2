<?php
use GarnetDG\FileManager as Main;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

/*************************************************************************************\
*                                                                                     *
*  This extension requires at least version 2.0.0 of Garnet DeGelder's File Manager.  *
*                                                                                     *
\*************************************************************************************/

Main\Hooks::register('_main.login.message', function() {
	echo '<div style="text-align: center;">'.Main\GlobalSettings::get('motd', '').'</div>';
});
