<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

MainUiTemplate::header('Shares - Administration');
?>

<input id="api_uri" name="api_uri" value="<?=Router::getHtmlReadyUri('/admin/api')?>" type="hidden" />
<input id="csrf_token" type="hidden" name="csrf_token" value="<?=htmlspecialchars(Session::get('_csrf_token'))?>" />

<button id="create_open_modal" style="margin-bottom: 1em;">Create Share</button>
<div id="create_modal" class="modal">
	<div class="content overflow">
		<h1>Create Share</h1>
		<form id="create_form" action="<?=Router::getHtmlReadyUri('/admin/api')?>" method="post" class="full-width">
			<input type="hidden" name="csrf_token" value="<?=htmlspecialchars(Session::get('_csrf_token'))?>" />

			<input type="hidden" name="admin" value="shares" />
			<input type="hidden" name="action" value="create" />

			<label for="create_name">Name:</label>
			<input id="create_name" name="name" value="" type="text" />

			<label for="create_path">Path:</label>
			<input id="create_path" name="path" value="" type="text" required="required" />

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
				alert("Error! "+xhr.responseText);
			}
		).always(
			function () {
				$("#create_name").focus();
			}
		);
	});

</script>

<div class="overflow">
	<fieldset>
		<legend>Shares</legend>
		<div id="share_manage"><em>Retrieving share list, please wait...</em></div>
		<div id="edit_modal" class="modal">
			<div class="content overflow">
				<h1>Edit Share</h1>
				<div id="edit_status"></div>
				<div id="edit_form" class="form full-width">
					<label for="edit_name">Name:</label>
					<input id="edit_name" name="name" value="" type="text" />

					<br />

					<label for="edit_path">Path:</label>
					<input id="edit_path" name="path" value="" type="text" required="required" />

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

			var button_share_map = [];

			var edit_modal_ready = false;

			var current_share_id = null;

			function openEditModal(share_id) {
				$("#edit_form").hide();
				$("#edit_status").html("<em>Retrieving share information, please wait...</em>").prop("title", "").show();
				$.post(
					$("#api_uri").val(),
					{
						"csrf_token": $("#csrf_token").val(),
						"admin": "shares",
						"action": "get",
						"share_id": share_id
					},
					function (share) {
						$("#edit_name").val(share.name);
						$("#edit_path").val(share.path);
						$("#edit_enabled").prop("checked", share.enabled);
						$("#edit_comment").val(share.comment);

						$("#edit_status").hide();
						$("#edit_form").show();
						current_share_id = share_id;
						edit_modal_ready = true;
					},
					"json"
				).fail(
					function (xhr) {
						$("#edit_status").html("<em><strong>Error!</strong> Could not get the share information from the server.</em>").prop("title", xhr.responseText);
					}
				);
				$("#edit_modal").fadeIn(100);
			}

			function closeEditModal() {
				current_share_id = null;
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
						"admin": "shares",
						"action": "get_all"
					},
					function (shares) {
						button_share_map = [];
						$("#share_manage").html('<table class="border center"><thead><tr><th>Name</th><th>Enabled</th><th></th></tr></thead><tbody id="shares_tbody"></tbody></table>');
						for (var i = 0; i < shares.length; i++) {
							var share = shares[i];
							var row = $("<tr>");
							row.append($("<td>").text(share.name).prop("title", "Share ID: "+share.id));
							if (share.enabled) {
								row.append($('<td style="background-color: #9f9;">').text("Yes"));
							} else {
								row.append($('<td style="background-color: #f99;">').text("No"));
							}
							row.append(
								$("<td>").append(
									$('<button style="width: auto;">')
									.prop("id", "edit_share_"+i)
									.click(function(event){
										openEditModal(button_share_map[event.target.id]);
									})
									.text("Edit")
								)
							);
							button_share_map["edit_share_"+i] = share.id;
							$("#shares_tbody").append(row);
						}
					},
					"json"
				).fail(
					function (xhr) {
						$("#share_manage").html("").append(
							$("<p>").append(
								$("<em>").html("<strong>Error!</strong> Could not get the list of shares from the server.")
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
						"admin": "shares",
						"action": "set",
						"set": "name",
						"value": $(this).val(),
						"share_id": current_share_id
					},
					function () {
						//updateEditTable();
					},
					"text"
				).fail(
					function (xhr) {
						alert("Error! Could not change name!");
					}
				);
			});

			$("#edit_path").change(function () {
				$.post(
					$("#api_uri").val(),
					{
						"csrf_token": $("#csrf_token").val(),
						"admin": "shares",
						"action": "set",
						"set": "path",
						"value": $(this).val(),
						"share_id": current_share_id
					},
					function () {
						//updateEditTable();
					},
					"text"
				).fail(
					function (xhr) {
						alert("Error! Could not change path!");
					}
				);
			});

			$("#edit_enabled").change(function () {
				$.post(
					$("#api_uri").val(),
					{
						"csrf_token": $("#csrf_token").val(),
						"admin": "shares",
						"action": "set",
						"set": "enabled",
						"value": ($(this).is(":checked") ? "true" : "false"),
						"share_id": current_share_id
					},
					function () {
						//updateEditTable();
					},
					"text"
				).fail(
					function (xhr) {
						alert("Error! Could not enable/disable share!");
					}
				);
			});

			$("#edit_comment").change(function () {
				$.post(
					$("#api_uri").val(),
					{
						"csrf_token": $("#csrf_token").val(),
						"admin": "shares",
						"action": "set",
						"set": "comment",
						"value": $(this).val(),
						"share_id": current_share_id
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
							"admin": "shares",
							"action": "delete",
							"share_id": current_share_id
						},
						function () {
							closeEditModal();
						},
						"text"
					).fail(
						function (xhr) {
							alert("Error! Could not delete share!");
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
