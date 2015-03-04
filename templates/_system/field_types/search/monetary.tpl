{capture name="input_text_field_from"} <input type="text" name="{$id}[monetary][not_less]" class="searchMoney" value="{$value.monetary.not_less}" /> {/capture}
{capture name="input_text_field_to"}   <input type="text" name="{$id}[monetary][not_more]" class="searchMoney" value="{$value.monetary.not_more}" /> {/capture}

{assign var="input_text_field_from" value="`$smarty.capture.input_text_field_from`"}
{assign var="input_text_field_to" value="`$smarty.capture.input_text_field_to`"}

<span class="salary-abbr">[[$input_text_field_from to $input_text_field_to]]</span>
<select name='{$id}[monetary][currency]' id='{$id}_list'>
	<option value="">[[Select]] [[Currency]]</option>
	{foreach from=$list_currency item=list_curr}
		<option value='{$list_curr.currency_code}' {if $list_curr.currency_code == $value.monetary.currency || (!$value.monetary.currency && $list_curr.main==1)}selected="selected"{/if} >{tr mode="raw"}{$list_curr.currency_sign}{/tr}</option>
	{/foreach}
</select>