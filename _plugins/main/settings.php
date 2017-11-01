<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

Router::registerPage('settings', function($subpage) {
	Auth::authenticate();
	if (Auth::getCurrentUserType() !== Auth::USER_TYPE_GUEST) {
		switch ($subpage) {
			case '':
				MainUiTemplate::header('Settings');
				echo '		<form action="'.Router::getHtmlReadyUri('/settings/action').'" method="post">
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
				foreach (UserSettings::getAll() as $key) {
					if (strlen($key) > 1 && substr($key, 0, 2) !== '__') {
						echo '<tr>';
						echo '<td>'.htmlspecialchars($key).'</td>';
						echo '<td>'.htmlspecialchars(UserSettings::get($key)).'</td>';
						echo '</tr>';
					}
				}
				Database::unlock();
				echo'
						</tbody>
					</table>
				</fieldset>
			</div>
		</form>';
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
	} else {
		Router::execErrorPage(403);
	}
});
