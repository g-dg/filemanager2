<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

MainUiTemplate::header('Users in Groups - Administration', '<link rel="stylesheet" href="' . Router::getHtmlReadyUri('/resource/main/admin.css') . '" type="text/css" />');

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

echo '		<div class="overflow">
			<fieldset>
				<legend>Users in Groups</legend>
				<div class="table">
';

echo '					<div><div><pre style="margin: 0px;"><code style="margin: 0px;"><strong>      Groups &gt;<br />Users<br />  v</strong></code></pre></div>';

Database::lock();
$groups = Groups::getAll();
$users = Users::getAll();
foreach ($groups as $group_id) {
	echo '<div class="th">';
	echo str_replace(' ', '&nbsp;', htmlspecialchars(Groups::getName($group_id)));
	echo '</div>';
}
echo '</div>'.PHP_EOL;

foreach ($users as $user_id) {
	echo '					<div>';
	echo '<div class="th">';
	echo str_replace(' ', '&nbsp;', htmlspecialchars(Users::getName($user_id)));
	echo '</div>';

	foreach ($groups as $group_id) {
		echo '<div>';
		echo '<form action="'.Router::getHtmlReadyUri('/admin/action/users_in_groups', ['user' => $user_id, 'group' => $group_id]).'" method="post">';
			echo '<input name="csrf_token" type="hidden" value="'.htmlspecialchars(Session::get('_csrf_token')).'" />';
			if (Groups::userInGroup($group_id, $user_id)) {
				echo '<input name="remove" type="submit" value="Allowed" title="Click to deny" style="background-color: #6f6; color: #000; width: 12.5em;" />';
			} else {
				echo '<input name="add" type="submit" value="Denied" title="Click to allow" style="background-color: #f66; color: #000;  width: 12.5em;" />';
			}
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
