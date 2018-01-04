<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

Router::registerPage('account', function($subpage) {
	Auth::authenticate();
	switch ($subpage) {
		case '':
			MainUiTemplate::header('My Account'); ?>
<input id="api_uri" type="hidden" value="<?=Router::getHtmlReadyUri('/account/api');?>" />
<input id="csrf_token" type="hidden" value="<?=htmlspecialchars(Session::get('_csrf_token'));?>" />

<fieldset>
	<legend>Username</legend>
	<code title="User ID: <?=htmlspecialchars(Auth::getCurrentUserId());?>"><?=str_replace(' ', '&nbsp;', htmlspecialchars(Auth::getCurrentUserName()));?></code>
</fieldset>
<fieldset>
	<legend>Account Type</legend>
	<code id="account_type"><em>Retrieving account type, please wait...</em></code>
</fieldset>
<script>

	$.post(
		$("#api_uri").val(),
		{
			"csrf_token": $("#csrf_token").val(),
			"action": "get_account_type",
		},
		function (account_type) {
			$("#account_type").text(account_type);
		},
		"text"
	).fail(
		function () {
			$("#account_type").html("<em><strong>Error!</strong> Could not retrieve account type from the server.</em>");
		}
	);

</script>
<fieldset>
	<legend>Full Name</legend>
	<div class="form">
		<input id="fullname" name="fullname" type="text" value="<?=htmlspecialchars(UserSettings::get('_main.account.full_name', Auth::getCurrentUserName()));?>" required="required" />
		<button id="update_fullname">Save</button>
	</div>
</fieldset>
<script>

	$("#update_fullname").click(
		function() {
			if ($("fullname").val() !== '') {
				$.post(
					$("#api_uri").val(),
					{
						"csrf_token": $("#csrf_token").val(),
						"action": "set_fullname",
						"fullname": $("#fullname").val()
					},
					function () {
						alert("The full name has been changed.");
						$("#_fullname").text($("#fullname").val());
					}
				).fail(
					function () {
						alert("Error! Could not change full name.");
					}
				);
			}
		}
	);

</script>

<?php if (Auth::getCurrentUserType() !== Auth::USER_TYPE_GUEST) { ?>
<fieldset>
	<legend>Password</legend>
	<div class="form">
		<!--<label for="old_password">Current password:</label>-->
		<input id="old_password" name="old_password" type="password" value="" placeholder="Current password" />

		<!--<label for="new_password1">New password:</label>-->
		<input id="new_password1" name="new_password" type="password" value="" placeholder="New password" />

		<!--<label for="new_password2">Confirm new password:</label>-->
		<input id="new_password2" type="password" value="" placeholder="Confirm new password" />

		<button id="change_password">Change Password</button>
	</div>
</fieldset>
<script>

	$("#change_password").click(
		function () {
			if ($("#new_password1").val() === $("#new_password2").val()) {
				$.post(
					$("#api_uri").val(),
					{
						"csrf_token": $("#csrf_token").val(),
						"action": "set_password",
						"old_password": $("#old_password").val(),
						"new_password": $("#new_password1").val()
					},
					function () {
						alert("The password has been changed.");
						$("#old_password").val("");
						$("#new_password1").val("");
						$("#new_password2").val("");
						$("#old_password").focus();
					},
					"text"
				).fail(
					function (xhr) {
						alert(xhr.responseText);
					}
				);
			} else {
				alert("The new passwords don't match.");
				$("#new_password1").focus();
			}
		}
	);

	$("#old_password").keyup(
		function (event) {
			if (event.keyCode == 13) { // "Enter" key
				$("#change_password").click();
			}
		}
	);
	$("#new_password1").keyup(
		function (event) {
			if (event.keyCode == 13) { // "Enter" key
				$("#change_password").click();
			}
		}
	);
	$("#new_password2").keyup(
		function (event) {
			if (event.keyCode == 13) { // "Enter" key
				$("#change_password").click();
			}
		}
	);

</script>
<?php } ?>

<fieldset>
	<legend>Groups</legend>
	<div id="groups">
		<em>Retrieving group list, please wait...</em>
	</div>
</fieldset>
<script>

	$.post(
		$("#api_uri").val(),
		{
			"csrf_token": $("#csrf_token").val(),
			"action": "get_groups"
		},
		function (groups) {
			if (groups.length > 0) {
				$("#groups").html('<ul id="group_list"></ul>').prop("title", "");
				for (var i = 0; i < groups.length; i++) {
					$("#group_list").append(
						$("<li>").text(groups[i])
					);
				}
			} else {
				$("#groups").html('<em>&lt;none&gt;</em>').prop("title", "");
			}
		},
		"json"
	).fail(
		function (xhr) {
			$("#groups").html("<em><strong>Error!</strong> Could not retrieve the list of groups from the server.</em>").prop("title", xhr.responseText);
		}
	);

</script>

<?php MainUiTemplate::footer();
			break;


		case 'api':
			if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === Session::get('_csrf_token')) {
				if (isset($_POST['action'])) {
					switch ($_POST['action']) {

						case 'get_groups':
							$groups = [];
							foreach (Users::getGroups(Auth::getCurrentUserId()) as $group_id) {
								$groups[] = Groups::getName($group_id);
							}
							header('Content-type: text/json');
							echo json_encode($groups);
							break;


						case 'get_account_type':
							switch (Auth::getCurrentUserType()) {
								case Auth::USER_TYPE_ADMIN:
									echo 'Administrator';
									break;
								case Auth::USER_TYPE_STANDARD:
									echo 'Standard user';
									break;
								case Auth::USER_TYPE_GUEST:
									echo 'Guest';
									break;
								default:
									echo 'Unknown';
									break;
							}
							break;


						case 'set_fullname':
							if (isset($_POST['fullname'])) {
								UserSettings::set('_main.account.full_name', $_POST['fullname']);
							} else {
								http_response_code(400);
								exit('There was a problem with the request.');
							}
							break;


						case 'set_password':
							if (isset($_POST['old_password'], $_POST['new_password'])) {
								if (Auth::checkPassword(Auth::getCurrentUserId(), $_POST['old_password'])) {
									Users::setPassword(Auth::getCurrentUserId(), $_POST['new_password']);
								} else {
									http_response_code(403);
									exit('The old password is incorrect.');
								}
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
