<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

MainUiTemplate::header('Global Settings - Administration', '<link rel="stylesheet" href="' . Router::getHtmlReadyUri('/resource/main/css/admin.css') . '" type="text/css" />');
echo '		<form action="'.Router::getHtmlReadyUri('/admin/action/settings').'" method="post">
			<input name="csrf_token" type="hidden" value="'.htmlspecialchars(Session::get('_csrf_token')).'" />
			<fieldset>
				<legend>Set</legend>
				<div class="form-inputs">
					<label for="set_key">Key:</label>
					<input id="set_key" name="set_key" type="text" value="" class="u-full-width" />

					<label for="set_value">Value:</label>
					<input id="set_value" name="set_value" type="text" value="" class="u-full-width" />
				</div>

				<div class="form-buttons">
					<input name="set" type="submit" value="Set" class="u-full-width button-primary" />
				</div>
			</fieldset>
			<fieldset>
				<legend>Unset</legend>
				<div class="form-inputs">
					<label for="unset_key">Key:</label>
					<input id="unset_key" name="unset_key" type="text" value="" class="u-full-width" />
				</div>

				<div class="form-buttons">
					<input name="unset" type="submit" value="Unset" class="u-full-width button-primary" />
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
	echo '<td>'.str_replace(' ', '&nbsp;', htmlspecialchars($key)).'</td>';
	echo '<td>'.str_replace(' ', '&nbsp;', htmlspecialchars(GlobalSettings::get($key))).'</td>';
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
