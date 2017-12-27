<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

Router::registerPage('login', function($subpage) {
	switch ($subpage) {
		case '':
			echo '<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge" />
	<link rel="stylesheet" href="' . Router::getHtmlReadyUri('/resource/main/css/normalize.css') . '" type="text/css" />
	<link rel="stylesheet" href="' . Router::getHtmlReadyUri('/resource/main/css/main.css') . '" type="text/css" />
	<link rel="stylesheet" href="' . Router::getHtmlReadyUri('/resource/main/css/forms.css') . '" type="text/css" />
	<link rel="stylesheet" href="' . Router::getHtmlReadyUri('/resource/main/css/login.css') . '" type="text/css" />
	<link rel="icon" href="' . Router::getHtmlReadyUri('/resource/main/img/favicon.ico') . '" />
	<title>Login - Garnet DeGelder\'s File Manager</title>
	<script src="'.Router::getHtmlReadyUri('/resource/main/js/jquery.js').'"></script>
</head>
<body class="login">
	<form action="' . htmlspecialchars(Router::getHtmlReadyUri('/login/action')) . '" method="post">
		<h1>Log into Garnet DeGelder\'s File Manager at ' . htmlspecialchars($_SERVER['SERVER_NAME']) . '.</h1>
		<input id="username" name="username" type="text" value="" placeholder="Username" autocomplete="on" autofocus="autofocus" class="u-full-width" />
		<input id="password" name="password" type="password" value="" placeholder="Password" class="u-full-width" />
		';
		Hooks::exec('_main.login.form');
		echo '
		<input id="submit" name="submit" type="submit" value="Log In" autocomplete="current-password" class="u-full-width button-primary" />
		<input name="csrf_token" type="hidden" value="'.htmlspecialchars(Session::get('_csrf_token')).'" />
		';
		Hooks::exec('_main.login.post_form');
		echo '
		<div class="message">';
	Session::lock();
	if (Session::isset('_auth_status')) {
		switch (Session::get('_auth_status')) {
			case Auth::ERROR_DOESNT_EXIST:
			case Auth::ERROR_INCORRECT_PASSWORD:
				echo '<p>Incorrect username or password</p>';
				break;
			case Auth::ERROR_DISABLED:
				echo '<p>User is disabled</p>';
				break;
		}
		Session::unset('_auth_status');
	}
	Session::unlock();
	echo '</div>
	';
	Hooks::exec('_main.login.message');
	echo '
	</form>
	<footer>
		<p>Garnet DeGelder\'s File Manager ' . htmlspecialchars(GARNETDG_FILEMANAGER_VERSION) . ' ' . GARNETDG_FILEMANAGER_COPYRIGHT . '<p>
	</footer>
</body>
</html>
';
			break;


		case 'action';
			if ($_POST['csrf_token'] === Session::get('_csrf_token')) {
				$authenticated = Auth::authenticate(false, $_POST['username'], $_POST['password']);
				if ($authenticated === true) {
					Session::lock();
					if (Session::isset('_login_target')) {
						header('Location: ' . Session::get('_login_target'));
						Session::unset('_login_target');
						Session::unlock();
						exit();
					} else {
						Session::unlock();
						Router::redirect('/');
					}
				} else {
					Session::set('_auth_status', $authenticated);
					Router::redirect('/login');
				}
			} else {
				Router::redirect('/');
			}
			break;


		default:
			Router::execErrorPage(404);
			break;
	}
});

Resources::register('main/css/login.css', function() {
	Resources::serveFile('_extensions/main/resources/css/login.css');
});
