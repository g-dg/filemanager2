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
	function query($sql, $params = [])
	function getVersionString()
}


class GlobalSettings {
	function get($key, $default = null, $force = false)
	function set($key, $value, $force = false)
	function isset($key, $force = false)
	function unset($key, $force = false)
}


class Groups {
	function addUser($group, $user)
	function removeUser($group, $user)
	function addShare($group, $share)
	function removeShare($group, $share)
	function userInGroup($group, $user = null)
	function shareInGroup($group, $share)
	function create($name, $enabled = true, $comment = '')
	function delete($group)
	function getId($name)
	function getName($group)
	function setName($group, $new_name)
	function getEnabled($group)
	function setEnabled($group, $enabled)
	function getComment($group)
	function setComment($group, $comment)
	function getUsersInGroup($group, $enabled_only = false)
	function getSharesInGroup($group, $enabled_only = false)
	function getAll($enabled_only = false)
}


class Hooks {
	function register($hook_name, $callback)
	function invoke($hook_name, $arguments, $last_only = false)
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


class Router {
	function registerPage($name, $callback)
	function registerErrorPage($http_error, $callback)
	function execCurrentPage()
	function execErrorPage($http_error)
	function getCurrentPage()
	function getCurrentParameters()
	function getCurrentPageAndParameters()
	function getHttpReadyUri($full_path, $params = [])
	function redirect($full_path, $params = [])
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
	const ACCESS_NONE
	const ACCESS_READ_ONLY
	const ACCESS_READ_WRITE

	function getUserAccessLevel($share, $user_id = null)
	function create($name, $path, $enabled = true, $comment = '')
	function delete($share)
	function setName($share, $new_name)
	function setPath($share, $new_path)
	function setEnabled($share, $enabled)
	function setComment($share, $new_comment)
	function getId($name)
	function getName($share)
	function getPath($share)
	function getEnabled($share)
	function getComment($share)
	function getAllAccessible($user_id = null)
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
