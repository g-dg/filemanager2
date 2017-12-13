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
			if (Session::isset('_main_account_status')) {
				echo '<div class="message">';
				if (Session::get('_main_account_status')) {
					echo 'The last action completed successfully.';
				} else {
					echo 'A problem occurred during the last action.';
				}
				echo '</div>
';
				Session::unset('_main_account_status');
			}
			Session::unlock();
			echo '		<fieldset>
			<legend>Username</legend>
			Username: <code title="User ID: '.htmlspecialchars(Auth::getCurrentUserId()).'">'.str_replace(' ', '&nbsp;', htmlspecialchars(Auth::getCurrentUserName())).'</code>
		</fieldset>
';
			echo '		<fieldset>
			<legend>Full Name</legend>
			<form action="'.Router::getHtmlReadyUri('/account/action').'" method="post">
				<input name="csrf_token" type="hidden" value="'.htmlspecialchars(Session::get('_csrf_token')).'" />
				
				<input id="fullname" name="fullname" type="text" value="'.htmlspecialchars(UserSettings::get('_main.account.full_name', Auth::getCurrentUserName())).'" class="u-full-width" />
				<input name="change_fullname" type="submit" value="Save" class="u-full-width button-primary" />
			</form>
		</fieldset>
';
			if (Auth::getCurrentUserType() !== Auth::USER_TYPE_GUEST) {
				echo '		<fieldset>
			<legend>Password</legend>
			<form action="'.Router::getHtmlReadyUri('/account/action').'" method="post">
				<input name="csrf_token" type="hidden" value="'.htmlspecialchars(Session::get('_csrf_token')).'" />

				<div class="form-inputs">
					<label for="old_password">Current password:</label>
					<input id="old_password" name="old_password" type="password" value="" class="u-full-width" />

					<label for="new_password1">New password:</label>
					<input id="new_password1" name="new_password1" type="password" value="" class="u-full-width" />

					<label for="new_password2">Confirm new password:</label>
					<input id="new_password2" name="new_password2" type="password" value="" class="u-full-width" />
				</div>
				
				<div class="form-buttons">
					<input id="change_password" name="change_password" type="submit" value="Change Password" class="u-full-width button-primary" />
				</div>
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
				isset($_POST['change_password'], $_POST['old_password'], $_POST['new_password1'], $_POST['new_password2'], $_POST['csrf_token']) &&
				$_POST['new_password1'] === $_POST['new_password2'] &&
				Auth::checkPassword(Auth::getCurrentUserId(), $_POST['old_password']) &&
				$_POST['csrf_token'] === Session::get('_csrf_token')
			) {
				Session::set('_main_account_status', Users::setPassword(Auth::getCurrentUserId(), $_POST['new_password1']));

			} else if (
				isset($_POST['change_fullname'], $_POST['fullname'], $_POST['csrf_token']) &&
				$_POST['csrf_token'] === Session::get('_csrf_token')
			) {
				UserSettings::set('_main.account.full_name', $_POST['fullname']);
				Session::set('_main_account_status', true);

			} else {
				Session::set('_main_account_status', false);
			}
			Router::redirect('/account');
			break;
		default:
			Router::execErrorPage(404);
			break;
	}
});
