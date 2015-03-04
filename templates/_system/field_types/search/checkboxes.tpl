<div {if $sort_by_alphabet}class="sortable-input"{/if}>
	{foreach from=$list_values item=list_value}
		<input type="checkbox" value="{$list_value.id}" name="{$id}[multi_like][]"/><span>&nbsp;{tr}{$list_value.caption}{/tr|escape:'html'}</span><br/>
	{/foreach}
</div>