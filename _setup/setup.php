<?php
namespace GarnetDG\FileManager;
error_reporting(E_ALL);
ini_set('display_errors', 'On');
set_time_limit(0);

// needed for reading config
define('GARNETDG_FILEMANAGER_VERSION', '2.0.0');
class Log{const EMERG=0;const ALERT=1;const CRIT=2;const ERR=3;const WARNING=4;const NOTICE=5;const INFO=6;const DEBUG=7;}

if (
	isset($_POST['username'], $_POST['password1'], $_POST['password2']) &&
	$_POST['password1'] === $_POST['password2'] &&
	$_POST['username'] !== ''
) {
	chdir('..');
	require('_config.php');
	$database_file = $config['database_file'];
	if (
		file_exists($database_file) &&
		is_file($database_file) &&
		is_readable($database_file) &&
		is_writable($database_file) &&
		filesize($database_file) === 0
	) {

		$dbcon = new \PDO('sqlite:'.$database_file);
		$dbcon->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$dbcon->exec('PRAGMA journal_mode=WAL;PRAGMA synchronous=NORMAL;PRAGMA foreign_keys=ON;');
		$dbcon->beginTransaction();
		$dbcon->exec('PRAGMA user_version=2000000;DROP TABLE IF EXISTS "session_data";DROP TABLE IF EXISTS "sessions";DROP TABLE IF EXISTS "user_settings";DROP TABLE IF EXISTS "global_settings";DROP TABLE IF EXISTS "shares_in_groups";DROP TABLE IF EXISTS "users_in_groups";DROP TABLE IF EXISTS "shares";DROP TABLE IF EXISTS "groups";DROP TABLE IF EXISTS "users";CREATE TABLE "users"("id" INTEGER PRIMARY KEY AUTOINCREMENT,"name" TEXT NOT NULL UNIQUE,"password" BLOB NOT NULL,"enabled" INTEGER NOT NULL DEFAULT 1,"type" INTEGER NOT NULL DEFAULT 1,"comment" TEXT NOT NULL DEFAULT \'\');CREATE TABLE "groups"("id" INTEGER PRIMARY KEY AUTOINCREMENT,"name" TEXT NOT NULL UNIQUE,"enabled" INTEGER NOT NULL DEFAULT 1,"comment" TEXT NOT NULL DEFAULT \'\');CREATE TABLE "shares"("id" INTEGER PRIMARY KEY AUTOINCREMENT,"name" TEXT NOT NULL UNIQUE,"path" BLOB NOT NULL,"enabled" INTEGER NOT NULL DEFAULT 1,"comment" TEXT NOT NULL DEFAULT \'\');CREATE TABLE "users_in_groups"("user_id" INTEGER NOT NULL REFERENCES "users"("id") ON UPDATE CASCADE ON DELETE CASCADE,"group_id" INTEGER NOT NULL REFERENCES "groups"("id") ON UPDATE CASCADE ON DELETE CASCADE,UNIQUE("user_id", "group_id") ON CONFLICT REPLACE);CREATE TABLE "shares_in_groups"("group_id" INTEGER NOT NULL REFERENCES "groups"("id") ON UPDATE CASCADE ON DELETE CASCADE,"share_id" INTEGER NOT NULL REFERENCES "shares"("id") ON UPDATE CASCADE ON DELETE CASCADE,"writable" INTEGER NOT NULL DEFAULT 0,UNIQUE("group_id", "share_id") ON CONFLICT REPLACE);CREATE TABLE "global_settings"("key" TEXT NOT NULL UNIQUE ON CONFLICT REPLACE,"value" BLOB DEFAULT NULL);CREATE TABLE "user_settings"("key" TEXT NOT NULL,"user_id" INTEGER NOT NULL REFERENCES "users"("id") ON UPDATE CASCADE ON DELETE CASCADE,"value" BLOB DEFAULT NULL,UNIQUE("user_id", "key") ON CONFLICT REPLACE);CREATE TABLE "sessions"("session_id" BLOB NOT NULL,"timestamp" INTEGER NOT NULL DEFAULT (STRFTIME(\'%s\', \'now\')),UNIQUE("session_id"));CREATE TABLE "session_data"("session_id" BLOB NOT NULL REFERENCES "sessions"("session_id") ON UPDATE CASCADE ON DELETE CASCADE,"key" TEXT NOT NULL,"value" BLOB DEFAULT NULL,UNIQUE("session_id", "key") ON CONFLICT REPLACE);');
		$stmt = $dbcon->prepare('INSERT INTO "users"("name", "password", "type") VALUES (?, ?, 2);');
		$stmt->execute([$_POST['username'], password_hash($_POST['password1'], PASSWORD_DEFAULT)]);
		$dbcon->commit();

		echo '<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<title>Garnet DeGelder\'s File Manager 2.0 Setup</title>
	</head>
	<body>
		<h1>Setup Complete!</h1>
		<p>Setup is complete, click <a href="../">here</a> to use your new system.</p>
	</body>
</html>
';

	} else {
		header('Location: ./');
		exit();
	}
} else {
	header('Location: ./');
	exit();
}
