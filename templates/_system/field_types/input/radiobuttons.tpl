<div {if $sort_by_alphabet}class="sortable-input"{/if}>
	{foreach from=$list_values item=list_value}
		<input type="radio" name="{$id}" {if $list_value.id == $value}checked="checked"{/if} value="{$list_value.id}" /><span>&nbsp;{tr}{$list_value.caption}{/tr|escape:'html'}</span><br/>
	{/foreach}
</div>