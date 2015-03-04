{strip}
{if is_numeric($value.value)}
	{foreach from=$list_currency item="list_currency_item" key="list_currency_key"}
		{if $list_currency_item.sid == $value.currency}
			{capture assign="currentCurrencySign"}{$list_currency_item.currency_sign}{/capture}
		{/if}
	{/foreach}
	{capture assign="amount"}{tr type="float"}{$value.value}{/tr}{/capture}
	{currencyFormat amount=$amount sign=$currentCurrencySign}
{else}
	{$value.value}
{/if}
{/strip}