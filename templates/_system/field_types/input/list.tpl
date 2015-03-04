<select class="searchList {if $sort_by_alphabet}sortable-select{/if} {if $complexField}complexField{/if}" name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{elseif $parentID}{$parentID}[{$id}]{else}{$id}{/if}" {if $parentID && !$list_values && !$enabled} disabled="disabled" {/if} {if $parentID && $id == "Country"} onchange = "get{$parentID}States(this.value)" {/if} >
	{if $id !== 'email_frequency'}<option value="">[[Select]] {tr}{$caption}{/tr|escape:'html'}</option>{/if}
	{foreach from=$list_values item=list_value}
		<option value="{$list_value.id}" {if $list_value.id == $value}selected="selected"{/if} >{tr mode="raw"}{$list_value.caption}{/tr|escape:'html'}</option>
	{/foreach}
</select>