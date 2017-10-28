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
	switch ($subpage) {
		case '':
			MainUiTemplate::header('Administration');
			echo '<a href="' . htmlspecialchars(Router::getHttpReadyUri('/')) . '">&lt; Back</a>
<ul>
	<li><a href="' . htmlspecialchars(Router::getHttpReadyUri('/admin/users')) . '">Users</a></li>
	<li><a href="' . htmlspecialchars(Router::getHttpReadyUri('/admin/users_in_groups')) . '">Users in Groups</a></li>
	<li><a href="' . htmlspecialchars(Router::getHttpReadyUri('/admin/groups')) . '">Groups</a></li>
	<li><a href="' . htmlspecialchars(Router::getHttpReadyUri('/admin/shares_in_groups')) . '">Shares in Groups</a></li>
	<li><a href="' . htmlspecialchars(Router::getHttpReadyUri('/admin/shares')) . '">Shares</a></li>
</ul>';
			MainUiTemplate::footer();
			break;


		case 'users':
			MainUiTemplate::header('Users - Administration');
			echo '<p><a href="' . htmlspecialchars(Router::getHttpReadyUri('/admin')) . '">&lt; Back</a></p>';

			Session::lock();
			if (Session::isset('_admin_status')) {
				if (Session::get('_admin_status')) {
					echo '<p><em><strong>The last action completed successfully</strong></em></p>';
				} else {
					echo '<p><em><strong>A problem occurred during the last action</strong></em></p>';
				}
				Session::unset('_admin_status');
			}
			Session::unlock();

			echo '<fieldset><legend>Create</legend>';
			echo '<form action="' . Router::getHttpReadyUri('/admin/users/create') . '" method="post">';
			echo '<input name="username" type="text" value="" placeholder="Username" required="required" />';
			echo '<input name="password" type="password" value="" placeholder="Password" />';
			echo '<select name="enabled"><option value="enabled" selected="selected">Enabled</option><option value="disabled">Disabled</option></select>';
			echo '<select name="type"><option value="guest">Guest</option><option value="standard" selected="selected">Standard</option><option value="admin">Administrator</option></select>';
			echo '<textarea name="comment" placeholder="Comment"></textarea>';
			echo '<input name="csrf_token" type="hidden" value="' . htmlspecialchars(Session::get('_csrf_token')) . '" />';
			echo '<input name="submit" type="submit" value="Create" />';
			echo '</form>';
			echo '</fieldset>';

			echo '<fieldset><legend>Update</legend>';
			echo '<form action="' . Router::getHttpReadyUri('/admin/users/update') . '" method="post">';
			echo '<em>Blank fields will not be changed.</em>';
			echo '<input name="id" type="number" placeholder="User ID" />';
			echo '<input name="username" type="text" value="" placeholder="Username" />';
			echo '<input name="password" type="password" value="" placeholder="Password" />';
			echo '<select name="enabled"><option value="" selected="selected"></option><option value="enabled">Enabled</option><option value="disabled">Disabled</option></select>';
			echo '<select name="type"><option value="" selected="selected"></option><option value="guest">Guest</option><option value="standard">Standard</option><option value="admin">Administrator</option></select>';
			echo '<textarea name="comment" placeholder="Comment"></textarea>';
			echo '<input name="csrf_token" type="hidden" value="' . htmlspecialchars(Session::get('_csrf_token')) . '" />';
			echo '<input name="submit" type="submit" value="Update" />';
			echo '</form>';
			echo '</fieldset>';

			echo '<fieldset><legend>Delete</legend>';
			echo '<form action="' . Router::getHttpReadyUri('/admin/users/delete') . '" method="post">';
			echo '<input name="id" type="number" placeholder="User ID" />';
			echo '<input name="csrf_token" type="hidden" value="' . htmlspecialchars(Session::get('_csrf_token')) . '" />';
			echo '<input name="submit" type="submit" value="Delete" />';
			echo '</form>';
			echo '</fieldset>';

			echo '<fieldset><legend>Users</legend>';
			echo '<table style="width: 100%;" border="1"><thead><tr><th>ID</th><th>Name</th><th>Enabled</th><th>Type</th><th>Comment</th></tr></thead><tbody>';
			foreach (Users::getAll() as $user_id) {
				echo '<tr>';
				echo '<td>' . htmlspecialchars($user_id) . '</td>';
				echo '<td>' . htmlspecialchars(Users::getName($user_id)) . '</td>';
				if (Users::getEnabled($user_id)) {
					echo '<td>True</td>';
				} else {
					echo '<td>False</td>';
				}
				switch (Users::getType($user_id)) {
					case Users::USER_TYPE_ADMIN:
						echo '<td>Administrator</td>';
						break;
					case Users::USER_TYPE_STANDARD:
						echo '<td>Standard</td>';
						break;
					case Users::USER_TYPE_GUEST:
						echo '<td>Guest</td>';
						break;
					default:
						echo '<td><em>Unknown</em></td>';
				}
				echo '<td>' . htmlspecialchars(Users::getComment($user_id)) . '</td>';
				echo '</tr>';
			}
			echo '</tbody></table>';
			echo '</fieldset>';
			MainUiTemplate::footer();
			break;


		case 'users/create':
			$result = false;
			if (isset($_POST['username'], $_POST['password'], $_POST['enabled'], $_POST['type'], $_POST['comment'], $_POST['csrf_token'])) {
				if ($_POST['csrf_token'] === Session::get('_csrf_token')) {
					$type = Users::USER_TYPE_STANDARD;
					$enabled = true;
					switch ($_POST['type']) {
						case 'guest':
							$type = Users::USER_TYPE_GUEST;
							break;
						case 'standard':
							$type = Users::USER_TYPE_STANDARD;
							break;
						case 'admin':
							$type = Users::USER_TYPE_ADMIN;
							break;
					}
					if ($_POST['enabled'] == 'disabled') {
						$enabled = false;
					}
					$result = Users::create($_POST['username'], $_POST['password'], $type, $enabled, $_POST['comment']);
				}
			}
			Session::set('_admin_status', $result);
			Router::redirect('/admin/users');
			break;


		case 'users/update':
			$result = false;
			if (isset($_POST['id'], $_POST['username'], $_POST['password'], $_POST['enabled'], $_POST['type'], $_POST['comment'], $_POST['csrf_token'])) {
				if ($_POST['csrf_token'] === Session::get('_csrf_token')) {
					
				}
			}
			Session::set('_admin_status', $result);
			Router::redirect('/admin/users');
			break;


		case 'users/delete':
			$result = false;
			if (isset($_POST['id'], $_POST['csrf_token'])) {
				if ($_POST['csrf_token'] === Session::get('_csrf_token')) {
					$result = Users::delete($_POST['id']);
				}
			}
			Session::set('_admin_status', $result);
			Router::redirect('/admin/users');
			break;


		case 'users_in_groups':
			MainUiTemplate::header('Users in Groups - Administration');
			echo '<p><a href="' . htmlspecialchars(Router::getHttpReadyUri('/admin')) . '">&lt; Back</a></p>';
			
			MainUiTemplate::footer();
			break;


		case 'groups':
			MainUiTemplate::header('Groups - Administration');
			echo '<p><a href="' . htmlspecialchars(Router::getHttpReadyUri('/admin')) . '">&lt; Back</a></p>';

			Session::lock();
			if (Session::isset('_admin_status')) {
				if (Session::get('_admin_status')) {
					echo '<p><em><strong>The last action completed successfully</strong></em></p>';
				} else {
					echo '<p><em><strong>A problem occurred during the last action</strong></em></p>';
				}
				Session::unset('_admin_status');
			}
			Session::unlock();

			echo '<fieldset><legend>Create</legend>';
			echo '<form action="' . Router::getHttpReadyUri('/admin/groups/create') . '" method="post">';

			echo '</form>';
			echo '</fieldset>';

			echo '<fieldset><legend>Update</legend>';
			echo '<form action="' . Router::getHttpReadyUri('/admin/groups/update') . '" method="post">';
			
			echo '</form>';
			echo '</fieldset>';

			echo '<fieldset><legend>Delete</legend>';
			echo '<form action="' . Router::getHttpReadyUri('/admin/groups/delete') . '" method="post">';
			
			echo '</form>';
			echo '</fieldset>';

			echo '<fieldset><legend>Groups</legend>';
			echo '<table style="width: 100%" border="1"><thead><tr><th>ID</th><th>Name</th><th>Enabled</th><th>Comment</th></thead><tbody>';
			foreach (Groups::getAll() as $user_id) {
				echo '<tr>';
				echo '<td>' . htmlspecialchars($user_id) . '</td>';
				echo '<td>' . htmlspecialchars(Groups::getName($user_id)) . '</td>';
				if (Groups::getEnabled($user_id)) {
					echo '<td>True</td>';
				} else {
					echo '<td>False</td>';
				}
				echo '<td>' . htmlspecialchars(Groups::getComment($user_id)) . '</td>';
				echo '</tr>';
			}
			echo '</tbody></table>';
			echo '</fieldset>';
			
			MainUiTemplate::footer();
			break;


		case 'groups/create':
			$result = false;
			
			Session::set('_admin_status', $result);
			Router::redirect('/admin/groups');
			break;


		case 'groups/update':
			$result = false;
			
			Session::set('_admin_status', $result);
			Router::redirect('/admin/groups');
			break;


		case 'groups/delete':
			$result = false;
			
			Session::set('_admin_status', $result);
			Router::redirect('/admin/groups');
			break;


		case 'shares_in_groups':
			MainUiTemplate::header('Shares in Groups - Administration');
			echo '<p><a href="' . htmlspecialchars(Router::getHttpReadyUri('/admin')) . '">&lt; Back</a></p>';
			
			MainUiTemplate::footer();
			break;


		case 'shares':
			MainUiTemplate::header('Shares - Administration');
			echo '<p><a href="' . htmlspecialchars(Router::getHttpReadyUri('/admin')) . '">&lt; Back</a></p>';

			Session::lock();
			if (Session::isset('_admin_status')) {
				if (Session::get('_admin_status')) {
					echo '<p><em><strong>The last action completed successfully</strong></em></p>';
				} else {
					echo '<p><em><strong>A problem occurred during the last action</strong></em></p>';
				}
				Session::unset('_admin_status');
			}
			Session::unlock();

			echo '<fieldset><legend>Create</legend>';
			echo '<form action="' . Router::getHttpReadyUri('/admin/shares/create') . '" method="post">';

			echo '</form>';
			echo '</fieldset>';
			echo '<fieldset><legend>Update</legend>';
			echo '<form action="' . Router::getHttpReadyUri('/admin/shares/update') . '" method="post">';
			
			echo '</form>';
			echo '</fieldset>';
			echo '<fieldset><legend>Delete</legend>';
			echo '<form action="' . Router::getHttpReadyUri('/admin/shares/delete') . '" method="post">';
			
			echo '</form>';
			echo '</fieldset>';

			echo '<fieldset><legend>Shares</legend><ul>';
			foreach (Shares::getAll() as $user_id) {
				echo '<li>' . htmlspecialchars(Shares::getName($user_id)) . '</li>';
			}
			echo '</ul></fieldset>';
			
			MainUiTemplate::footer();
			break;


		case 'shares/create':
			$result = false;
			
			Session::set('_admin_status', $result);
			Router::redirect('/admin/shares');
			break;


		case 'shares/update':
			$result = false;
			
			Session::set('_admin_status', $result);
			Router::redirect('/admin/shares');
			break;


		case 'shares/delete':
			$result = false;
			
			Session::set('_admin_status', $result);
			Router::redirect('/admin/shares');
			break;


		default:
			Router::execErrorPage(404);
			break;
	}
});
