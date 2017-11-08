<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

MainUiTemplate::header('Shares in Groups - Administration', '<link rel="stylesheet" href="' . Router::getHtmlReadyUri('/resource/main/admin.css') . '" type="text/css" />');

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

echo '		<div class="overflow">
			<fieldset>
				<legend>Shares in Groups</legend>
				<div class="table">
';

echo '					<div><div><pre style="margin: 0px;"><code style="margin: 0px;"><strong>       Groups &gt;<br />Shares<br />  v</strong></code></pre></div>';

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
		echo '<form action="'.Router::getHtmlReadyUri('/admin/action/shares_in_groups', ['share' => $share_id, 'group' => $group_id]).'" method="post">';
			echo '<input name="csrf_token" type="hidden" value="'.htmlspecialchars(Session::get('_csrf_token')).'" />';
			if (Groups::shareInGroup($group_id, $share_id)) {
				if (Groups::getShareWritable($group_id, $share_id)) {
					echo '<input name="deny" type="submit" value="Allowed" title="Click to deny" style="background-color: #6f6; color: #000; width: 15em; display: block;" />';
					echo '<input name="read-only" type="submit" value="Read-Write" title="Click to set to read-only" style="background-color: #6f6; color: #000; width: 15em; display: block;" />';
				} else {
					echo '<input name="deny" type="submit" value="Allowed" title="Click to deny" style="background-color: #6f6; color: #000; width: 15em; display: block;" />';
					echo '<input name="read-write" type="submit" value="Read-Only" title="Click to set to read-write" style="background-color: #ff6; color: #000; width: 15em; display: block;" />';
				}
			} else {
				echo '<input name="allow" type="submit" value="Denied" title="Click to allow" style="background-color: #f66; color: #000; width: 15em; display: block;" />';
				echo '<input name="noaccess" type="submit" value="&lt;No Access&gt;" disabled="disabled" style="background-color: #ccc; width: 15em; display: block; pointer-events: none;" />';
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
