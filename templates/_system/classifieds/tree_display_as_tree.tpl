<div id="tree-available"></div>
<div id="tree-deselect-all" onclick="treeDeselectAll();">
	<small>[[Deselect all]]</small>
</div>
<div class="clearfix"></div>

<div id="tree-block"></div>
<div id="tree-buttons">
	<input class="btn btn-primary" type="button" onClick="saveSelected = true; $('#messageBox').dialog('close');"  value="[[Select]]" />
	<input class="btn btn-danger" type="button" onClick="$('#messageBox').dialog('close');"  value="[[Cancel]]" />
</div>

<script type='text/javascript'>
	saveSelected = false;
	$(document).ready(function() {
		var treeHtml = getTreeHtml("{$fieldId}", new Object({ {$treeValues} }), [{$checked}], 0, 0);
		$("#tree-block").html(treeHtml);
		changeAvailableCount("[[Available]]", {$choiceLimit});
	});
</script>
