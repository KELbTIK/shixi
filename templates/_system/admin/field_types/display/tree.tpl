{foreach from=$assoc_display item=value name=tree_value key=k}
	{$k|escape:'html'} : {$value|escape:'html'}<br>
{/foreach}