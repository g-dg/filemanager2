<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

Router::registerPage('account', function($subpage) {
	Auth::authenticate();
	switch ($subpage) {
		case '':
			MainUiTemplate::header('My Account');
			Session::lock();
			if (Session::isset('_main_admin_status')) {
				echo '<div class="message">';
				if (Session::get('_main_admin_status')) {
					echo 'The last action completed successfully.';
				} else {
					echo 'A problem occurred during the last action.';
				}
				echo '</div>';
				Session::unset('_main_admin_status');
			}
			Session::unlock();
			echo '
		<fieldset>
			<legend>Username</legend>
			<p>Username: <code>'.htmlspecialchars(Auth::getCurrentUserName()).'</code></p>
		</fieldset>
';
			if (Auth::getCurrentUserType() !== Auth::USER_TYPE_GUEST) {
				echo '		<fieldset>
			<legend>Password</legend>
			<form action="'.Router::getHtmlReadyUri('/account/action').'" method="post">
				<input name="password1" type="password" value="" placeholder="Password" class="u-full-width" />
				<input name="password2" type="password" value="" placeholder="Password (again)" class="u-full-width" />
				<input name="csrf_token" type="hidden" value="'.htmlspecialchars(Session::get('_csrf_token')).'" />
				<input name="change_password" type="submit" value="Change Password" class="u-full-width button-primary" />
			</form>
		</fieldset>
';
			}
			echo '		<fieldset>
			<legend>Groups</legend>
			';
			$groups = Users::getGroups(Auth::getCurrentUserId());
			if (count($groups) > 0) {
				echo '<ul>
				';
				foreach ($groups as $group_id) {
					echo '<li>'.htmlspecialchars(Groups::getName($group_id)).'</li>';
				}
				echo '
			</ul>';
			} else {
				echo '<em>&lt;None&gt;</em>';
			} 
			echo '
		</fieldset>';
			MainUiTemplate::footer();
			break;
		case 'action':
			if (
				isset($_POST['change_password'], $_POST['password1'], $_POST['password2'], $_POST['csrf_token']) &&
				$_POST['password1'] === $_POST['password2'] &&
				$_POST['csrf_token'] === Session::get('_csrf_token')) {
				Session::set('_main_admin_status', Users::setPassword(Auth::getCurrentUserId(), $_POST['password1']));
			} else {
				Session::set('_main_admin_status', false);
			}
			Router::redirect('/account');
			break;
		default:
			Router::execErrorPage(404);
			break;
	}
});
