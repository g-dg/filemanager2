<?php
namespace GarnetDG\FileManager;
error_reporting(E_ALL);
ini_set('display_errors', 'On');
set_time_limit(0);

require('config.php');
chdir('../../../');

$old_db = $config['old_database'];

if (
	!is_null($old_db) &&
	file_exists($old_db) &&
	is_file($old_db) &&
	is_readable($old_db) &&
	is_writable($old_db)
) {

	// open the old database
	$olddbcon = new \PDO('sqlite:'.$old_db);

	// needed for reading config
	define('GARNETDG_FILEMANAGER_VERSION', '2.0.0');
	class Log{const EMERG=0;const ALERT=1;const CRIT=2;const ERR=3;const WARNING=4;const NOTICE=5;const INFO=6;const DEBUG=7;}
	// load the new config file
	require('_config.php');
	$new_db = $config['database_file'];


	if (
		file_exists($new_db) &&
		is_file($new_db) &&
		is_readable($new_db) &&
		is_writable($new_db) &&
		filesize($new_db) === 0
	) {
		// create the new database
		$newdbcon = new \PDO('sqlite:'.$new_db);
		$newdbcon->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$newdbcon->exec('PRAGMA journal_mode=WAL;PRAGMA synchronous=NORMAL;PRAGMA foreign_keys=ON;');
		$newdbcon->beginTransaction();
		$newdbcon->exec('PRAGMA user_version=2000000;DROP TABLE IF EXISTS "session_data";DROP TABLE IF EXISTS "sessions";DROP TABLE IF EXISTS "user_settings";DROP TABLE IF EXISTS "global_settings";DROP TABLE IF EXISTS "shares_in_groups";DROP TABLE IF EXISTS "users_in_groups";DROP TABLE IF EXISTS "shares";DROP TABLE IF EXISTS "groups";DROP TABLE IF EXISTS "users";CREATE TABLE "users"("id" INTEGER PRIMARY KEY AUTOINCREMENT,"name" TEXT NOT NULL UNIQUE,"password" BLOB NOT NULL,"enabled" INTEGER NOT NULL DEFAULT 1,"type" INTEGER NOT NULL DEFAULT 1,"comment" TEXT NOT NULL DEFAULT \'\');CREATE TABLE "groups"("id" INTEGER PRIMARY KEY AUTOINCREMENT,"name" TEXT NOT NULL UNIQUE,"enabled" INTEGER NOT NULL DEFAULT 1,"comment" TEXT NOT NULL DEFAULT \'\');CREATE TABLE "shares"("id" INTEGER PRIMARY KEY AUTOINCREMENT,"name" TEXT NOT NULL UNIQUE,"path" BLOB NOT NULL,"enabled" INTEGER NOT NULL DEFAULT 1,"comment" TEXT NOT NULL DEFAULT \'\');CREATE TABLE "users_in_groups"("user_id" INTEGER NOT NULL REFERENCES "users"("id") ON UPDATE CASCADE ON DELETE CASCADE,"group_id" INTEGER NOT NULL REFERENCES "groups"("id") ON UPDATE CASCADE ON DELETE CASCADE,UNIQUE("user_id", "group_id") ON CONFLICT REPLACE);CREATE TABLE "shares_in_groups"("group_id" INTEGER NOT NULL REFERENCES "groups"("id") ON UPDATE CASCADE ON DELETE CASCADE,"share_id" INTEGER NOT NULL REFERENCES "shares"("id") ON UPDATE CASCADE ON DELETE CASCADE,"writable" INTEGER NOT NULL DEFAULT 0,UNIQUE("group_id", "share_id") ON CONFLICT REPLACE);CREATE TABLE "global_settings"("key" TEXT NOT NULL UNIQUE ON CONFLICT REPLACE,"value" BLOB DEFAULT NULL);CREATE TABLE "user_settings"("key" TEXT NOT NULL,"user_id" INTEGER NOT NULL REFERENCES "users"("id") ON UPDATE CASCADE ON DELETE CASCADE,"value" BLOB DEFAULT NULL,UNIQUE("user_id", "key") ON CONFLICT REPLACE);CREATE TABLE "sessions"("session_id" BLOB NOT NULL,"timestamp" INTEGER NOT NULL DEFAULT (STRFTIME(\'%s\', \'now\')),UNIQUE("session_id"));CREATE TABLE "session_data"("session_id" BLOB NOT NULL REFERENCES "sessions"("session_id") ON UPDATE CASCADE ON DELETE CASCADE,"key" TEXT NOT NULL,"value" BLOB DEFAULT NULL,UNIQUE("session_id", "key") ON CONFLICT REPLACE);');

		// load users
		$loaded_users = [];
		foreach ($olddbcon->query('SELECT * FROM "USERS";') as $record) {
			$loaded_users[] = $record;
		}
		// load shares
		$loaded_shares = [];
		foreach ($olddbcon->query('SELECT * FROM "SHARES";') as $record) {
			$loaded_shares[] = $record;
		}

		// close old db
		$olddbcon = null;

		// create users
		foreach($loaded_users as $user) {
			$username = $user['NAME'];
			$password = '$6$$'.$user['PASSWORD_SHA512'];

			$stmt = $newdbcon->prepare('INSERT INTO "users"("name", "password") VALUES (?, ?);');
			$stmt->execute([$username, $password]);

			// get user id
			$stmt = $newdbcon->prepare('SELECT "id" FROM "users" WHERE "name" = ?;');
			$stmt->execute([$username]);
			$user_id = (int)$stmt->fetchAll()[0][0];

			// create groups and add the users to them
			foreach (explode(',', $user['GROUPS']) as $group) {
				// if the user is in the "root" group, they are an administrator
				if ($group === 'root') {
					$stmt = $newdbcon->prepare('UPDATE "users" SET "type" = 2 WHERE "name" = ?;');
					$stmt->execute([$username]);
				}

				// check if group exists
				$stmt = $newdbcon->prepare('SELECT COUNT() FROM "groups" WHERE "name" = ?;');
				$stmt->execute([$group]);
				$group_exists = $stmt->fetchAll()[0][0] > 0;

				if (!$group_exists) {
					$stmt = $newdbcon->prepare('INSERT INTO "groups"("name") VALUES (?);');
					$stmt->execute([$group]);
				}

				// add the user to the group
				$stmt = $newdbcon->prepare('SELECT "id" FROM "groups" WHERE "name" = ?;');
				$stmt->execute([$group]);
				$group_id = (int)$stmt->fetchAll()[0][0];

				$stmt = $newdbcon->prepare('INSERT INTO "users_in_groups"("user_id", "group_id") VALUES (?, ?);');
				$stmt->execute([$user_id, $group_id]);
			}
		}

		// create shares
		foreach($loaded_shares as $share) {
			$name = $share['NAME'];
			$path = $share['PATH'];

			$stmt = $newdbcon->prepare('INSERT INTO "shares"("name", "path") VALUES (?, ?);');
			$stmt->execute([$name, $path]);

			// get share id
			$stmt = $newdbcon->prepare('SELECT "id" FROM "shares" WHERE "name" = ?;');
			$stmt->execute([$name]);
			$share_id = (int)$stmt->fetchAll()[0][0];

			// create groups and add the shares to them
			foreach (explode(',', $share['GROUPS_ACCESS_FILES']) as $group) {
				$stmt = $newdbcon->prepare('SELECT COUNT() FROM "groups" WHERE "name" = ?;');
				$stmt->execute([$group]);
				$group_exists = $stmt->fetchAll()[0][0] > 0;

				if (!$group_exists) {
					$stmt = $newdbcon->prepare('INSERT INTO "groups"("name") VALUES (?);');
					$stmt->execute([$group]);
				}

				// add the share to the group
				$stmt = $newdbcon->prepare('SELECT "id" FROM "groups" WHERE "name" = ?;');
				$stmt->execute([$group]);
				$group_id = (int)$stmt->fetchAll()[0][0];

				$stmt = $newdbcon->prepare('INSERT INTO "shares_in_groups"("share_id", "group_id") VALUES (?, ?);');
				$stmt->execute([$share_id, $group_id]);
			}
		}

		$newdbcon->commit();
		echo '<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<title>Garnet DeGelder\'s File Manager Setup</title>
	</head>
	<body>
		<h1>Upgrade Complete!</h1>
		<p>Upgrade is complete, click <a href="../../../">here</a> to use your new system.</p>
		<p><em>Note: none of the shares will be writable.</em></p>
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
