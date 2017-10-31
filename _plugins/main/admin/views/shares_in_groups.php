<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

function mainUiAdminSharesInGroups()
{
	MainUiTemplate::header('Shares in Groups - Administration', '<link rel="stylesheet" href="' . Router::getHtmlReadyUri('/resource/main/admin.css') . '" type="text/css" />');
	echo '		<div class="overflow">
			<fieldset>
				<legend>Shares in Groups</legend>
				<div class="table">
';

	echo '					<div><div></div>';

	Database::lock();
	$groups = Groups::getAll();
	$shares = Shares::getAll();
	foreach ($groups as $group_id) {
		echo '<div class="th">';
		echo htmlspecialchars(Groups::getName($group_id));
		echo '</div>';
	}
	echo '</div>'.PHP_EOL;

	foreach ($shares as $share_id) {
		echo '					<div>';
		echo '<div class="th">';
		echo htmlspecialchars(Shares::getName($share_id));
		echo '</div>';

		foreach ($groups as $group_id) {
			echo '<div>';
			echo '<form action="'.Router::getHtmlReadyUri('/admin/action/shares_in_groups', ['share'=>$share_id, 'group'=>$group_id]).'" method="post">';
				echo '<select name="share_in_group">';
				if (Groups::shareInGroup($group_id, $share_id)) {
					if (Groups::getShareWritable($group_id, $share_id)) {
						echo '<option value="none">No Access</option><option value="read-only">Read-Only</option><option value="read-write" selected="selected">Read-Write</option>';
					} else {
						echo '<option value="none">No Access</option><option value="read-only" selected="selected">Read-Only</option><option value="read-write">Read-Write</option>';
					}
				} else {
					echo '<option value="none" selected="selected">No Access</option><option value="read-only">Read-Only</option><option value="read-write">Read-Write</option>';
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
