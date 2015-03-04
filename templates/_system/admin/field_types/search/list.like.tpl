<select name='{$id}[like]'>
	{if $id != 'data_source'}
		<option value="">[[Any]] [[{$caption|escape:'html'}]]</option>
	{elseif $id == 'data_source'}
		<option value="">[[Select]] {tr}{$caption}{/tr|escape:'html'}</option>
	{/if}
	{foreach from=$list_values item=list_value}
		<option value='{$list_value.id}' {if $list_value.id == $value.like}selected="selected"{/if} >[[{$list_value.caption|escape:'html'}]]</option>
	{/foreach}
</select>