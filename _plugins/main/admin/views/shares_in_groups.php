<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

function mainUiAdminUsersInGroups()
{
	MainUiTemplate::header('Shares in Groups - Administration');

	MainUiTemplate::footer();
}
