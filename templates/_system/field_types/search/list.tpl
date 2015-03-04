{if $parentID}
	{assign var="locationName" value=$id|replace:$parentID:''}
	{assign var="locationName" value=$locationName|replace:'_':''}
{/if}
<select name='{$id}[multi_like][]' class="searchList {if $sort_by_alphabet}sortable-select{/if}" {if $parentID && !$list_values && !$enabled} disabled="disabled" {/if} {if $parentID && $locationName == "Country"} onchange = "get{$parentID}States(this.value)" {/if}>
	{if $id != 'email_frequency'}
		<option value="">[[Any]] {tr}{$caption}{/tr|escape:'html'}</option>
	{/if}
	{foreach from=$list_values item=list_value}
		<option value='{$list_value.id}' {foreach from=$value.multi_like item=value_id}{if $list_value.id == $value_id}selected="selected"{/if}{/foreach}{foreach from=$value.multi_like_and item=value_id}{if $list_value.id == $value_id}selected="selected"{/if}{/foreach} >{tr mode="raw"}{$list_value.caption}{/tr|escape:'html'}</option>
	{/foreach}
</select>