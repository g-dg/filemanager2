# Garnet DeGelder's File Manager 2.0 API
```

class Auth {
	const USER_TYPE_ADMIN
	const USER_TYPE_STANDARD
	const USER_TYPE_GUEST
	const ERROR_NOT_LOGGED_IN
	const ERROR_DOESNT_EXIST
	const ERROR_INCORRECT_PASSWORD
	const ERROR_DISABLED

	function authenticate($redirect = true, $username = null, $password = null)
	function isAuthenticated()
	function getCurrentUserId($authenticate = true)
	function getCurrentUserName($authenticate = true)
	function getCurrentUserType($authenticate = true)
	function getCurrentUserComment($authenticate = true)
	function logout($keep_session = true)
}


class Config {
	function get($key, $default = null)
}


class Database {
	$connection

	function connect()
	function lock()
	function unlock()
	function query($sql, $params = [], $enable_cache = true)
	function getVersionString()
}


class Filesystem {
	function copy($source, $dest)
	function file_exists($filename)
	function file_get_contents($filename)
	function file_put_contents($filename, $data)
	function fileatime($filename)
	function filemtime($filename)
	function filesize($filename)
	function filetype($filename)
	function fopen($filename, $mode)
	function is_dir($filename)
	function is_file($filename)
	function is_readable($filename)
	function is_writable($filename)
	function mkdir($filename)
	function rename($oldname, $newname)
	function rmdir($dirname)
	function touch($filename)
	function unlink($filename)
	function scandir($path)
	function fileCount($path)
	function processFileUpload($form_upload_element_name, $dest_filename)
	function getMimeType($filename, $is_filesystem_path = true)
}


class GlobalSettings {
	function get($key, $default = null, $force = false)
	function set($key, $value, $force = false)
	function isset($key, $force = false)
	function unset($key, $force = false)
}


class Groups {
	function addUser($group_id, $user_id)
	function removeUser($group_id, $user_id)
	function addShare($group_id, $share_id, $writable = false)
	function removeShare($group_id, $share_id)
	function getShareWritable($group_id, $share_id)
	function setShareWritable($group_id, $share_id, $writable)
	function userInGroup($group_id, $user_id)
	function shareInGroup($group_id, $share_id)
	function create($name, $enabled = true, $comment = '')
	function delete($group_id)
	function getId($name)
	function getName($group_id)
	function setName($group_id, $new_name)
	function getEnabled($group_id)
	function setEnabled($group_id, $enabled)
	function getComment($group_id)
	function setComment($group_id, $comment)
	function getUsersInGroup($group_id, $enabled_only = false)
	function getSharesInGroup($group_id, $enabled_only = false)
	function getAll($enabled_only = false)
}


class Hooks {
	function register($hook_name, $callback)
	function exec($hook_name, $arguments = [], $last_only = false)
	function isRegistered($hook_name)
}


class Loader {
	function loadAll()
	function registerInit($init_function)
}


class Log {
	const EMERG
	const ALERT
	const CRIT
	const ERR
	const WARNING
	const NOTICE
	const INFO
	const DEBUG

	function emergency($message)
	function alert($message)
	function critical($message)
	function error($message)
	function warning($message)
	function notice($message)
	function info($message)
	function debug($message)
	function log($level, $message)
}


class Plugins {
	function loadAll()
	function exists($plugin)
	function registerInit($init_function)
}


class Resources {
	function register($name, $callback)
	function serveFile($filename)
}


class Router {
	function registerPage($name, $callback)
	function registerErrorPage($http_error, $callback)
	function execCurrentPage()
	function execErrorPage($http_error)
	function getCurrentPage()
	function getCurrentParameters()
	function getCurrentPageAndParameters()
	function getHttpReadyUri($full_path, $params = [])
	function getHtmlReadyUri($full_path, $params = [])
	function redirect($full_path, $params = [])
	function getApplicationRelativeHttpPath($relative_path)
}


class Session {
	function start($session_id = null)
	function getSessionId()
	function set($key, $value)
	function get($key, $default = null)
	function unset($key)
	function isset($key)
	function garbageCollect()
	function unsetSession($destroy_session = false)
	function lock()
	function unlock()
}


class Shares {
	function create($name, $path, $enabled = true, $comment = '')
	function delete($share_id)
	function setName($share_id, $new_name)
	function setPath($share_id, $new_path)
	function setEnabled($share_id, $enabled)
	function setComment($share_id, $new_comment)
	function getId($name)
	function getName($share_id)
	function getPath($share_id)
	function getEnabled($share_id)
	function getComment($share_id)
	function canRead($share_id, $user_id)
	function canWrite($share_id, $user_id)
	function getAllAccessible($user_id)
	function getAll($enabled_only = false)
}


class Users {
	const USER_TYPE_ADMIN
	const USER_TYPE_STANDARD
	const USER_TYPE_GUEST

	function create($username, $password, $type = self::USER_TYPE_STANDARD, $enabled = true, $comment = '')
	function delete($user_id)
	function setName($user_id, $new_name)
	function setPassword($user_id, $password)
	function setEnabled($user_id, $enabled)
	function setType($user_id, $type)
	function setComment($user_id, $comment)
	function getId($username)
	function getName($user_id)
	function getEnabled($user_id)
	function getType($user_id)
	function getComment($user_id)
	function getGroups($user_id)
	function getCurrentId()
	function getAll($enabled_only = false)
}


class UserSettings {
	function get($key, $default, $user = null, $force = false)
	function set($key, $value, $user = null, $force = false)
	function isset($key, $user = null, $force = false)
	function unset($key, $user = null, $force = false)
}

```
