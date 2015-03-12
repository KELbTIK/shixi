<div class="row">
	<div class="col-xs-6"><input type="text" value="{$value.value}" class="inputStringMoney form-control {if $complexField}complexField{/if}" name="{if $complexField}{$complexField}[{$id}][{$complexStep}][value]{else}{$id}[value]{/if}" /></div>
	<div class="col-xs-2 col-xs-offset-1"><select name="{if $complexField}{$complexField}[{$id}][{$complexStep}][add_parameter]{else}{$id}[add_parameter]{/if}" class="selectCurrency form-control {if $complexField}complexField{/if}">
		<option value="">[[Select]] [[Currency]]</option>
		{foreach from=$list_currency item=list_curr}
			<option value='{$list_curr.sid}' {if ($list_curr.sid == $value.currency) || (!$value.currency && $list_curr.main==1)}selected="selected"{/if} >{tr mode="raw"}{$list_curr.currency_sign}{/tr}</option>
		{/foreach}
	</select>
	</div>
</div>

