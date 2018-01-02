<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

MainUiTemplate::header('Users - Administration');
?>

<input id="api_uri" name="api_uri" value="<?=Router::getHtmlReadyUri('/admin/api')?>" type="hidden" />
<input id="csrf_token" type="hidden" name="csrf_token" value="<?=htmlspecialchars(Session::get('_csrf_token'))?>" />

<button id="create_open_modal" style="margin-bottom: 1em;">Create User</button>
<div id="create_modal" class="modal">
	<div class="content overflow">
		<h1>Create User</h1>
		<form id="create_form" action="<?=Router::getHtmlReadyUri('/admin/api')?>" method="post" class="full-width">
			<input type="hidden" name="csrf_token" value="<?=htmlspecialchars(Session::get('_csrf_token'))?>" />

			<input type="hidden" name="admin" value="users" />
			<input type="hidden" name="action" value="create" />

			<label for="create_name">Username:</label>
			<input id="create_name" name="name" value="" type="text" />

			<label for="create_password1">Password:</label>
			<input id="create_password1" name="password" value="" type="password" />
			<label for="create_password2">Confirm Password:</label>
			<input id="create_password2" value="" type="password" />

			<label>Type:</label>
			<input id="create_type_admin" name="type" value="administrator" type="radio" />
			<label for="create_type_admin">Administrator</label>
			<input id="create_type_standard" name="type" value="standard" type="radio" checked="checked" />
			<label for="create_type_standard">Standard User</label>
			<input id="create_type_guest" name="type" value="guest" type="radio" />
			<label for="create_type_guest">Guest</label>

			<label>Enabled:</label>
			<input id="create_enabled" name="enabled" value="true" type="checkbox" checked="checked" />
			<label for="create_enabled">Enabled</label>

			<label for="create_comment">Comment:</label>
			<textarea id="create_comment" name="comment"></textarea>

			<input id="create_submit" name="create" value="Create" type="submit" class="button-primary" />
			<input id="create_close_button" name="create_cancel" value="Close" type="reset" />
		</form>
	</div>
</div>
<script>

	function closeCreateModal() {
		$("#create_modal").fadeOut(100);
		$("#create_form").trigger("reset");
		updateEditTable();
	}

	$("#create_open_modal").click(function () {
		$("#create_modal").fadeIn(100);
		$("#create_name").focus();
	});
	$("#create_modal").click(function (event) {
		if (event.target == this) {
			closeCreateModal();
		}
	});
	$("#create_close").click(function () {
		closeCreateModal();
	});
	$("#create_close_button").click(function () {
		closeCreateModal();
	});
	$(window).keyup(function (event) {
		if (event.keyCode == 27) { // "Escape" key
			closeCreateModal();
		}
	});

	$("#create_form").submit(function(event) {
		event.preventDefault();
		if ($("#create_password1").val() === $("#create_password2").val()) {
			$.post(
				$("#api_uri").val(),
				$("#create_form").serialize(),
				function (result) {
					$("#create_form").trigger("reset");
					//updateEditTable();
				},
				"text"
			).fail(
				function (xhr) {
					$("#create_password1").val("");
					$("#create_password2").val("");
					alert("Error! "+xhr.responseText);
				}
			).always(
				function () {
					$("#create_name").focus();
				}
			);
		} else {
			alert("The passwords don't match. Please enter matching passwords.");
			$("#create_password1").val("");
			$("#create_password2").val("");
			$("#create_password1").focus();
		}
	});

</script>

<div class="overflow">
	<fieldset>
		<legend>Users</legend>
		<div id="user_manage"><em>Retrieving user list, please wait...</em></div>
		<div id="edit_modal" class="modal">
			<div class="content overflow">
				<h1>Edit User</h1>
				<div id="edit_status"></div>
				<div id="edit_form" class="form full-width">
					<label for="edit_name">Username:</label>
					<input id="edit_name" name="name" value="" type="text" />

					<br />

					<label for="edit_password1">Password:</label>
					<input id="edit_password1" name="password1" value="" type="password" />
					<label for="edit_password2">Confirm Password:</label>
					<input id="edit_password2" name="password2" value="" type="password" />
					<button id="edit_save_password">Change Password</button>

					<br />

					<label>Type:</label>
					<input id="edit_type_admin" name="type" value="admin" type="radio" />
					<label for="edit_type_admin">Administrator</label>
					<input id="edit_type_standard" name="type" value="standard" type="radio" checked="checked" />
					<label for="edit_type_standard">Standard User</label>
					<input id="edit_type_guest" name="type" value="guest" type="radio" />
					<label for="edit_type_guest">Guest</label>

					<br />

					<label>Enabled:</label>
					<input id="edit_enabled" name="enabled" value="true" type="checkbox" checked="checked" />
					<label for="edit_enabled">Enabled</label>

					<br />

					<label for="edit_comment">Comment:</label>
					<textarea id="edit_comment" name="comment"></textarea>

					<br />

					<button id="edit_delete" style="background-color: #f99;">Delete</button>

					<button id="edit_close_button">Close</button>
				</div>
			</div>
		</div>

		<script>

			var button_user_map = [];

			var edit_modal_ready = false;

			var current_user_id = null;

			function openEditModal(user_id) {
				$("#edit_form").hide();
				$("#edit_status").html("<em>Retrieving user information, please wait...</em>").prop("title", "").show();
				$.post(
					$("#api_uri").val(),
					{
						"csrf_token": $("#csrf_token").val(),
						"admin": "users",
						"action": "get",
						"user_id": user_id
					},
					function (user) {
						$("#edit_name").val(user.name);
						$("#edit_password1").val("");
						$("#edit_password2").val("");
						switch (user.type) {
							case 'administrator':
								$("#edit_type_admin").prop("checked", true);
								break;
							case 'standard':
								$("#edit_type_standard").prop("checked", true);
								break;
							case 'guest':
								$("#edit_type_guest").prop("checked", true);
								break;
							default:
								$("#edit_type_admin").prop("checked", false);
								$("#edit_type_standard").prop("checked", false);
								$("#edit_type_guest").prop("checked", false);
								break;
						}
						$("#edit_enabled").prop("checked", user.enabled);
						$("#edit_comment").val(user.comment);

						$("#edit_type_admin").prop("disabled", !user.can_demote);
						$("#edit_type_standard").prop("disabled", !user.can_demote);
						$("#edit_type_guest").prop("disabled", !user.can_demote);
						$("#edit_enabled").prop("disabled", !user.can_disable);
						$("#edit_delete").prop("disabled", !user.can_delete);

						$("#edit_status").hide();
						$("#edit_form").show();
						current_user_id = user_id;
						edit_modal_ready = true;
					},
					"json"
				).fail(
					function (xhr) {
						$("#edit_status").html("<em><strong>Error!</strong> Could not get the user information from the server.</em>").prop("title", xhr.responseText);
					}
				);
				$("#edit_modal").fadeIn(100);
			}

			function closeEditModal() {
				current_user_id = null;
				edit_modal_ready = false;
				$("#edit_modal").fadeOut(100);
				updateEditTable();
			}

			$("#edit_modal").click(function (event) {
				if (event.target == this) {
					closeEditModal();
				}
			});
			$("#edit_close").click(function () {
				closeEditModal();
			});
			$("#edit_close_button").click(function () {
				closeEditModal();
			});
			$(window).keyup(function (event) {
				if (event.keyCode == 27) { // "Escape" key
					closeEditModal();
				}
			});

			function updateEditTable() {
				$.post(
					$("#api_uri").val(),
					{
						"csrf_token": $("#csrf_token").val(),
						"admin": "users",
						"action": "get_all"
					},
					function (users) {
						button_user_map = [];
						$("#user_manage").html('<table class="border center"><thead><tr><th>Username</th><th>Type</th><th>Enabled</th><th></th></tr></thead><tbody id="users_tbody"></tbody></table>');
						for (var i = 0; i < users.length; i++) {
							var user = users[i];
							var row = $("<tr>");
							row.append($("<td>").text(user.name).prop("title", "User ID: "+user.id));
							switch (user.type) {
								case 'administrator':
									row.append($('<td style="background-color: #ff9;">').text("Administrator"));
									break;
								case 'standard':
									row.append($('<td style="background-color: #9f9;">').text("Standard User"));
									break;
								case 'guest':
									row.append($('<td style="background-color: #9ff;">').text("Guest"));
									break;
								default:
									row.append($('<td style="background-color: #ccc;">').text("Unknown"));
									break;
							}
							if (user.enabled) {
								row.append($('<td style="background-color: #9f9;">').text("Yes"));
							} else {
								row.append($('<td style="background-color: #f99;">').text("No"));
							}
							row.append(
								$("<td>").append(
									$('<button style="width: auto;">')
									.prop("id", "edit_user_"+i)
									.click(function(event){
										openEditModal(button_user_map[event.target.id]);
									})
									.text("Edit")
								)
							);
							button_user_map["edit_user_"+i] = user.id;
							$("#users_tbody").append(row);
						}
					},
					"json"
				).fail(
					function (xhr) {
						$("#user_manage").html("").append(
							$("<p>").append(
								$("<em>").html("<strong>Error!</strong> Could not get the list of users from the server.")
							).prop("title", xhr.responseText)
						);
					}
				);
			}

			$("#edit_name").change(function () {
				$.post(
					$("#api_uri").val(),
					{
						"csrf_token": $("#csrf_token").val(),
						"admin": "users",
						"action": "set",
						"set": "name",
						"value": $(this).val(),
						"user_id": current_user_id
					},
					function () {
						//updateEditTable();
					},
					"text"
				).fail(
					function (xhr) {
						alert("Error! Could not change username!");
					}
				);
			});

			$("#edit_save_password").click(function () {
				if ($("#edit_password1").val() === $("#edit_password2").val()) {
					$.post(
						$("#api_uri").val(),
						{
							"csrf_token": $("#csrf_token").val(),
							"admin": "users",
							"action": "set",
							"set": "password",
							"value": $("#edit_password1").val(),
							"user_id": current_user_id
						},
						function () {
							alert("The password has been changed.");
							$("#edit_password1").val("");
							$("#edit_password2").val("");
						},
						"text"
					).fail(
						function (xhr) {
							alert("Error! Could not change password!");
						}
					);
				} else {
					alert("The passwords don't match. Please enter matching passwords.");
					$("#edit_password1").val("");
					$("#edit_password2").val("");
					$("#edit_password1").focus();
				}
			});

			$("#edit_type_admin").change(function () {
				$.post(
					$("#api_uri").val(),
					{
						"csrf_token": $("#csrf_token").val(),
						"admin": "users",
						"action": "set",
						"set": "type",
						"value": "administrator",
						"user_id": current_user_id
					},
					function () {
						//updateEditTable();
					},
					"text"
				).fail(
					function (xhr) {
						alert("Error! Could not change account type!");
					}
				);
			});
			$("#edit_type_standard").change(function () {
				$.post(
					$("#api_uri").val(),
					{
						"csrf_token": $("#csrf_token").val(),
						"admin": "users",
						"action": "set",
						"set": "type",
						"value": "standard",
						"user_id": current_user_id
					},
					function () {
						//updateEditTable();
					},
					"text"
				).fail(
					function (xhr) {
						alert("Error! Could not change account type!");
					}
				);
			});
			$("#edit_type_guest").change(function () {
				$.post(
					$("#api_uri").val(),
					{
						"csrf_token": $("#csrf_token").val(),
						"admin": "users",
						"action": "set",
						"set": "type",
						"value": "guest",
						"user_id": current_user_id
					},
					function () {
						//updateEditTable();
					},
					"text"
				).fail(
					function (xhr) {
						alert("Error! Could not change account type!");
					}
				);
			});

			$("#edit_enabled").change(function () {
				$.post(
					$("#api_uri").val(),
					{
						"csrf_token": $("#csrf_token").val(),
						"admin": "users",
						"action": "set",
						"set": "enabled",
						"value": ($(this).is(":checked") ? "true" : "false"),
						"user_id": current_user_id
					},
					function () {
						//updateEditTable();
					},
					"text"
				).fail(
					function (xhr) {
						alert("Error! Could not enable/disable account!");
					}
				);
			});

			$("#edit_comment").change(function () {
				$.post(
					$("#api_uri").val(),
					{
						"csrf_token": $("#csrf_token").val(),
						"admin": "users",
						"action": "set",
						"set": "comment",
						"value": $(this).val(),
						"user_id": current_user_id
					},
					function () {
						//updateEditTable();
					},
					"text"
				).fail(
					function (xhr) {
						alert("Error! Could not set comment!");
					}
				);
			});

			$("#edit_delete").click(function () {
				if (confirm("Really delete \""+$("#edit_name").val()+"\"?")) {
					$.post(
						$("#api_uri").val(),
						{
							"csrf_token": $("#csrf_token").val(),
							"admin": "users",
							"action": "delete",
							"user_id": current_user_id
						},
						function () {
							closeEditModal();
						},
						"text"
					).fail(
						function (xhr) {
							alert("Error! Could not delete user!");
						}
					);
				}
			});

			updateEditTable();

		</script>
	</fieldset>
</div>

<?php
MainUiTemplate::footer();
