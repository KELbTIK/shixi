<div {if $sort_by_alphabet}class="sortable-input"{/if}>
	<input type="hidden" name="{$id}" value=""/>
	{foreach from=$list_values item=list_value}
		<input class="checkBox{$id}" type="checkbox" name="{$id}[]" {foreach from=$value item=value_id}{if $list_value.id == $value_id}checked="checked"{/if}{/foreach} value="{$list_value.id}" /><span>&nbsp;{tr}{$list_value.caption}{/tr|escape:'html'}</span><br/>
	{/foreach}
</div>
{if $comment}
	<span class="small">[[{$comment}]].</span>
{/if}
<script type="text/javascript">
	function limitShowAvailableCounter{$id}(limit) {
		var selCount = $(".checkBox{$id}:checked").length;
		$("#count-available-{$id}").empty().html(limit - selCount+" [[Available]]");
	}
	$(document).ready(function() {
		var limit{$id} = {if !empty($choiceLimit)}{$choiceLimit}{else}null{/if};
		if (limit{$id}) {
				$(".checkBox{$id}").bind("change", function() {
					limitSelectionsForCheckboxTypes(limit{$id});
					}
				);
			}
		}
	);

	function limitSelectionsForCheckboxTypes(limit) {
		var counter = 0;
		$(".checkBox{$id}:checked").each(function() {
				counter++;
				$(this).attr("checked", limit >= counter);
			}
		);
		limitShowAvailableCounter{$id}(limit);
	}
</script>