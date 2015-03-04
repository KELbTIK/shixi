<div style="min-height: 26px;">
	<img id="multilist-preloader-{$id}" src="{$GLOBALS.site_url}/templates/_system/main/images/ajax_preloader_circular_32.gif" height="16px" />
	<select multiple="multiple" style="display: none;" id="{$id}" name="{$id}[multi_like][]" {if $sort_by_alphabet}class="sortable-select"{/if}>
		{foreach from=$list_values item=list_value}
			<option value="{$list_value.id}" {foreach from=$value.multi_like item=value_id}{if $list_value.id == $value_id}selected="selected"{/if}{/foreach} >{tr mode="raw"}{$list_value.caption}{/tr|escape:"html"}</option>
		{/foreach}
	</select>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		var name = "{$id}[multi_like][]";
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
		$("select[name='" + name + "']").getCustomMultiList(options, "{$id}", null);
	});
</script>
