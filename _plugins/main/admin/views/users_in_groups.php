<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

function mainUiAdminUsersInGroups()
{
	MainUiTemplate::header('Users in Groups - Administration', '<link rel="stylesheet" href="' . Router::getHtmlReadyUri('/resource/main/admin.css') . '" type="text/css" />');
	echo '		<div class="overflow">
			<fieldset>
				<legend>Users in Groups</legend>
				<div class="table">
';

	echo '					<div><div></div>';

	Database::lock();
	$groups = Groups::getAll();
	$users = Users::getAll();
	foreach ($groups as $group_id) {
		echo '<div class="th">';
		echo htmlspecialchars(Groups::getName($group_id));
		echo '</div>';
	}
	echo '</div>'.PHP_EOL;

	foreach ($users as $user_id) {
		echo '					<div>';
		echo '<div class="th">';
		echo htmlspecialchars(Users::getName($user_id));
		echo '</div>';

		foreach ($groups as $group_id) {
			echo '<div>';
			echo '<form action="'.Router::getHtmlReadyUri('/admin/action/users_in_groups', ['user'=>$user_id, 'group'=>$group_id]).'" method="post">';
				echo '<select name="user_in_group">';
				if (Groups::userInGroup($group_id, $user_id)) {
					echo '<option value="true" selected="selected">Yes</option><option value="false">No</option>';
				} else {
					echo '<option value="true">Yes</option><option value="false" selected="selected">No</option>';
				}
				echo '</select>';
				echo '<input name="update" type="submit" value="Update" />';
			echo '</form>';
			echo '</div>';
		}
		echo '</div>'.PHP_EOL;
	}
	Database::unlock();

	echo '				</div>
			</fieldset>
		</div>';
	MainUiTemplate::footer();
}
