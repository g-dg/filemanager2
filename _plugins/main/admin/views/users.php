<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}


MainUiTemplate::header('Users - Administration', '<link rel="stylesheet" href="' . Router::getHtmlReadyUri('/resource/main/admin.css') . '" type="text/css" />');

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
	<fieldset><legend>Create User</legend>
		<form action="'.Router::getHtmlReadyUri('/admin/action/users').'" method="post">
			<div class="row">
				<div class="six columns">
					<label for="create_name">Username:</label>
					<input id="create_name" name="name" type="text" value="" placeholder="Username" class="u-full-width" />
				</div>
				<div class="three columns">
					<label for="create_password1">Password:</label>
					<input id="create_password1" name="password1" type="password" value="" placeholder="Password" class="u-full-width" />
				</div>
				<div class="three columns">
					<label for="create_password1">Password (again):</label>
					<input id="create_password2" name="password2" type="password" value="" placeholder="Password (again)" class="u-full-width" />
				</div>
			</div>
			<div class="row">
				<div class="three columns">
					<label for="create_type">Type:</label>
					<select id="create_type" name="type" class="u-full-width"><option value="admin">Administrator</option><option value="standard" selected="selected">Standard User</option><option value="guest">Guest</option></select>
				</div>
				<div class="three columns">
					<label for="create_enabled">Enabled:</label>
					<select id="create_enabled" name="enabled" class="u-full-width"><option value="enabled" selected="selected">Enabled</option><option value="disabled">Disabled</option></select>
				</div>
				<div class="six columns">
					<label for="create_comment">Comment:</label>
					<textarea id="create_comment" name="comment" placeholder="Comment" class="u-full-width"></textarea>
				</div>
			</div>
			<div class="row">
				<div class="twelve columns">
					<input name="csrf_token" type="hidden" value="'.htmlspecialchars(Session::get('_csrf_token')).'" />
					<label for="create">Create User:</label>
					<input id="create" name="create" type="submit" value="Create User" class="u-full-width button-primary" />
					<input id="create_cancel" name="reset" type="reset" value="Cancel" class="u-full-width" />
				</div>
			</div>
		</form>
	</fieldset>
	<div class="overflow">
		<fieldset>
			<legend>Users</legend>
';
Database::lock();
$users = Users::getAll();
if (count($users) > 0) {
	echo '			<div class="table">
				<div class="thead">
					<div>Username</div>
					<div>Password</div>
					<div>Type</div>
					<div>Enabled</div>
					<div>Comment</div>
					<div>Delete</div>
					<div>Cancel</div>
				</div>
';
	foreach (Users::getAll() as $user_id) {
		echo '				';

		echo '<form action="'.Router::getHtmlReadyUri('/admin/action/users', ['user' => $user_id]).'" method="post">';

		echo '<div>';
		echo '<input id="name_'.htmlspecialchars($user_id).'" name="name" type="text" value="'.htmlspecialchars(Users::getName($user_id)).'" placeholder="Name" />';
		echo '<input id="update_name_'.htmlspecialchars($user_id).'" name="update_name" type="submit" value="Update" />';
		echo '</div>';

		echo '<div>';
		echo '<input id="password1_'.htmlspecialchars($user_id).'" name="password1" type="password" value="" placeholder="Password" />';
		echo '<input id="password2_'.htmlspecialchars($user_id).'" name="password2" type="password" value="" placeholder="Password (again)" />';
		echo '<input id="update_password_'.htmlspecialchars($user_id).'" name="update_password" type="submit" value="Update" />';
		echo '</div>';

		echo '<div>';
		switch (Users::getType($user_id)) {
			case Users::USER_TYPE_ADMIN:
				echo '<select id="type_'.htmlspecialchars($user_id).'" name="type"><option value="admin" selected="selected">Administrator</option><option value="standard">Standard User</option><option value="guest">Guest</option></select>';
				break;
			case Users::USER_TYPE_STANDARD:
				echo '<select id="type_'.htmlspecialchars($user_id).'" name="type"><option value="admin">Administrator</option><option value="standard" selected="selected">Standard User</option><option value="guest">Guest</option></select>';
				break;
			case Users::USER_TYPE_GUEST:
				echo '<select id="type_'.htmlspecialchars($user_id).'" name="type"><option value="admin">Administrator</option><option value="standard">Standard User</option><option value="guest" selected="selected">Guest</option></select>';
				break;
			default:
				echo '<select id="type_'.htmlspecialchars($user_id).'" name="type"><option value="admin">Administrator</option><option value="standard">Standard User</option><option value="guest">Guest</option></select>';
				break;
		}
		echo '<input id="update_type_'.htmlspecialchars($user_id).'" name="update_type" type="submit" value="Update" />';
		echo '</div>';

		echo '<div>';
		if (Users::getEnabled($user_id)) {
			echo '<select id="enabled_'.htmlspecialchars($user_id).'" name="enabled"><option value="enabled" selected="selected">Enabled</option><option value="disabled">Disabled</option></select>';
		} else {
			echo '<select id="enabled_'.htmlspecialchars($user_id).'" name="enabled"><option value="enabled">Enabled</option><option value="disabled" selected="selected">Disabled</option></select>';
		}
		echo '<input id="update_enabled_'.htmlspecialchars($user_id).'" name="update_enabled" type="submit" value="Update" />';
		echo '</div>';

		echo '<div>';
		echo '<textarea id="comment_'.htmlspecialchars($user_id).'" name="comment" placeholder="Comment">'.htmlspecialchars(Users::getComment($user_id)).'</textarea>';
		echo '<input id="update_comment_'.htmlspecialchars($user_id).'" name="update_comment" type="submit" value="Update" />';
		echo '</div>';

		echo '<div>';
		echo '<input id="delete_'.htmlspecialchars($user_id).'" name="delete" type="submit" value="Delete" onclick="return confirm(\'Delete user &quot;\'+document.getElementById(\'name_'.htmlspecialchars($user_id).'\').getAttribute(\'value\')+\'&quot;?\');" />';
		echo '</div>';

		echo '<div>';
		echo '<input id="cancel_'.htmlspecialchars($user_id).'" name="reset" type="reset" value="Cancel" />';
		echo '</div>';

		echo '<input name="csrf_token" type="hidden" value="'.htmlspecialchars(Session::get('_csrf_token')).'" />';
		
		echo '</form>';

		echo PHP_EOL;
	}
	echo '			</div>
';
} else {
	echo '			&lt;<em>None</em>&gt;'.PHP_EOL.PHP_EOL;
}
Database::unlock();
echo '		</fieldset>
	</div>
';

MainUiTemplate::footer();
