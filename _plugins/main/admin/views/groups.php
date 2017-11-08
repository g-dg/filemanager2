<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

MainUiTemplate::header('Groups - Administration', '<link rel="stylesheet" href="' . Router::getHtmlReadyUri('/resource/main/admin.css') . '" type="text/css" />');

Session::lock();
echo '<div class="message">';
if (Session::isset('_main_admin_status')) {
	if (Session::get('_main_admin_status')) {
		echo 'The last action completed successfully.';
	} else {
		echo 'A problem occurred during the last action.';
	}
	Session::unset('_main_admin_status');
}
echo '</div>';
Session::unlock();

echo '
	<fieldset><legend>Create Group</legend>
		<form action="'.Router::getHtmlReadyUri('/admin/action/groups').'" method="post">
			<div class="row">
				<div class="four columns">
					<label for="create_name">Name:</label>
					<input id="create_name" name="name" type="text" value="" placeholder="Name" class="u-full-width" />
				</div>
				<div class="three columns">
					<label for="create_enabled">Enabled:</label>
					<select id="create_enabled" name="enabled" class="u-full-width"><option value="enabled" selected="selected">Enabled</option><option value="disabled">Disabled</option></select>
				</div>
				<div class="five columns">
					<label for="create_comment">Comment:</label>
					<textarea id="create_comment" name="comment" placeholder="Comment" class="u-full-width"></textarea>
				</div>
			</div>
			<div class="row">
				<div class="twelve columns">
					<input name="csrf_token" type="hidden" value="'.htmlspecialchars(Session::get('_csrf_token')).'" />
					<label for="create">Create Group:</label>
					<input id="create" name="create" type="submit" value="Create Group" class="u-full-width button-primary" />
					<input id="create_cancel" name="reset" type="reset" value="Cancel" class="u-full-width" formnovalidate="formnovalidate" />
				</div>
			</div>
		</form>
	</fieldset>
	<div class="overflow">
		<fieldset>
			<legend>Groups</legend>
';
Database::lock();
$groups = Groups::getAll();
if (count($groups) > 0) {
	echo '			<div class="table">
				<div class="thead">
					<div>Name</div>
					<div>Enabled</div>
					<div>Comment</div>
					<div>Delete</div>
					<div>Cancel</div>
				</div>
';
	foreach (Groups::getAll() as $group_id) {
		echo '				';

		echo '<form action="'.Router::getHtmlReadyUri('/admin/action/groups', ['group' => $group_id]).'" method="post">';

		echo '<div>';
		echo '<input id="name_'.htmlspecialchars($group_id).'" name="name" type="text" value="'.htmlspecialchars(Groups::getName($group_id)).'" placeholder="Name" required="required" />';
		echo '<input id="update_name_'.htmlspecialchars($group_id).'" name="update_name" type="submit" value="Save" />';
		echo '</div>';

		echo '<div>';
		if (Groups::getEnabled($group_id)) {
			echo '<select id="enabled_'.htmlspecialchars($group_id).'" name="enabled"><option value="enabled" selected="selected">Enabled</option><option value="disabled">Disabled</option></select>';
		} else {
			echo '<select id="enabled_'.htmlspecialchars($group_id).'" name="enabled"><option value="enabled">Enabled</option><option value="disabled" selected="selected">Disabled</option></select>';
		}
		echo '<input id="update_enabled_'.htmlspecialchars($group_id).'" name="update_enabled" type="submit" value="Save" />';
		echo '</div>';

		echo '<div>';
		echo '<textarea id="comment_'.htmlspecialchars($group_id).'" name="comment" placeholder="Comment">'.htmlspecialchars(Groups::getComment($group_id)).'</textarea>';
		echo '<input id="update_comment_'.htmlspecialchars($group_id).'" name="update_comment" type="submit" value="Save" />';
		echo '</div>';

		echo '<div>';
		echo '<input id="delete_'.htmlspecialchars($group_id).'" name="delete" type="submit" value="Delete" onclick="return confirm(\'Delete group &quot;\'+document.getElementById(\'name_'.htmlspecialchars($group_id).'\').getAttribute(\'value\')+\'&quot;?\');" formnovalidate="formnovalidate" style="color: #c00;" />';
		echo '</div>';

		echo '<div>';
		echo '<input id="cancel_'.htmlspecialchars($group_id).'" name="reset" type="reset" value="Cancel" formnovalidate="formnovalidate" />';
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
