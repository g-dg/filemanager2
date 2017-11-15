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

	public static function authenticate($redirect = true, $username = null, $password = null)
	public static function isAuthenticated()
	public static function getCurrentUserId($authenticate = true)
	public static function getCurrentUserName($authenticate = true)
	public static function getCurrentUserType($authenticate = true)
	public static function getCurrentUserComment($authenticate = true)
	public static function checkPassword($user_id, $password)
	public static function logout($keep_session = true)
}


class Config {
	public static function get($key, $default = null)
}


class Database {
	public static $connection

	public static function connect()
	public static function lock()
	public static function unlock()
	public static function query($sql, $params = [], $enable_cache = true)
	public static function getVersionString()
}


class Filesystem {
	public static function copy($source, $dest)
	public static function file_exists($filename)
	public static function file_get_contents($filename)
	public static function file_put_contents($filename, $data)
	public static function fileatime($filename)
	public static function filemtime($filename)
	public static function filesize($filename)
	public static function filetype($filename)
	public static function fopen($filename, $mode)
	public static function is_dir($filename)
	public static function is_file($filename)
	public static function is_readable($filename)
	public static function is_writable($filename)
	public static function mkdir($filename)
	public static function rename($oldname, $newname)
	public static function rmdir($dirname)
	public static function touch($filename)
	public static function unlink($filename)
	public static function scandir($path)
	public static function fileCount($path)
	public static function processFileUpload($form_upload_element_name, $dest_filename)
	public static function getContentType($filename, $is_filesystem_path = false, $cache = false)
	public static function sanitizePath($path)
}


class GlobalSettings {
	public static function get($key, $default = null, $force = false)
	public static function set($key, $value, $force = false)
	public static function isset($key, $force = false)
	public static function unset($key, $force = false)
	public static function getAll($force = false)
}


class Groups {
	public static function addUser($group_id, $user_id)
	public static function removeUser($group_id, $user_id)
	public static function addShare($group_id, $share_id, $writable = false)
	public static function removeShare($group_id, $share_id)
	public static function getShareWritable($group_id, $share_id)
	public static function setShareWritable($group_id, $share_id, $writable)
	public static function userInGroup($group_id, $user_id)
	public static function shareInGroup($group_id, $share_id)
	public static function create($name, $enabled = true, $comment = '')
	public static function delete($group_id)
	public static function getId($name)
	public static function getName($group_id)
	public static function setName($group_id, $new_name)
	public static function getEnabled($group_id)
	public static function setEnabled($group_id, $enabled)
	public static function getComment($group_id)
	public static function setComment($group_id, $comment)
	public static function getUsersInGroup($group_id, $enabled_only = false)
	public static function getSharesInGroup($group_id, $enabled_only = false)
	public static function getAll($enabled_only = false)
}


class Hooks {
	public static function register($hook_name, $callback)
	public static function exec($hook_name, $arguments = [], $last_only = false)
	public static function isRegistered($hook_name)
}


class Loader {
	public static function loadAll()
	public static function registerInit($init_public static function)
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

	public static function emergency($message)
	public static function alert($message)
	public static function critical($message)
	public static function error($message)
	public static function warning($message)
	public static function notice($message)
	public static function info($message)
	public static function debug($message)
	public static function log($level, $message)
}


class Extensions {
	public static function loadAll()
	public static function exists($extension)
	public static function registerInit($init_public static function)
}


class Resources {
	public static function register($name, $callback)
	public static function serveFile($filename)
}


class Router {
	public static function registerPage($name, $callback)
	public static function registerErrorPage($http_error, $callback)
	public static function execCurrentPage()
	public static function execErrorPage($http_error)
	public static function getCurrentPage()
	public static function getCurrentParameters()
	public static function getCurrentPageAndParameters()
	public static function getHttpReadyUri($full_path, $params = [])
	public static function getHtmlReadyUri($full_path, $params = [])
	public static function redirect($full_path, $params = [])
	public static function getApplicationRelativeHttpPath($relative_path)
}


class Session {
	public static function start($session_id = null)
	public static function started()
	public static function getSessionId($start = true)
	public static function set($key, $value)
	public static function get($key, $default = null)
	public static function unset($key)
	public static function isset($key)
	public static function garbageCollect()
	public static function unsetSession($destroy_session = false)
	public static function lock()
	public static function unlock()
}


class Shares {
	public static function create($name, $path, $enabled = true, $comment = '')
	public static function delete($share_id)
	public static function setName($share_id, $new_name)
	public static function setPath($share_id, $new_path)
	public static function setEnabled($share_id, $enabled)
	public static function setComment($share_id, $new_comment)
	public static function getId($name)
	public static function getName($share_id)
	public static function getPath($share_id)
	public static function getEnabled($share_id)
	public static function getComment($share_id)
	public static function canRead($share_id, $user_id)
	public static function canWrite($share_id, $user_id)
	public static function getGroups($share_id)
	public static function getAllAccessible($user_id)
	public static function getAll($enabled_only = false)
}


class Users {
	const USER_TYPE_ADMIN
	const USER_TYPE_STANDARD
	const USER_TYPE_GUEST

	public static function create($username, $password, $type = self::USER_TYPE_STANDARD, $enabled = true, $comment = '')
	public static function delete($user_id)
	public static function setName($user_id, $new_name)
	public static function setPassword($user_id, $password)
	public static function setEnabled($user_id, $enabled)
	public static function setType($user_id, $type)
	public static function setComment($user_id, $comment)
	public static function getId($username)
	public static function getName($user_id)
	public static function getEnabled($user_id)
	public static function getType($user_id)
	public static function getComment($user_id)
	public static function getGroups($user_id)
	public static function getCurrentId()
	public static function getAll($enabled_only = false)
}


class UserSettings {
	public static function get($key, $default, $user = null, $force = false)
	public static function set($key, $value, $user = null, $force = false)
	public static function isset($key, $user = null, $force = false)
	public static function unset($key, $user = null, $force = false)
	public static function getAll($user = null, $force = false)
}

```
