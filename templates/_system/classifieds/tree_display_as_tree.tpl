<div id="tree-available"></div>
<div id="tree-deselect-all" onclick="treeDeselectAll();">
	<small>[[Deselect all]]</small>
</div>
<div class="clr"></div>

<div id="tree-block"></div>
<div id="tree-buttons">
	<input type="button" onClick="saveSelected = true; $('#messageBox').dialog('close');"  value="[[Select]]" />
	<input type="button" onClick="$('#messageBox').dialog('close');"  value="[[Cancel]]" />
</div>

<script language='JavaScript' type='text/javascript'>
	saveSelected = false;
	$(document).ready(function() {
		var treeHtml = getTreeHtml("{$fieldId}", new Object({ {$treeValues} }), [{$checked}], 0, 0);
		$("#tree-block").html(treeHtml);
		changeAvailableCount("[[Available]]", {$choiceLimit});
	});
</script>
