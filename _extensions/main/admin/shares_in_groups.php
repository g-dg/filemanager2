<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

MainUiTemplate::header('Shares <-> Groups - Administration');
?>

<input id="api_uri" name="api_uri" value="<?=Router::getHtmlReadyUri('/admin/api')?>" type="hidden" />
<input id="csrf_token" type="hidden" name="csrf_token" value="<?=htmlspecialchars(Session::get('_csrf_token'))?>" />

<div id="status"><em>Retrieving share list, please wait...</em></div>
<div id="manage" style="display: none;">
	<label for="share_select">Share:</label>
	<select id="share_select" style="width: auto;"></select>
	<label style="margin-top: .5em;">Groups:</label>
	<div id="groups">
		<em>Retrieving group list, please wait...</em>
	</div>
</div>
<script>

	var current_share = null;

	function updateShares() {
		$("#share_select").html('');
		$.post(
			$("#api_uri").val(),
			{
				"csrf_token": $("#csrf_token").val(),
				"admin": "shares_groups",
				"action": "get_shares"
			},
			function (shares) {
				for (var i = 0; i < shares.length; i++) {
					var share = shares[i];
					$("#share_select").append(
						$("<option>").val(share.id).text(share.name)
					);
				}
				$("#share_select").change();
				$("#manage").show();
				$("#status").hide();
			},
			"json"
		).fail(
			function (xhr) {
				$("#manage").hide();
				$("#status").html("<em><strong>Error!</strong> Could not get the share information from the server.</em>").prop("title", xhr.responseText).show();
			}
		);
	}

	function updateGroups(share_id) {
		$('#groups input[type="checkbox"').each(function (checkbox_id) {
			$($('#groups input[type="checkbox"')[checkbox_id]).prop("disabled", true);
		});
		current_share = share_id;
		$.post(
			$("#api_uri").val(),
			{
				"csrf_token": $("#csrf_token").val(),
				"admin": "shares_groups",
				"action": "get_groups",
				"share_id": share_id
			},
			function (groups) {
				$("#groups").html('<div style="margin-bottom: .5em;">[Read] [Write]</div>').prop("title", "");
				for (var i = 0; i < groups.length; i++) {
					var group = groups[i];
					$("#groups").append(
						$('<input type="checkbox">')
						.prop("id", "member_read_"+group.id)
						.prop("title", "Read")
						.prop("name", group.id)
						.prop("checked", group.can_read)
						.change(
							function () {
								var group_id = $(this).prop("name");
								$("#member_write_"+group_id).prop("disabled", !$(this).is(":checked"));
								$("#member_write_"+group_id).prop("checked", false);
								$.post(
									$("#api_uri").val(),
									{
										"csrf_token": $("#csrf_token").val(),
										"admin": "shares_groups",
										"action": "set_readable",
										"group_id": group_id,
										"share_id": current_share,
										"readable": $(this).is(":checked")
									},
									function () {
										//updateGroups();
									},
									"text"
								).fail(
									function () {
										alert("Error! Could not save changes.");
									}
								);
							}
						)
					).append(
						$('<input type="checkbox" style="margin-left: 0;">')
						.prop("id", "member_write_"+group.id)
						.prop("title", "Write")
						.prop("name", group.id)
						.prop("checked", group.can_write)
						.prop("disabled", !group.can_read)
						.change(
							function () {
								var group_id = $(this).prop("name");
								$.post(
									$("#api_uri").val(),
									{
										"csrf_token": $("#csrf_token").val(),
										"admin": "shares_groups",
										"action": "set_writable",
										"group_id": group_id,
										"share_id": current_share,
										"writable": $(this).is(":checked")
									},
									function () {
										//updateGroups();
									},
									"text"
								).fail(
									function () {
										alert("Error! Could not save changes.");
									}
								);
							}
						)
					).append(
						$('<label>')
						.prop("for", "member_read_"+group.id)
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

	$("#share_select").change(
		function () {
			var id = $(this).val();
			updateGroups(id);
		}
	);

	updateShares();

</script>

<?php
MainUiTemplate::footer();
