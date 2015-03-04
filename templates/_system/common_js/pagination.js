function showInputPage(layout) {
	$("#pageField_" + layout).css("display", "inline");
	$("#pageInput_" + layout).focus();
}

function checkEnteredPage(keyCode ,layout, totalPages, url) {
	var page = $("#pageInput_" + layout).val();
	if (parseInt(page) <= parseInt(totalPages) && page > 0 && !isNaN(page) && page.indexOf('.') == -1 && (page % 1 === 0)) {
		$("#pageInput_" + layout).css("border", "1px solid #CACACA");
		$("#enterButton_" + layout).attr("disabled", "");
		if (keyCode == 13) {
			goToPage(layout, url);
		}
	} else {
		$("#pageInput_" + layout).css("border", "1px solid #ff0000");
		$("#enterButton_" + layout).attr("disabled", "disabled");
		$("form").submit(function() {
			return false;
		})
	}
}

function goToPage(layout, url) {
	location.href = url + "&page=" + parseInt($("#pageInput_" + layout).val());
}

function hideInputPage(layout) {
	setTimeout(function () {
		$("#pageField_" + layout).css("display", "none");
	}, 1000);
}

function setAllCheckboxes() {
	var checkboxes = $("#all_checkboxes_control").closest("form").find(":checkbox");
	if ($("#all_checkboxes_control").is(":checked")) {
		checkboxes.attr("checked", "checked");
	} else {
		checkboxes.removeAttr("checked");
	}
}

function goSingleButton(action, textChooseItem, textToDelete) {
	if ($("input:checked").length > 0) {
		if (((action == "delete" || action == "remove") && confirm(textToDelete)) || action != "delete") {
			submitForm(action);
		}
	} else {
		$("#actionWarning").dialog("destroy");
		$("#actionWarning").attr({ title: "" });
		$("#actionWarning").html(textChooseItem).dialog({ width: 300 });
	}
}

function go(button, textToDelete, textChooseAction, textChooseItem) {
	if (isActionEmpty(button, textChooseAction, textChooseItem) == true) {
		var action = $("#selectedAction_" + button).val();
		if (((action == "delete" || action == "remove") && confirm(textToDelete)) || action != "delete") {
			submitForm(action);
		}
	}
}

function submitForm(action) {
	$("#action_name").attr("value", action);
	$("#action_name").parent("form").submit();
}

function isActionEmpty(button, textChooseAction, textChooseItem) {
	var inputChecked = $("input:checked").length;
	var actionChecked = $("#selectedAction_" + button).val();
	if (inputChecked > 0 && actionChecked != "") {
		return true;
	} else {
		var htmlMessage = "";
		if (actionChecked == "") {
			htmlMessage += textChooseAction;
		}
		if (inputChecked == 0) {
			htmlMessage += "<br />" + textChooseItem;
		}
		$("#actionWarning").dialog("destroy");
		$("#actionWarning").attr({title: ""});
		$("#actionWarning").html(htmlMessage).dialog({width: 300});
	}
}