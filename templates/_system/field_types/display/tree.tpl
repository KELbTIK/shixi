{foreach from=$assoc_array item=val name=tree_value key=k}
	{if !empty($val)}
		<b>{tr}{$k}{/tr|escape:'html'}</b>{if $val}:{/if} {foreach from=$val item=child name=child_lv1}{tr}{$child}{/tr|escape:'html'}{if !$smarty.foreach.child_lv1.last}, {/if}{/foreach}{if !$smarty.foreach.tree_value.last}<br />{/if}
	{else}
		{tr}{$k}{/tr|escape:'html'}{if !$smarty.foreach.tree_value.last},{/if}
	{/if}
{/foreach}