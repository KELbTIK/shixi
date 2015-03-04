<script language="JavaScript" type="text/javascript" src="{common_js}/tree.js"></script>

{capture name="trLess"}&#171;&nbsp;{tr}[[less]]{/tr|escape:'quotes'}{/capture}
{capture name="trMore"}&#187;&nbsp;{tr}[[more]]{/tr|escape:'quotes'}{/capture}
{capture name="fieldId"}{$id|replace:'_':'-'}{/capture}

{if $object_sid->details->properties.choiceLimit->value}
	{capture name="choiceLimit"}{$object_sid->details->properties.choiceLimit->value}{/capture}
{elseif $listing_field_info.choiceLimit}
	{capture name="choiceLimit"}{$listing_field_info.choiceLimit}{/capture}
{elseif $user_profile_field_info}
	{capture name="choiceLimit"}{$user_profile_field_info.choiceLimit}{/capture}
{elseif $choiceLimit}
	{capture name="choiceLimit"}{$choiceLimit}{/capture}
{else}
	{capture name="choiceLimit"}0{/capture}
{/if}

<div class="tree-input-field">
	<div class="left">
		<a href="#" id="tree-{$smarty.capture.fieldId}-options" style="display:inline-block;">[[Click to select]]</a>
		<div id="tree-{$smarty.capture.fieldId}-values" class="tree-values"></div>
		<div id="tree-{$smarty.capture.fieldId}-values-more" style="display: none;"></div>
		<div id="tree-{$smarty.capture.fieldId}-values-more-button" class="more-button" onclick="buttonMoreTreeValuesClick('{$smarty.capture.fieldId}', '{$smarty.capture.trLess}', '{$smarty.capture.trMore}');" style="display: none; cursor: pointer;">{$smarty.capture.trMore}</div>
	</div>
	<div class="clr"></div>
</div>
{if $smarty.capture.choiceLimit}
	<div id="{$smarty.capture.fieldId}-available" class="tree-available-count">{$smarty.capture.choiceLimit}/{$smarty.capture.choiceLimit} [[Available]]</div>
{/if}
<input type="hidden" name="{$id}[tree]" id="tree-{$smarty.capture.fieldId}-selected" value="" />


<script language='JavaScript' type='text/javascript'>
	var {$id}Values = [];

	$("#tree-" + "{$smarty.capture.fieldId}" + "-options").click(function(event) {
		event.preventDefault();
		var checked     = $("#tree-" + "{$smarty.capture.fieldId}" + "-selected").val();
		var name        = "{if $listing_field_info.id}{$listing_field_info.id}{else}{$id}{/if}";
		var id          = "{$sid}";
		var choiceLimit = "{$smarty.capture.choiceLimit}";
		var caption     = "{if $listing_field_info.id}[[$listing_field_info.caption]]{else}[[$caption]]{/if}";
		var userTree    = "{$userTree}";
		
		var url = "{$GLOBALS.admin_site_url}/tree-options/?check=" + checked + "&name=" + name +"&id=" + id + "&choiceLimit=" + choiceLimit + "&userTree=" + userTree;
		popUpWindow(url, 700, 550, caption, NaN, popUpClose{$id});
	});

	function getObject{$id}()
	{
		return new Object({
			"arrayName"      : "{$id}Values",
			"fieldId"        : "{$smarty.capture.fieldId}",
			"trMore"         : "{$smarty.capture.trMore}",
			"availableCount" : "{$smarty.capture.choiceLimit}",
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
