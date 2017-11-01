<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

MainUiTemplate::header('Global Settings - Administration', '<link rel="stylesheet" href="' . Router::getHtmlReadyUri('/resource/main/admin.css') . '" type="text/css" />');

MainUiTemplate::footer();
