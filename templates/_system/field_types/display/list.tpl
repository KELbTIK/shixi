{foreach from=$list_values item=list_value}
	{if $list_value.id == $value}
		{if $displayAS}
			{tr}{$list_value.$displayAS}{/tr|escape:'html'}
		{else}
			{tr}{$list_value.caption}{/tr|escape:'html'}
		{/if}
	{/if}
{/foreach}
