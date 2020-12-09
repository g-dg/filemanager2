<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

// Base URL and index page name (used to create links)
$config['base_uri'] = null;
$config['index_page'] = 'index.php';

// SQLite3 database file
// Must be readable and writable and in a readable and writable directory
$config['database_file'] = '_database.sqlite3';

// Log file
// Must be writable
$config['log_file'] = '_log.txt';

// Log level
$config['log_level'] = Log::NOTICE;

// Debug mode (enables error reporting)
$config['debug'] = false;

// Default username (only used for initial setup)
$config['setup_username'] = 'admin';
// Default password (only used for initial setup)
$config['setup_password'] = 'password';
