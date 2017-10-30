<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

Router::registerPage('admin', function($subpage) {
	if (Auth::getCurrentUserType() !== Auth::USER_TYPE_ADMIN) {
		Router::execErrorPage(403);
		exit();
	}
	
	$subpage_array = explode('/', trim($subpage, '/'));
	if (isset($subpage_array[0])) {

		switch ($subpage_array[0]) {
			case '':
				MainUiTemplate::head('Administration');
				echo '<fieldset><legend>Administration</legend>';
				echo '<ul>';
				echo '<li><a href="'.Router::getHttpReadyUri('/admin/users').'">Users</a></li>';
				echo '<li><a href="'.Router::getHttpReadyUri('/admin/users_in_groups').'">Users in Groups</a></li>';
				echo '<li><a href="'.Router::getHttpReadyUri('/admin/groups').'">Groups</a></li>';
				echo '<li><a href="'.Router::getHttpReadyUri('/admin/shares_in_groups').'">Shares in Groups</a></li>';
				echo '<li><a href="'.Router::getHttpReadyUri('/admin/shares').'">Shares</a></li>';
				echo '</ul>';
				echo '</fieldset>';
				MainUiTemplate::foot();
				break;


			case 'users':
				MainUiTemplate::head('Users - Administration', '<link rel="stylesheet" href="' . Router::getHttpReadyUri('/resource/main/admin.css') . '" type="text/css" />');
				echo '
	<fieldset><legend>Create User</legend>
		<form action="'.Router::getHttpReadyUri('/admin/action/users').'" method="post">
			<div class="row">
				<div class="six columns">
					<label for="create_username">Username:</label>
					<input id="create_username" name="username" type="text" value="" placeholder="Username" class="u-full-width" />
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
				<div class="six columns">
					<label for="create_type">Type:</label>
					<select id="create_type" name="type" class="u-full-width"><option value="admin">Administrator</option><option value="standard" selected="selected">Standard User</option><option value="guest">Guest</option></select>
				</div>
				<div class="six columns">
					<label for="create_enabled">Enabled:</label>
					<select id="create_enabled" name="enabled" class="u-full-width"><option value="enabled" selected="selected">Enabled</option><option value="disabled">Disabled</option></select>
				</div>
			</div>
			<div class="row">
				<div class="twelve columns">
					<label for="create_comment">Comment:</label>
					<textarea id="create_comment" name="comment" placeholder="Comment" class="u-full-width"></textarea>
				</div>
			</div>
			<div class="row">
				<div class="twelve columns">
					<label for="create">Create User:</label>
					<input id="create" name="create" type="submit" value="Create User" class="u-full-width" />
					<input id="create_cancel" name="reset" type="reset" value="Cancel" class="u-full-width" />
				</div>
			</div>
		</form>
	</fieldset>
	<div class="overflow">
		<fieldset><legend>Users</legend>
			<div class="table">
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
					echo '					';

					echo '<form action="'.Router::getHttpReadyUri('/admin/action/users/'.(int)$user_id).'" method="post">';

					echo '<div>';
					echo '<input id="name_'.htmlspecialchars($user_id).'" name="name" type="text" value="'.htmlspecialchars(Users::getName($user_id)).'" placeholder="Name" />';
					echo '<input id="update_name_'.htmlspecialchars($user_id).'" name="update_name" type="submit" value="Change Name" />';
					echo '</div>';

					echo '<div>';
					echo '<input id="password1_'.htmlspecialchars($user_id).'" name="password1" type="password" value="" placeholder="Password" />';
					echo '<input id="password2_'.htmlspecialchars($user_id).'" name="password2" type="password" value="" placeholder="Password (again)" />';
					echo '<input id="update_password_'.htmlspecialchars($user_id).'" name="update_password" type="submit" value="Change Password" />';
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
					echo '<input id="update_type_'.htmlspecialchars($user_id).'" name="update_type" type="submit" value="Change Type" />';
					echo '</div>';

					echo '<div>';
					if (Users::getEnabled($user_id)) {
						echo '<select id="enabled_'.htmlspecialchars($user_id).'" name="enabled"><option value="enabled" selected="selected">Enabled</option><option value="disabled">Disabled</option></select>';
					} else {
						echo '<select id="enabled_'.htmlspecialchars($user_id).'" name="enabled"><option value="enabled">Enabled</option><option value="disabled" selected="selected">Disabled</option></select>';
					}
					echo '<input id="update_enabled_'.htmlspecialchars($user_id).'" name="update_enabled" type="submit" value="Set Enabled" />';
					echo '</div>';

					echo '<div>';
					echo '<textarea id="comment_'.htmlspecialchars($user_id).'" name="comment" placeholder="Comment">'.htmlspecialchars(Users::getComment($user_id)).'</textarea>';
					echo '<input id="update_comment_'.htmlspecialchars($user_id).'" name="update_comment" type="submit" value="Update Comment" />';
					echo '</div>';

					echo '<div>';
					echo '<input id="delete_'.htmlspecialchars($user_id).'" name="delete" type="submit" value="Delete" onclick="return confirm(\'Delete user &quot;\'+document.getElementById(\'name_'.htmlspecialchars($user_id).'\').getAttribute(\'value\')+\'&quot;?\');" />';
					echo '</div>';

					echo '<div>';
					echo '<input id="cancel_'.htmlspecialchars($user_id).'" name="reset" type="reset" value="Cancel" />';
					echo '</div>';

					echo '</form>';

					echo PHP_EOL;
				}
				echo '				</div>
		</fieldset>
	</div>
';
				MainUiTemplate::foot();
				break;



			case 'users_in_groups':
				MainUiTemplate::head('Users in Groups - Administration');

				MainUiTemplate::foot();
				break;



			case 'groups':
				MainUiTemplate::head('Groups - Administration', '<link rel="stylesheet" href="' . Router::getHttpReadyUri('/resource/main/admin.css') . '" type="text/css" />');
				echo '
	<fieldset><legend>Create Group</legend>
		<form action="'.Router::getHttpReadyUri('/admin/action/groups').'" method="post">
			<div class="row">
				<div class="four columns">
					<label for="create_name">Name:</label>
					<input id="create_name" name="name" type="text" value="" placeholder="Name" class="u-full-width" />
				</div>
				<div class="four columns">
					<label for="create_enabled">Enabled:</label>
					<select id="create_enabled" name="enabled" class="u-full-width"><option value="enabled" selected="selected">Enabled</option><option value="disabled">Disabled</option></select>
				</div>
				<div class="four columns">
					<label for="create_comment">Comment:</label>
					<textarea id="create_comment" name="comment" placeholder="Comment" class="u-full-width"></textarea>
				</div>
			</div>
			<div class="row">
				<div class="twelve columns">
					<label for="create">Create Group:</label>
					<input id="create" name="create" type="submit" value="Create Group" class="u-full-width" />
					<input id="create_cancel" name="reset" type="reset" value="Cancel" class="u-full-width" />
				</div>
			</div>
		</form>
	</fieldset>
	<div class="overflow">
		<fieldset><legend>Groups</legend>
			<div class="table">
				<div class="thead">
					<div>Name</div>
					<div>Enabled</div>
					<div>Comment</div>
					<div>Delete</div>
					<div>Cancel</div>
				</div>
';
				foreach (Groups::getAll() as $group_id) {
					echo '					';

					echo '<form action="'.Router::getHttpReadyUri('/admin/action/groups/'.(int)$group_id).'" method="post">';

					echo '<div>';
					echo '<input id="name_'.htmlspecialchars($group_id).'" name="name" type="text" value="'.htmlspecialchars(Groups::getName($group_id)).'" placeholder="Name" />';
					echo '<input id="update_name_'.htmlspecialchars($group_id).'" name="update_name" type="submit" value="Change Name" />';
					echo '</div>';

					echo '<div>';
					if (Groups::getEnabled($group_id)) {
						echo '<select id="enabled_'.htmlspecialchars($group_id).'" name="enabled"><option value="enabled" selected="selected">Enabled</option><option value="disabled">Disabled</option></select>';
					} else {
						echo '<select id="enabled_'.htmlspecialchars($group_id).'" name="enabled"><option value="enabled">Enabled</option><option value="disabled" selected="selected">Disabled</option></select>';
					}
					echo '<input id="update_enabled_'.htmlspecialchars($group_id).'" name="update_enabled" type="submit" value="Set Enabled" />';
					echo '</div>';

					echo '<div>';
					echo '<textarea id="comment_'.htmlspecialchars($group_id).'" name="comment" placeholder="Comment">'.htmlspecialchars(Groups::getComment($group_id)).'</textarea>';
					echo '<input id="update_comment_'.htmlspecialchars($group_id).'" name="update_comment" type="submit" value="Update Comment" />';
					echo '</div>';

					echo '<div>';
					echo '<input id="delete_'.htmlspecialchars($group_id).'" name="delete" type="submit" value="Delete" onclick="return confirm(\'Delete group &quot;\'+document.getElementById(\'name_'.htmlspecialchars($group_id).'\').getAttribute(\'value\')+\'&quot;?\');" />';
					echo '</div>';

					echo '<div>';
					echo '<input id="cancel_'.htmlspecialchars($group_id).'" name="reset" type="reset" value="Cancel" />';
					echo '</div>';

					echo '</form>';

					echo PHP_EOL;
				}
				echo '				</div>
		</fieldset>
	</div>
';
				MainUiTemplate::foot();
				break;



			case 'shares_in_groups':
				MainUiTemplate::head('Shares in Groups - Administration');

				MainUiTemplate::foot();
				break;



			case 'shares':
				MainUiTemplate::head('Shares - Administration', '<link rel="stylesheet" href="' . Router::getHttpReadyUri('/resource/main/admin.css') . '" type="text/css" />');
				echo '
	<fieldset><legend>Create Share</legend>
		<form action="'.Router::getHttpReadyUri('/admin/action/shares').'" method="post">
			<div class="row">
				<div class="three columns">
					<label for="create_name">Name:</label>
					<input id="create_name" name="name" type="text" value="" placeholder="Name" class="u-full-width" />
				</div>
				<div class="three columns">
					<label for="create_path">Path:</label>
					<input id="create_path" name="path" type="text" value="" placeholder="Path" class="u-full-width" />
				</div>
				<div class="three columns">
					<label for="create_enabled">Enabled:</label>
					<select id="create_enabled" name="enabled" class="u-full-width"><option value="enabled" selected="selected">Enabled</option><option value="disabled">Disabled</option></select>
				</div>
				<div class="three columns">
					<label for="create_comment">Comment:</label>
					<textarea id="create_comment" name="comment" placeholder="Comment" class="u-full-width"></textarea>
				</div>
			</div>
			<div class="row">
				<div class="twelve columns">
					<label for="create">Create Share:</label>
					<input id="create" name="create" type="submit" value="Create Share" class="u-full-width" />
					<input id="create_cancel" name="reset" type="reset" value="Cancel" class="u-full-width" />
				</div>
			</div>
		</form>
	</fieldset>
	<div class="overflow">
		<fieldset><legend>Shares</legend>
			<div class="table">
				<div class="thead">
					<div>Name</div>
					<div>Path</div>
					<div>Enabled</div>
					<div>Comment</div>
					<div>Delete</div>
					<div>Cancel</div>
				</div>
';
				foreach (Shares::getAll() as $share_id) {
					echo '					';

					echo '<form action="'.Router::getHttpReadyUri('/admin/action/groups/'.(int)$share_id).'" method="post">';

					echo '<div>';
					echo '<input id="name_'.htmlspecialchars($share_id).'" name="name" type="text" value="'.htmlspecialchars(Shares::getName($share_id)).'" placeholder="Name" />';
					echo '<input id="update_name_'.htmlspecialchars($share_id).'" name="update_name" type="submit" value="Change Name" />';
					echo '</div>';

					echo '<div>';
					echo '<input id="path_'.htmlspecialchars($share_id).'" name="path" type="text" value="'.htmlspecialchars(Shares::getPath($share_id)).'" placeholder="Name" />';
					echo '<input id="update_path_'.htmlspecialchars($share_id).'" name="update_path" type="submit" value="Change Path" />';
					echo '</div>';

					echo '<div>';
					if (Shares::getEnabled($share_id)) {
						echo '<select id="enabled_'.htmlspecialchars($share_id).'" name="enabled"><option value="enabled" selected="selected">Enabled</option><option value="disabled">Disabled</option></select>';
					} else {
						echo '<select id="enabled_'.htmlspecialchars($share_id).'" name="enabled"><option value="enabled">Enabled</option><option value="disabled" selected="selected">Disabled</option></select>';
					}
					echo '<input id="update_enabled_'.htmlspecialchars($share_id).'" name="update_enabled" type="submit" value="Set Enabled" />';
					echo '</div>';

					echo '<div>';
					echo '<textarea id="comment_'.htmlspecialchars($share_id).'" name="comment" placeholder="Comment">'.htmlspecialchars(Shares::getComment($share_id)).'</textarea>';
					echo '<input id="update_comment_'.htmlspecialchars($share_id).'" name="update_comment" type="submit" value="Update Comment" />';
					echo '</div>';

					echo '<div>';
					echo '<input id="delete_'.htmlspecialchars($share_id).'" name="delete" type="submit" value="Delete" onclick="return confirm(\'Delete share &quot;\'+document.getElementById(\'name_'.htmlspecialchars($share_id).'\').getAttribute(\'value\')+\'&quot;?\');" />';
					echo '</div>';

					echo '<div>';
					echo '<input id="cancel_'.htmlspecialchars($share_id).'" name="reset" type="reset" value="Cancel" />';
					echo '</div>';

					echo '</form>';

					echo PHP_EOL;
				}
				echo '				</div>
		</fieldset>
	</div>
';
				MainUiTemplate::foot();
				break;



			case 'action':
				if (isset($subpage_array[1])) {
					switch ($subpage_array[1]) {
						case 'users':

							break;


						case 'users_in_groups':

							break;


						case 'groups':

							break;


						case 'shares_in_groups':

							break;


						case 'shares':

							break;

						
						default:
							Router::execErrorPage(404);
							break;
					}
				} else {
					Router::execErrorPage(404);
				}
				break;


			default:
				Router::execErrorPage(404);
				break;
		}
	}
});

Resources::register('main/admin.css', function() {
	Resources::serveFile('_plugins/main/resources/admin.css');
});
