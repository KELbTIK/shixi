<select class="searchList {if $complexField}complexField{/if}" name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}" {if $product_type && $id == 'user_group_sid'} onChange="changePermissions(this.value)" {/if}>
{foreach from=$list_values item=list_value}
	<option value="{$list_value.id|escape:'html'}" {if $list_value.id == $value}selected="selected"{/if} >{tr}{$list_value.caption}{/tr|escape:'html'}</option>
{/foreach}
</select>