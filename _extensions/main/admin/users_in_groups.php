<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

MainUiTemplate::header('Users <-> Groups - Administration');
?>

<input id="api_uri" name="api_uri" value="<?=Router::getHtmlReadyUri('/admin/api')?>" type="hidden" />
<input id="csrf_token" type="hidden" name="csrf_token" value="<?=htmlspecialchars(Session::get('_csrf_token'))?>" />

<div id="status"><em>Retrieving user list, please wait...</em></div>
<div id="manage" style="display: none;">
	<label for="user_select">User:</label>
	<select id="user_select" style="width: auto;"></select>
	<label style="margin-top: .5em;">Groups:</label>
	<div id="groups">
		<em>Retrieving group list, please wait...</em>
	</div>
</div>
<script>

	var current_user = null;

	function updateUsers() {
		$("#user_select").html('');
		$.post(
			$("#api_uri").val(),
			{
				"csrf_token": $("#csrf_token").val(),
				"admin": "users_groups",
				"action": "get_users"
			},
			function (users) {
				for (var i = 0; i < users.length; i++) {
					var user = users[i];
					$("#user_select").append(
						$("<option>").val(user.id).text(user.name)
					);
				}
				$("#user_select").change();
				$("#manage").show();
				$("#status").hide();
			},
			"json"
		).fail(
			function (xhr) {
				$("#manage").hide();
				$("#status").html("<em><strong>Error!</strong> Could not get the user information from the server.</em>").prop("title", xhr.responseText).show();
			}
		);
	}

	function updateGroups(user_id) {
		$('#groups input[type="checkbox"').each(function (checkbox_id) {
			$($('#groups input[type="checkbox"')[checkbox_id]).prop("disabled", true);
		});
		current_user = user_id;
		$.post(
			$("#api_uri").val(),
			{
				"csrf_token": $("#csrf_token").val(),
				"admin": "users_groups",
				"action": "get_groups",
				"user_id": user_id
			},
			function (groups) {
				$("#groups").html("").prop("title", "");
				for (var i = 0; i < groups.length; i++) {
					var group = groups[i];
					$("#groups").append(
						$('<input type="checkbox">')
						.prop("id", "member_"+group.id)
						.prop("name", group.id)
						.prop("checked", group.member)
						.change(
							function (event) {
								$.post(
									$("#api_uri").val(),
									{
										"csrf_token": $("#csrf_token").val(),
										"admin": "users_groups",
										"action": "set_membership",
										"user_id": user_id,
										"group_id": $(this).prop("name"),
										"in_group": $(this).is(":checked")
									},
									function () {
										//$("#user_select").change();
									},
									"text"
								).fail(
									function (xhr) {
										alert("Error! Could not set group membership!");
										$("#user_select").change();
									}
								)
							}
						)
					).append(
						$('<label>')
						.prop("for", "member_"+group.id)
						.text(group.name)
					);
				}
			},
			"json"
		).fail(
			function (xhr) {
				$("#groups").html("<em><strong>Error!</strong> Could not get the group information from the server.</em>").prop("title", xhr.responseText);
			}
		);
	}

	$("#user_select").change(
		function () {
			var id = $(this).val();
			updateGroups(id);
		}
	);

	updateUsers();

</script>

<?php
MainUiTemplate::footer();
