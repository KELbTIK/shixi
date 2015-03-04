<div {if $sort_by_alphabet}class="sortable-input"{/if}>
	{foreach from=$list_values item=list_value}
		<input type="radio" name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}" value="{$list_value.id}" {if $list_value.id == $value || (!$value && $list_value.id == 0)}checked="checked"{/if} /><span>&nbsp;{tr}{$list_value.caption}{/tr|escape:'html'}</span><br />
	{/foreach}
</div>