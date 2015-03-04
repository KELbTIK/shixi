<div id="groupsContainer" style="z-index:999;">
	<input type="hidden" name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}" value=""/>
	<select multiple class="inputList {if $sort_by_alphabet}sortable-select{/if} {if $complexField}complexField{/if}" name="{if $complexField}{$complexField}[{$id}][{$complexStep}][]{else}{$id}[]{/if}">
		{foreach from=$allGroups item=list_value}
			<option value="{$list_value.id}" {foreach from=$value item=value_id}{if $list_value.id == $value_id}selected="selected"{/if}{/foreach} >{tr mode="raw"}{$list_value.caption}{/tr|escape:"html"}</option>
		{/foreach}
	</select><br/>
	{if $comment}
		<small>[[{$comment}]].</small>
	{/if}
</div>
<script type="text/javascript">
	function showAvailableCounter(fieldId, counter) {
		$("#count-available-" + fieldId).empty().html(counter +" [[Available]]");
	}

	function postGroupsBehavior() {
		var selectButton = $("#groupsContainer>button.ui-multiselect");
		if ($("#post_to_groups").is(":checked")) {
			selectButton.attr({ disabled: false });
			selectButton.css({ opacity: 1 });
		} else {
			$(".ui-multiselect span").unbind();
			selectButton.attr({ disabled: true });
			selectButton.css({ opacity: 0.3 });
		}
	}

	$(document).ready(function() {
		var limit = {if !empty($choiceLimit)}{$choiceLimit}{else}null{/if};
		var name = "{if $complexField}{$complexField}[{$id}][{$complexStep}][]{else}{$id}[]{/if}";
		var fieldId = "{$id}";
		var options = {
			selectedText: function(numChecked) {
				return numChecked + " {tr}{$caption}{/tr|escape:'html'} [[selected]]";
			},
			noneSelectedText: "{tr}Click to select{/tr|escape:'html'}",
			checkAllText: "{tr}Select all{/tr|escape:'html'}",
			uncheckAllText: "{tr}Deselect all{/tr|escape:'html'}",
			header: true,
			height: 'auto',
			minWidth: 209
		};
		$("select[name='" + name + "']").getCustomMultiList(options, fieldId, limit);
		showAvailableCounter(fieldId, limit - $("select[name='" + name + "'] option:selected").size());

		postGroupsBehavior();

		$("#post_to_groups").click(function(){
			postGroupsBehavior();
		});

	});
</script>