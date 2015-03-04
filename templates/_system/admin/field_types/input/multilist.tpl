<input type="hidden" name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}" value=""/>
<div style="min-height: 26px;">
	<img id="multilist-preloader-{$id}" src="{$GLOBALS.site_url}/templates/_system/main/images/ajax_preloader_circular_32.gif" height="16px" />
	<select multiple="multiple" style="display: none;" class="inputList {if $sort_by_alphabet}sortable-select{/if} {if $complexField}complexField{/if}" name="{if $complexField}{$complexField}[{$id}][{$complexStep}][]{else}{$id}[]{/if}">
		{foreach from=$list_values item=list_value}
			<option value="{$list_value.id}" {foreach from=$value item=value_id}{if $list_value.id == $value_id}selected="selected"{/if}{/foreach} >{tr mode="raw"}{$list_value.caption}{/tr|escape:"html"}</option>
		{/foreach}
	</select>
	<br/>
</div>
{if $comment}
	<small>[[{$comment}]].</small>
{/if}
<script type="text/javascript">
	function showAvailableCounter(fieldId, counter) {
		$("#count-available-" + fieldId).empty().html(counter +" [[Available]]");
	}

	$(document).ready(function() {
		var limit = {if !empty($choiceLimit)}{$choiceLimit}{else}null{/if};
		var name = "{if $complexField}{$complexField}[{$id}][{$complexStep}][]{else}{$id}[]{/if}";
		var fieldId = "{$id}";
		var options = {
			selectedList: 3,
			selectedText: "# {tr}selected{/tr|escape:'html'}",
			noneSelectedText: "{tr}Click to select{/tr|escape:'html'}",
			checkAllText: "{tr}Select all{/tr|escape:'html'}",
			uncheckAllText: "{tr}Deselect all{/tr|escape:'html'}",
			header: true,
			height: 'auto',
			minWidth: 209
		};
		$("select[name='" + name + "']").getCustomMultiList(options, fieldId, limit);
		showAvailableCounter(fieldId, limit - $("select[name='" + name + "'] option:selected").size());
	});
</script>
