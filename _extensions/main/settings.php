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
				<label>Hidden file behaviour:</label>
				';
				if (UserSettings::get('_main.browse.show_hidden', 'false') === 'true') {
					echo '<select name="set_value"><option value="true" selected="selected">Show hidden files</option><option value="false">Hide hidden files</option></select>';
				} else {
					echo '<select name="set_value"><option value="true">Show hidden files</option><option value="false" selected="selected">Hide hidden files</option></select>';
				}
				echo '
				<input name="set" type="submit" value="Save" />
			</form>

			<form action="'.Router::getHtmlReadyUri('/settings/action').'" method="post">
				<input name="csrf_token" type="hidden" value="'.htmlspecialchars(Session::get('_csrf_token')).'" />
				<input name="set_key" type="hidden" value="_main.browse.sort_field" />
				<label>Sort field:</label>
				';
				switch (UserSettings::get('_main.browse.sort_field', 'name')) {
					case 'last-modified':
						echo '<select name="set_value"><option value="name">Name</option><option value="last-modified" selected="selected">Last Modified</option><option value="size">Size</option></select>';
						break;
					case 'size':
						echo '<select name="set_value"><option value="name">Name</option><option value="last-modified">Last Modified</option><option value="size" selected="selected">Size</option></select>';
						break;
					default:
						echo '<select name="set_value"><option value="name" selected="selected">Name</option><option value="last-modified">Last Modified</option><option value="size">Size</option></select>';
						break;
				}
				echo '
				<input name="set" type="submit" value="Save" onclick="alert(\'You will have to log out and log back in for changes to take effect.\');" />
			</form>

			<form action="'.Router::getHtmlReadyUri('/settings/action').'" method="post">
				<input name="csrf_token" type="hidden" value="'.htmlspecialchars(Session::get('_csrf_token')).'" />
				<input name="set_key" type="hidden" value="_main.browse.sort_order" />
				<label>Sort order:</label>
				';
				switch (UserSettings::get('_main.browse.sort_order', 'desc')) {
					case 'asc':
						echo '<select name="set_value"><option value="asc" selected="selected">Ascending</option><option value="desc">Descending</option></select>';
						break;
					default:
						echo '<select name="set_value"><option value="asc">Ascending</option><option value="desc" selected="selected">Descending</option></select>';
						break;
				}
				echo '
				<input name="set" type="submit" value="Save" onclick="alert(\'You will have to log out and log back in for changes to take effect.\');" />
			</form>

			';
			Hooks::exec('_main.settings.easysetting');
			echo '

		</fieldset>
';
?>

<input id="api_uri" name="api_uri" value="<?=Router::getHtmlReadyUri('/settings/api')?>" type="hidden" />
<input id="csrf_token" type="hidden" name="csrf_token" value="<?=htmlspecialchars(Session::get('_csrf_token'))?>" />

<button id="show_advanced" style="margin-top: 1em; margin-bottom: 1em;">Advanced</button>
<div class="overflow">
	<fieldset id="advanced" style="display: none;">
		<legend>Advanced Settings</legend>
		<button id="create" style="margin-bottom: 1em;">Create</button>
		<div id="settings_list"><em>Retrieving settings list, please wait...</em></div>
	</fieldset>
</div>
<script>

	var edit_button_to_settings_keys_map = [];
	var delete_button_to_settings_keys_map = [];

	function updateSettings() {
		$.post(
			$("#api_uri").val(),
			{
				"csrf_token": $("#csrf_token").val(),
				"action": "get_all"
			},
			function (settings) {
				$("#settings_list").html('<table class="border" style="max-width: 100%;"><thead><tr><th>Key</th><th>Value</th><th></th><th></th></thead><tbody id="settings_tbody"></tbody>').prop("title", "");
				for (var i = 0; i < settings.length; i++) {
					var setting = settings[i];
					$("#settings_tbody").append(
						$("<tr>").append(
							$("<td>").text(setting.key)
						).append(
							$("<td>").text(setting.value)
						).append(
							$("<td>").append(
								$('<button style="width: auto;">')
								.prop("id", "edit_setting_"+i)
								.text("Edit")
								.click(
									function () {
										var key = edit_button_to_settings_keys_map[$(this).prop("id")];
										var val = prompt("Value:")
										if (val !== null) {
											$.post(
												$("#api_uri").val(),
												{
													"csrf_token": $("#csrf_token").val(),
													"action": "set",
													"key": key,
													"value": val
												},
												function () {
													updateSettings();
												},
												"text"
											).fail(
												function (xhr) {
													alert("Error! Could not edit setting.");
												}
											);
										}
									}
								)
							)
						).append(
							$("<td>").append(
								$('<button style="width: auto;">')
								.prop("id", "delete_setting_"+i)
								.text("Delete")
								.click(
									function () {
										var key = delete_button_to_settings_keys_map[$(this).prop("id")];
										if (confirm('Delete "'+key+'"?')) {
											$.post(
												$("#api_uri").val(),
												{
													"csrf_token": $("#csrf_token").val(),
													"action": "unset",
													"key": key
												},
												function () {
													updateSettings();
												},
												"text"
											).fail(
												function (xhr) {
													alert("Error! Could not delete setting.");
												}
											);
										}
									}
								)
							)
						)
					);
					edit_button_to_settings_keys_map["edit_setting_"+i] = setting.key;
					delete_button_to_settings_keys_map["delete_setting_"+i] = setting.key;
				}
			},
			"json"
		).fail(
			function (xhr) {
				$("#settings_list").html("<em><strong>Error!</strong> Could not get settings list from server.</em>").prop("title", xhr.responseText);
			}
		);
	}

	$("#create").click(
		function () {
			var key = prompt("Key:");
			if (key !== null) {
				var val = prompt("Value:");
				if (val !== null) {
					$.post(
						$("#api_uri").val(),
						{
							"csrf_token": $("#csrf_token").val(),
							"action": "set",
							"key": key,
							"value": val
						},
						function () {
							updateSettings();
						},
						"text"
					).fail(
						function (xhr) {
							alert("Error! Could not create setting.");
						}
					);
				}
			}
		}
	);

	$("#show_advanced").click(
		function () {
			$("#settings_list").html("<em>Retrieving settings list, please wait...</em>");
			updateSettings();
			$("#advanced").toggle();
		}
	);

</script>
<?php
			MainUiTemplate::footer();
			break;
		

		case 'action': // backwards compatibility
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


		case 'api':
			if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === Session::get('_csrf_token')) {
				if (isset($_POST['action'])) {
					switch ($_POST['action']) {
						case 'get_all':
							$settings = [];
							foreach (UserSettings::getAll() as $setting_key) {
								if (strlen($setting_key) < 2 || substr($setting_key, 0, 2) !== '__') {
									$settings[] = ['key' => $setting_key, 'value' => UserSettings::get($setting_key, '')];
								}
							}
							header('Content-type: text/json');
							echo json_encode($settings);
							break;


						case 'set':
							if (isset($_POST['key'], $_POST['value'])) {
								UserSettings::set($_POST['key'], $_POST['value']);
							} else {
								http_response_code(400);
								exit('There was a problem with the request.');
							}
							break;


						case 'unset':
							if (isset($_POST['key'])) {
								UserSettings::unset($_POST['key']);
							} else {
								http_response_code(400);
								exit('There was a problem with the request.');
							}
							break;


						default:
							http_response_code(400);
							exit('There was a problem with the request.');
							break;
					}
				} else {
					http_response_code(400);
					exit('There was a problem with the request.');
				}
			} else {
				http_response_code(403);
				exit('There was a problem with the request.');
			}
			break;

		default:
			Router::execErrorPage(404);
			break;
	}
});
