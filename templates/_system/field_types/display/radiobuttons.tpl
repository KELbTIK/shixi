{foreach from=$list_values item=list_value}
	{if $list_value.id == $value}
		{tr}{$list_value.caption}{/tr|escape:"html"}
	{/if}
{/foreach}