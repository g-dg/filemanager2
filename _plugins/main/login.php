<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

Router::registerPage('login', function($subpage) {
	switch ($subpage) {
		case '':
			MainUiTemplate::head('Log In', '<link rel="stylesheet" type="text/css" href="' . Router::getHttpReadyUri('/resource/main/login.css') . '" />');
			echo '<form action="' . htmlspecialchars(Router::getHttpReadyUri('/login/go')) . '" method="post">
	<h1 class="title">Log into Garnet DeGelder\'s File Manager on ' . htmlspecialchars($_SERVER['SERVER_NAME']) . '.</h1>
	<input id="username" name="username" type="text" value="" placeholder="Username" autocomplete="on" autofocus="autofocus" />
	<input id="password" name="password" type="password" value="" placeholder="Password" />
	<input id="submit" name="submit" type="submit" value="Log In" autocomplete="current-password" />
	<input id="csrf_token" name="csrf_token" type="hidden" value="' . Session::get('_csrf_token') . '" />
	<div class="message">';
	if (Session::isset('_auth_status')) {
		switch (Session::get('_auth_status')) {
			case Auth::ERROR_DOESNT_EXIST:
				echo 'User doesn\'t exist';
				break;
			case Auth::ERROR_INCORRECT_PASSWORD:
				echo 'Incorrect password';
				break;
			case Auth::ERROR_DISABLED:
				echo 'User is disabled';
				break;
		}
		Session::unset('_auth_status');
	}
	echo '</div>
</form>';
			MainUiTemplate::foot();
			break;
		case 'go';
			if ($_POST['csrf_token'] === Session::get('_csrf_token')) {
				$authenticated = Auth::authenticate(false, $_POST['username'], $_POST['password']);
				if ($authenticated === true) {
					header('Location: ' . Session::get('_login_target'));
					Session::unset('_login_target');
				} else {
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

Resources::register('main/login.css', function() {
	Resources::serveFile('_plugins/main/resources/login.css');
});
