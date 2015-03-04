function builderFieldBlockView(fieldID) {
	var elemVal = $("textarea[name='"+fieldID+"']").val();
	if (typeof elemVal != "undefined") {
		var pattern = /{([\w\d\.]+)}/g;
		var viewElem = $("#view_" + fieldID);
		elemVal = elemVal.replace(pattern, "&lt;$1 value&gt;");
		elemVal = elemVal.replace(".", " ");
		viewElem.html(elemVal);
		$("#edit_" + fieldID).hide("slow");
		viewElem.show("slow");
	}
}

function builderFieldBlockEdit(fieldID) {
	$("#view_" + fieldID).hide("slow");
	$("#edit_" + fieldID).show("slow");
}

function builderFieldBlockSwitcher(fieldID) {
	var button = $("#button_" + fieldID);
	if (button.text() == 'Edit') {
		builderFieldBlockEdit(fieldID);
		button.text('View');
	}
	else if (button.text() == 'View') {
		builderFieldBlockView(fieldID);
		button.text('Edit');
	}
}

$("document").ready(function() {
	$("[id^='button_']").live("click", function() {
		var fieldID = $(this).parents(".htmlBlock").attr("id");
		builderFieldBlockSwitcher(fieldID);
	});
	$(".htmlBlock_info").live("click", function() {
		popUpWindow('listingFieldsKeys', 500, 300, 'You can use the following variables:'); return false;
	});
	$(".complexBlock_info").live("click", function() {
		popUpWindow($(this).attr('id') + "_details", 500, 300, 'You can use the following variables:'); return false;
	});
	$(".locationBlock_info").live("click", function() {
		popUpWindow($(this).attr('id') + "_details", 500, 300, 'You can use the following variables:'); return false;
	});

	$(".side-button").toggle(function(){
		$(".side-btn-txt").css("background-position", "-16px");
		$('#form_builder').animate({
			width: '366px',
			overflow: 'visible'
		}, 600);
		$(".form-builder-cont").show();
	}, function(){
		$(".form-builder-cont").hide();
		$(".side-btn-txt").css("background-position", "0px");
		$('#form_builder').animate({
			width: '35px',
			overflow: 'hidden'
		}, 600);
	});
});
