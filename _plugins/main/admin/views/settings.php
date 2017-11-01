<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

MainUiTemplate::header('Global Settings - Administration', '<link rel="stylesheet" href="' . Router::getHtmlReadyUri('/resource/main/admin.css') . '" type="text/css" />');
echo '		<form action="'.Router::getHtmlReadyUri('/admin/action/settings').'" method="post">
			<input name="csrf_token" type="hidden" value="'.htmlspecialchars(Session::get('_csrf_token')).'" />
			<fieldset>
				<legend>Set</legend>
				<div class="row">
					<div class="four columns">
						<input name="set_key" type="text" value="" placeholder="Key" class="u-full-width" />
					</div>
					<div class="six columns">
						<input name="set_value" type="text" value="" placeholder="Value" class="u-full-width" />
					</div>
					<div class="two columns">
						<input name="set" type="submit" value="Set" class="u-full-width button-primary" />
					</div>
				</div>
			</fieldset>
			<fieldset>
				<legend>Unset</legend>
				<div class="row">
					<div class="ten columns">
						<input name="unset_key" type="text" value="" placeholder="Key" class="u-full-width" />
					</div>
					<div class="two columns">
						<input name="unset" type="submit" value="Unset" class="u-full-width button-primary" />
					</div>
				</div>
			</fieldset>
			<div class="overflow">
				<fieldset>
					<legend>Settings</legend>				
					<table class="u-full-width">
						<thead>
							<tr>
								<th>Key</th>
								<th>Value</th>
							</tr>
						</thead>
						<tbody>
							';
Database::lock();
foreach (GlobalSettings::getAll() as $key) {
	echo '<tr>';
	echo '<td>'.htmlspecialchars($key).'</td>';
	echo '<td>'.htmlspecialchars(GlobalSettings::get($key)).'</td>';
	echo '</tr>';
}
Database::unlock();
echo'
						</tbody>
					</table>
				</fieldset>
			</div>
		</form>';
MainUiTemplate::footer();
