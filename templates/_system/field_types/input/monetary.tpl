<input type="text" value="{$value.value}" class="inputStringMoney {if $complexField}complexField{/if}" name="{if $complexField}{$complexField}[{$id}][{$complexStep}][value]{else}{$id}[value]{/if}" />
<select name="{if $complexField}{$complexField}[{$id}][{$complexStep}][add_parameter]{else}{$id}[add_parameter]{/if}" class="selectCurrency {if $complexField}complexField{/if}">
	<option value="">[[Select]] [[Currency]]</option>
	{foreach from=$list_currency item=list_curr}
		<option value='{$list_curr.sid}' {if ($list_curr.sid == $value.currency) || (!$value.currency && $list_curr.main==1)}selected="selected"{/if} >{tr mode="raw"}{$list_curr.currency_sign}{/tr}</option>
	{/foreach}
</select>