<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

MainUiTemplate::header('Global Settings - Administration');
?>

<input id="api_uri" name="api_uri" value="<?=Router::getHtmlReadyUri('/admin/api')?>" type="hidden" />
<input id="csrf_token" type="hidden" name="csrf_token" value="<?=htmlspecialchars(Session::get('_csrf_token'))?>" />

<button id="create" style="margin-bottom: 1em;">Create</button>
<div id="settings_list" class="overflow"><em>Retrieving settings list, please wait...</em></div>

<script>

	var edit_button_to_settings_keys_map = [];
	var delete_button_to_settings_keys_map = [];

	function updateSettings() {
		$.post(
			$("#api_uri").val(),
			{
				"csrf_token": $("#csrf_token").val(),
				"admin": "global_settings",
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
													"admin": "global_settings",
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
													"admin": "global_settings",
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
							"admin": "global_settings",
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

	updateSettings();

</script>

<?php
MainUiTemplate::footer();
