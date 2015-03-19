function addElements(elements, newElements, parameters)
{
	$.each(newElements, function(index, element) {
		elements.push(
			new Object({
				'id'      : element['sid'],
				'caption' : htmlentities(element['caption']),
				'level'   : element['level'],
				'value'   : element['value']
			})
		);
	});
	
	showElements(elements, parameters);
}

function treePopUpClose(elements, parameters)
{
	if (!saveSelected) {
		return;
	}
	
	elements.splice(0, elements.length);
	
	$("#tree-block").find(".checked, .half_checked").each(function() {
		elements.push(
			new Object({
				'id'      : $(this).parent("li").attr("id").substr(8),
				'caption' : $(this).children("span").html(),
				'level'   : $(this).attr("level"),
				'value'   : $(this).parent("li").children("ul").length == 0 ? $(this).parent("li").children("input").val() : ""
			})
		);
	});
	
	showElements(elements, parameters);
}

function removeTreeElement(id, elements, parameters)
{
	for (var i = 0; i < elements.length; i++) {
		if (elements[i]['id'] == id) {
			var elementLevel = elements[i]['level'];
			var removeCount  = 1;
			// remove children
			while (i + removeCount < elements.length && elements[i + removeCount]['level'] > elementLevel) {
				removeCount++;
			}
			
			// remove parents
			while (i > 0 && elements[i - 1]['level'] < elementLevel
				&& (i + removeCount >= elements.length || elements[i + removeCount]['level'] <= elements[i - 1]['level'] )) {
				elementLevel = elements[i - 1]['level'];
				i--;
				removeCount++;
			}
			
			elements.splice(i, removeCount);
			break;
		}
	}
	showElements(elements, parameters);
}

function showElements(elements, parameters)
{
	var countShownElements = 0;
	var countChildren      = 0;
	var countParents       = 0;
	var showLimit          = 10;
	
	var treeSelectedValues = "";
	
	var treeValues     = $("#tree-" + parameters["fieldId"] + "-values");
	var treeValuesMore = $("#tree-" + parameters["fieldId"] + "-values-more");
	
	treeValues.html("");
	treeValuesMore.html("");
	
	for (var i = 0; i < elements.length; i++) {
		if (elements[i]['level'] == "0") {
			countParents++;
		}
	}
	
	$.each(elements, function(index, elem) {
		var id      = elem['id'];
		var caption = elem['caption'];
		var level   = elem['level'];
		var style   = "";
		
		if (level != "0") {
			style = 'class="tree-child-' + String(parseInt(level)) + '"';
		} else {
			style = 'class="tree-parent"';
		}
		
		if (elem['value'] != "") {
			countChildren++;
			treeSelectedValues += treeSelectedValues != "" ? "," + elem['value']: elem['value'];
		}
		
		if (countParents < 4 || elem['level'] == "0") {
			countShownElements++;
			
			var countParameters = 0;
			var onClickParameters = "new Object({";
			$.each(parameters, function(index, value) {
				if (countParameters++) {
					onClickParameters += ",";
				}
				onClickParameters += "'" + index + "':" + "'" + value + "'";
			});
			onClickParameters += "})";
			
			var onClickFunction = 'onclick="removeTreeElement(' + id + ', ' + parameters["arrayName"] + ', ' + onClickParameters + ');"';
			var html = '<div id="' + id + '" ' + style + ' level="' + level + '"><span class="tree-child-close" ' + onClickFunction + '>X</span>' + caption + '</div>';
			if (countShownElements > showLimit) {
				treeValuesMore.append(html);
			} else {
				treeValues.append(html);
			}
		}
	});

	$("#tree-" + parameters["fieldId"] + "-selected").val(treeSelectedValues);
	if (!countChildren) {
		treeValues.html(parameters["default"]);
		treeValuesMore.html("");
		elements.splice(0, elements.length);
		
		hideMoreTreeValues(parameters["fieldId"], parameters["trMore"]);
	}
	else if (countShownElements > showLimit) {
		$("#tree-" + parameters["fieldId"] + "-values-more-button").css("display", "block");
	} else {
		hideMoreTreeValues(parameters["fieldId"], parameters["trMore"]);
	}
	
	if (!empty(parameters["availableCount"])) {
		var deltaCount = parseInt(parameters["availableCount"]) - countChildren;
		var text = (deltaCount > 0 ? deltaCount : 0)+ "/" + parameters["availableCount"] + " " + parameters["availableTitle"];
		$("#" + parameters["fieldId"] + "-available").html(text);
	}
}

function hideMoreTreeValues(fieldId, trMore)
{
	var buttonMore = $("#tree-" + fieldId + "-values-more-button");
	buttonMore.html(trMore);
	buttonMore.css("display", "none");
	$("#tree-" + fieldId + "-values-more").css("display", "none");
}

function buttonMoreTreeValuesClick(fieldId, trLess, trMore)
{
	var button = $("#tree-" + fieldId + "-values-more-button");
	button.prev("#tree-" + fieldId + "-values-more").slideToggle("normal", function() {
		if ($(this).css("display") == "block") {
			button.html(trLess);
		} else {
			button.html(trMore);
		}
	});
}



// POPUP PAGE ----------------------------------------------------------------------

var saveSelected   = false;
var selectedCount  = 0;
var availableCount = 1000;
var availableTitle = "Available";

function getTreeHtml(fieldId, treeValues, checkedValues, parent, level)
{
	if (typeof parent === 'undefined') {
		parent = 0;
	}
	if (typeof level === 'undefined') {
		level = 0;
	}
	
	var html = '';
	if (array_key_exists(parent, treeValues)) {
		if (level > 0) {
			html += "<ul id='tree-ul-" + parent + "'>";
		} else {
			html += "<ul class='tree' id='tree-" + fieldId + "'> ";
		}
		
		// processing each child
		$.each(treeValues[parent], function(index, element) {
			var elementSid = element['sid'];
			
			var checked = '';
			if (array_key_exists(elementSid, treeValues)) {// if current element has children
				checked = haveSelectedChildren(elementSid, checkedValues, treeValues);
			}
			else if (in_array(elementSid, checkedValues)) {// if current element checked
				checked = 'checked';
			}
			
			html += getTreeElementHtml(elementSid, parent, level, checked, element['caption']);
			html += getTreeHtml(fieldId, treeValues, checkedValues, elementSid, level + 1);
			html += "</li>";
		});
		
		html += "</ul>";
	}
	
	return html;
}

function getTreeElementHtml(elementSid, parent, level, checked, caption)
{
	if (!level) {
		caption = "<span class=\"strong\">" + htmlentities(caption) + "</span>";
	} else {
		caption = "<span>" + htmlentities(caption) + "</span>";
	}
	
	var classes = ' class="checkbox ' + checked + '"';
	var onClick = ' onclick="treeElementClick(' + elementSid + ', ' + parent + ', ' + level + ')" ';
	var id      = ' id="tree-check-' + elementSid + '"';
	var value   = ' value="' + elementSid + '"';
	var style   = ' style="display: none;"';
	level       = ' level="' + level + '"';
	
	return '<li id="tree-li-' + elementSid + '">'
		+ '<div' + classes + onClick + level + '>' + caption + '</div>'
		+ '<input type="checkbox"' + id + value + (!empty(checked) ? ' checked="checked"' : '') + style + ' />';
}

function haveSelectedChildren(sid, checkedValues, treeValues)
{
	var checkedChild = "";
	var count = 0;
	$.each(treeValues[sid], function(index, treeValue) {
		for (var i = 0; i < checkedValues.length; i++) {
			
			if (checkedValues[i] == treeValue['sid']) {
				count++;
				break;
			}
			else if (array_key_exists(treeValue['sid'], treeValues)) {
				checkedChild = haveSelectedChildren(treeValue['sid'], checkedValues, treeValues);
				
				if (checkedChild == "checked") {
					count++;
				}
				
				if (!empty(checkedChild)) {
					break;
				}
			}
		}
	});
	
	if (count == treeValues[sid].length) {
		return "checked";
	}
	else if (count || !empty(checkedChild)) {
		return "half_checked";
	}
	
	return '';
}

function treeElementClick(id, parentId, level)
{
	var inputBox = $("#tree-check-" + id);
	var action   = "";
	if (inputBox.is(':checked') == false) {
		action = "checked";

	}
	if (selectedCount >= availableCount && action == "checked") {
		return;
	}

	var childrenBlock = $("#tree-li-" + id);
	if (childrenBlock.children("ul").size() > 0) {// if it has children
		setChildrenStatus(id, action);
		setParentStatus(id);
	} else {
		if (action == "checked") {
			inputBox.prop("checked", true);
		} else {
			inputBox.prop("checked", false);
		}
		childrenBlock.children(".checkbox").removeClass().addClass("checkbox").addClass(action);
	}
	
	// parent
	if (level > 0) {
		setParentsStatus(parentId, level);
	}
	
	changeAvailableCount();
}

function setParentsStatus(id, level)
{
	setParentStatus(id);
	if (level > 0) {
		var parentId = $("#tree-li-" + id).parent("ul").attr("id").substr(8);
		setParentsStatus(parentId, level - 1);
	}
}

function setChildrenStatus(fieldId, action)
{
	$("#tree-li-" + fieldId).children("ul").each(function(ul) {
		$(this).children("li").each(function(li) {
			if ($(this).children("ul").size() > 0) {
				var id = $(this).attr("id").substr(8);
				setChildrenStatus(id, action);
				setParentStatus(id);
			} else {
				if (action == "checked") {
					if (selectedCount == availableCount) {
						return;
					}
					else if (!$(this).children(":checkbox").attr("checked")) {
						selectedCount++;
					}
				} else {
					selectedCount--;
				}
				
				$(this).children(".checkbox").removeClass().addClass("checkbox").addClass(action);
				$(this).children(":checkbox").attr("checked", action);
			}
		});
	});
}

function setParentStatus(fieldId)
{
	var parentLi = $("#tree-li-" + fieldId);
	var total    = $('ul > li', parentLi).size();
	var checked  = $('ul > li .checked', parentLi).size();
	
	var classes = "";
	if (checked == total) {
		classes = "checked";
	}
	else if (checked > 0) {
		classes = "half_checked";
	}
	
	parentLi.children(".checkbox").removeClass().addClass("checkbox").addClass(classes);
	if (classes == "checked" || classes == "half_checked") {
		$("#tree-check-" + fieldId).prop("checked", true);
	} else {
		$("#tree-check-" + fieldId).prop("checked", false);
	}
}

function treeDeselectAll()
{
	var pattern = /[a-zA-Z0-9_]*\((\w+),\s*(\w+),\s*(\w+)\)/;
	$("#tree-block").find(".checked").each(function() {
		if ( $(this).hasClass('checked') ) {
			var elementFunction = $(this).attr('onclick');
			var array = pattern.exec(elementFunction);
			treeElementClick(array[1], array[2], array[3]);
		}
	});
	
	changeAvailableCount();
}


function changeAvailableCount(title, count)
{
	if (typeof title !== 'undefined') {
		availableTitle = title;
	}
	if (count == 0 || typeof count !== 'undefined') {
		availableCount = count ? count : 1000;
	}
	
	selectedCount = 0;
	$("#tree-block").find(".checked").each(function() {
		if ($(this).parent("li").children("ul").length == 0) {
			selectedCount++;
		}
	});
	
	if (availableCount != 1000) {
		var deltaCount = availableCount - selectedCount;
		var text = (deltaCount > 0 ? deltaCount : 0) + "/" + availableCount + " " + availableTitle;
		$("#tree-available").html(text);
	}
}
