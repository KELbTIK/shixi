<div class="row">
	<div class="col-xs-6"><input value="{$value.value}" class="inputStringMoney form-control {if $complexField}complexField{/if}" name="{if $complexField}{$complexField}[{$id}][{$complexStep}][value]{else}{$id}[value]{/if}" type="number" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" title="This should be a number with up to 2 decimal places." /></div>
	<div class="col-xs-2 col-xs-offset-1"><select name="{if $complexField}{$complexField}[{$id}][{$complexStep}][add_parameter]{else}{$id}[add_parameter]{/if}" class="selectCurrency form-control {if $complexField}complexField{/if}">
			<option value="">[[Select]] [[Currency]]</option>
			{foreach from=$list_currency item=list_curr}
				<option value='{$list_curr.sid}' {if ($list_curr.sid == $value.currency) || (!$value.currency && $list_curr.main==1)}selected="selected"{/if} >{tr mode="raw"}{$list_curr.currency_sign}{/tr}</option>
			{/foreach}
		</select>
	</div>
</div>

