<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

class MainUiTemplate
{
	public static function header($title = null, $head_html = '')
	{
		Auth::authenticate(false);
		if (is_null($title)) {
			$title = 'Garnet DeGelder\'s File Manager';
		} else {
			$title .= ' - Garnet DeGelder\'s File Manager';
		}
		echo '<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge" />
	<link rel="stylesheet" href="' . Router::getHtmlReadyUri('/resource/main/css/normalize.css') . '" type="text/css" />
	<link rel="stylesheet" href="' . Router::getHtmlReadyUri('/resource/main/css/skeleton.css') . '" type="text/css" />
	<link rel="stylesheet" href="' . Router::getHtmlReadyUri('/resource/main/css/main.css') . '" type="text/css" />
	<link rel="icon" href="' . Router::getHtmlReadyUri('/resource/main/img/favicon.ico') . '" />
	<title>' . htmlspecialchars($title) . '</title>
	<script src="'.Router::getHtmlReadyUri('/resource/main/js/jquery.js').'" type="text/javascript"></script>
	' . $head_html . '
</head>
<body>
	<header class="header">
		<h1>'.htmlspecialchars($title).'</h1>
	</header>
	<nav class="nav">
		<ul>
			';
		echo '<li><a href="'.Router::getHtmlReadyUri('/browse').'">Browse</a></li>';
		echo '<li>';
		echo '<a href="'.Router::getHtmlReadyUri('/settings').'">Settings</a>';
		echo '<ul><li><a href="'.Router::getHtmlReadyUri('/account').'">My Account</a></li></ul>';
		echo '</li>';
		if (Auth::getCurrentUserType(false) === Auth::USER_TYPE_ADMIN) {
			echo '<li>';
				echo '<a href="'.Router::getHtmlReadyUri('/admin').'">Administration</a>';
				echo '<ul>';
					echo '<li><a href="'.Router::getHtmlReadyUri('/admin/users').'">Users</a></li>';
					echo '<li><a href="'.Router::getHtmlReadyUri('/admin/users_in_groups').'">Users in Groups</a></li>';
					echo '<li><a href="'.Router::getHtmlReadyUri('/admin/groups').'">Groups</a></li>';
					echo '<li><a href="'.Router::getHtmlReadyUri('/admin/shares_in_groups').'">Shares in Groups</a></li>';
					echo '<li><a href="'.Router::getHtmlReadyUri('/admin/shares').'">Shares</a></li>';
					echo '<li><a href="'.Router::getHtmlReadyUri('/admin/settings').'">Global Settings</a></li>';
					Hooks::exec('_main.admin.page');
				echo '</ul>';
			echo '</li>';
		}
		Hooks::exec('_main.template.shortcuts');
		echo '<li><a href="'.Router::getHtmlReadyUri('/about').'">About</a></li>';
		echo '<li>';
			if (Auth::isAuthenticated()) {
				echo str_replace(' ', '&nbsp;', htmlspecialchars(UserSettings::get('_main.account.full_name', Auth::getCurrentUserName())));
			} else {
				echo '<em>Not logged in</em>';
			}
			echo '<ul>';
				echo '<li><a href="'.Router::getHtmlReadyUri('/logout/switchuser').'">Switch User</a></li>';
				echo '<li><a href="'.Router::getHtmlReadyUri('/logout/logout').'">Log Out</a></li>';
			echo '</ul>';
		echo '</li>';
		echo '
		</ul>
	</nav>
	<main class="main">
';
	}

	public static function footer()
	{
		echo '
	</main>
	<footer class="footer">
		<p>Garnet DeGelder\'s File Manager ' . htmlspecialchars(GARNETDG_FILEMANAGER_VERSION) . ' at ' . htmlspecialchars($_SERVER['SERVER_NAME']) . '. ' . GARNETDG_FILEMANAGER_COPYRIGHT . '<p>
	</footer>
</body>
</html>
';
	}
}

Resources::register('main/img/favicon.ico', function() {
	Resources::serveFile('_extensions/main/resources/img/favicon.ico');
});

Resources::register('main/css/main.css', function() {
	Resources::serveFile('_extensions/main/resources/css/main.css');
});

Resources::register('main/css/normalize.css', function() {
	Resources::serveFile('_extensions/main/resources/css/normalize.css');
});

Resources::register('main/css/skeleton.css', function() {
	Resources::serveFile('_extensions/main/resources/css/skeleton.css');
});

Resources::register('main/js/jquery.js', function() {
	Resources::serveFile('_extensions/main/resources/js/jquery.min.js');
});

Resources::register('main/img/close.png', function() {
	Resources::serveFile('_extensions/main/resources/img/close.png');
});
