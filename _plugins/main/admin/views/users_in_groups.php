<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

function mainUiAdminUsersInGroups()
{
	MainUiTemplate::header('Users in Groups - Administration');

	MainUiTemplate::footer();
}
