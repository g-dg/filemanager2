<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

Router::registerPage('settings', function($subpage) {
	Auth::authenticate();
	switch ($subpage) {
		case '':
			MainUiTemplate::header('Settings');
			echo '		
		<fieldset>
			<legend>Account Settings</legend>
			<a href="'.Router::getHtmlReadyUri('/account').'">My Account</a>
		</fieldset>
		<fieldset>
			<legend>Settings</legend>

			<form action="'.Router::getHtmlReadyUri('/settings/action').'" method="post">
				<input name="csrf_token" type="hidden" value="'.htmlspecialchars(Session::get('_csrf_token')).'" />
				<input name="set_key" type="hidden" value="_main.browse.show_hidden" />
				<div class="row">
					<div class="one column">
						<p>Hidden file behaviour:</p>
					</div>
					<div class="nine columns">
						';
						if (UserSettings::get('_main.browse.show_hidden', 'false') === 'true') {
							echo '<select name="set_value" class="u-full-width"><option value="true" selected="selected">Show hidden files</option><option value="false">Hide hidden files</option></select>';
						} else {
							echo '<select name="set_value" class="u-full-width"><option value="true">Show hidden files</option><option value="false" selected="selected">Hide hidden files</option></select>';
						}
						echo '
					</div>
					<div class="two columns">
						<input name="set" type="submit" value="Save" class="u-full-width button-primary" />
					</div>
				</div>
			</form>

			<form action="'.Router::getHtmlReadyUri('/settings/action').'" method="post">
				<input name="csrf_token" type="hidden" value="'.htmlspecialchars(Session::get('_csrf_token')).'" />
				<input name="set_key" type="hidden" value="_main.browse.sort_field" />
				<div class="row">
					<div class="one column">
						<p>Sort field:</p>
					</div>
					<div class="nine columns">
						';
						switch (UserSettings::get('_main.browse.sort_field', 'name')) {
							case 'last-modified':
								echo '<select name="set_value" class="u-full-width"><option value="name">Name</option><option value="last-modified" selected="selected">Last Modified</option><option value="size">Size</option></select>';
								break;
							case 'size':
								echo '<select name="set_value" class="u-full-width"><option value="name">Name</option><option value="last-modified">Last Modified</option><option value="size" selected="selected">Size</option></select>';
								break;
							default:
								echo '<select name="set_value" class="u-full-width"><option value="name" selected="selected">Name</option><option value="last-modified">Last Modified</option><option value="size">Size</option></select>';
								break;
						}
						echo '
					</div>
					<div class="two columns">
						<input name="set" type="submit" value="Save" class="u-full-width button-primary" onclick="alert(\'You will have to log out and log back in for changes to take effect.\');" />
					</div>
				</div>
			</form>

			<form action="'.Router::getHtmlReadyUri('/settings/action').'" method="post">
				<input name="csrf_token" type="hidden" value="'.htmlspecialchars(Session::get('_csrf_token')).'" />
				<input name="set_key" type="hidden" value="_main.browse.sort_order" />
				<div class="row">
					<div class="one column">
						<p>Sort order:</p>
					</div>
					<div class="nine columns">
						';
						switch (UserSettings::get('_main.browse.sort_order', 'desc')) {
							case 'asc':
								echo '<select name="set_value" class="u-full-width"><option value="asc" selected="selected">Ascending</option><option value="desc">Descending</option></select>';
								break;
							default:
								echo '<select name="set_value" class="u-full-width"><option value="asc">Ascending</option><option value="desc" selected="selected">Descending</option></select>';
								break;
						}
						echo '
					</div>
					<div class="two columns">
						<input name="set" type="submit" value="Save" class="u-full-width button-primary" onclick="alert(\'You will have to log out and log back in for changes to take effect.\');" />
					</div>
				</div>
			</form>

			';
			Hooks::exec('_main.settings.easysetting');
			echo '

		</fieldset>
		<input type="button" value="Advanced" onclick="$(\'#advanced\').toggle();" style="margin-top: 1em; margin-bottom: 1em;" />
		<fieldset id="advanced" style="display: none;">
			<legend>Advanced Settings</legend>
			<form action="'.Router::getHtmlReadyUri('/settings/action').'" method="post">
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
						<table class="border" style="max-width: 100%;">
							<thead>
								<tr>
									<th>Key</th>
									<th>Value</th>
								</tr>
							</thead>
							<tbody>
								';
			Database::lock();
			foreach (UserSettings::getAll() as $key) {
				if (strlen($key) > 1 && substr($key, 0, 2) !== '__') {
					echo '<tr>';
					echo '<td>'.str_replace(' ', '&nbsp;', htmlspecialchars($key)).'</td>';
					echo '<td>'.str_replace(' ', '&nbsp;', htmlspecialchars(UserSettings::get($key))).'</td>';
					echo '</tr>';
				}
			}
			Database::unlock();
			echo'
							</tbody>
						</table>
					</fieldset>
				</div>
			</form>
		</fieldset>';
			MainUiTemplate::footer();
			break;
		

		case 'action':
			if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === Session::get('_csrf_token')) {
				if (
					isset($_POST['set'], $_POST['set_key'], $_POST['set_value'])
				) {
					UserSettings::set($_POST['set_key'], $_POST['set_value']);
				} else if (
					isset($_POST['unset'], $_POST['unset_key'])
				) {
					UserSettings::unset($_POST['unset_key']);
				}
				Router::redirect('/settings');
			} else {
				Router::execErrorPage(403);
			}
			break;
		

		default:
			Router::execErrorPage(404);
			break;
	}
});
