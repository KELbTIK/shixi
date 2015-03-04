<select name='{$id}[equal]'>
	{foreach from=$list_values item=list_value}
		<option value='{$list_value.id}' {if $list_value.id == $value.equal}selected="selected"{/if} >[[{$list_value.caption|escape:"html"}]]</option>
	{/foreach}
</select>