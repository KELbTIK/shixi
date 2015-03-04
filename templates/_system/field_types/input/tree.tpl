<script language="JavaScript" type="text/javascript" src="{common_js}/tree.js"></script>

{capture name="trLess"}&#171;&nbsp;{tr}less{/tr|escape:'quotes'}{/capture}
{capture name="trMore"}&#187;&nbsp;{tr}more{/tr|escape:'quotes'}{/capture}

<div class="tree-input-field">
	<div class="left">
		<a href="#" id="tree-{$id}-options" style="display:inline-block;">[[Click to select]]</a>
		<div id="tree-{$id}-values" class="tree-values"></div>
		<div id="tree-{$id}-values-more" style="display: none;"></div>
		<div id="tree-{$id}-values-more-button" class="more-button" onclick="buttonMoreTreeValuesClick('{$id}', '{$smarty.capture.trLess}', '{$smarty.capture.trMore}');" style="display: none; cursor: pointer;">{$smarty.capture.trMore}</div>
	</div>
	<div class="clr"></div>
</div>
{if $choiceLimit}
	<div id="{$id}-available" class="tree-available-count">{$choiceLimit}/{$choiceLimit} [[Available]]</div>
{/if}
<input type="hidden" name="{$id}[tree]" id="tree-{$id}-selected" value="" />


<script language='JavaScript' type='text/javascript'>
	var {$id}Values = [];

	$("#tree-" + "{$id}" + "-options").click(function(event) {
		event.preventDefault();
		var checked     = $("#tree-" +"{$id}" + "-selected").val();
		var name        = "{$id}";
		var id          = "{$sid}";
		var choiceLimit = "{$choiceLimit}";
		var caption     = "[[$caption]]";
		var userTree    = "{$userTree}";
		
		var url = "{$GLOBALS.site_url}/tree-options/?check=" + checked + "&name=" + name + "&id=" + id + "&choiceLimit=" + choiceLimit + "&userTree=" + userTree;
		popUpWindow(url, 700, caption, NaN, NaN, popUpClose{$id});
	});

	function getObject{$id}()
	{
		return new Object({
			"arrayName"      : "{$id}Values",
			"fieldId"        : "{$id}",
			"trMore"         : "{$smarty.capture.trMore}",
			"availableCount" : "{$choiceLimit}",
			"availableTitle" : "[[Available]]",
			"default"        : ""
		});
	}

	function popUpClose{$id}()
	{
		treePopUpClose({$id}Values, getObject{$id}());
	}

	{if $value}
		addElements({$id}Values, [{$value}], getObject{$id}());
	{/if}
</script>