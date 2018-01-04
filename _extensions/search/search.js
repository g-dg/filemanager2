"use strict";

var search_xhr;
function search() {
	$("#search_results_header_search_string").text($("#search_text").val());
	$("#search_results").text("Searching...");
	$("#search_modal").fadeIn(100);
	$("#search_text").blur();
	search_xhr = $.post(
		$("#search_api_uri").val(),
		{"searchString": $("#search_text").val(), "searchPath": $("#search_path").val()},
		function (results) {
			var tbody = $("<tbody>");
			for (var i = 0; i < results.length; i++) {
				var result = results[i];
				var table_row = $("<tr>");
				table_row.append($("<td>").addClass("img").append($("<img>").prop("src", result.icon).prop("alt", result.type)));
				switch (result.type) {
					case "dir":
						table_row.append($("<td>").append($("<a>").prop("href", result.uri).html(result.htmlReadyRelativeName)));
						break;
					case "file":
					case "audio":
					case "image":
					case "text":
					case "video":
						table_row.append($("<td>").append($("<a>").prop("href", result.uri).prop("target", "_blank").html(result.htmlReadyRelativeName)));
						break;
					case "inaccessible":
						table_row.append($("<td>").html(result.htmlReadyRelativeName));
						break;
					default:
						table_row.append($("<td>").append($("<a>").prop("href", result.uri).html(result.htmlReadyRelativeName)));
						break;
				}
				tbody.append(table_row);
			}
			if (results.length != 0) {
				var table = $("<table>").addClass("u-full-width listing").css("margin-bottom", "1em").append(
					$("<thead>").append(
						$("<tr>").append(
							$("<th>")
						).append(
							$("<th>").text("Name")
						)
					)
				);
				table.append(tbody);
				$("#search_results").html(table);
			} else {
				$("#search_results").text("Sorry, nothing matched your search.");
			}
		},
		"json"
	).fail(function() {
		$("#search_results").text("An error occurred. Please try again in a minute or two.");
	});
	$("#search_results").focus();
}

$("#search_modal").click(function (event) {
	if (event.target == this) {
		closeSearch();
	}
});

$("#search_text").keyup(function (event) {
	if (event.keyCode === 13) { // "Enter" key
		search();
	} else if (event.keyCode === 27) { // "Escape" key
		$(this).val("");
		$(this).blur();
	}
});

function closeSearch() {
	search_xhr.abort();
	$("#search_modal").fadeOut(100);
	$("#search_text").focus();
	$("#search_text").select();
}

$("#search_close").click(function () {
	closeSearch();
});

$(window).keyup(function (event) {
	if (event.keyCode == 27) { // "Escape" key
		closeSearch();
	}
});
