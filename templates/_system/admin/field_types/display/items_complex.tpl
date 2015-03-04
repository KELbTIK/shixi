{assign var="complexField" value=$id scope=global} {* nwy: Если не очистить переменную то в последующих полях начинаются проблемы (некоторые воспринимаются как комплексные)*}
<table id="invoice-table">
	<tbody>
	<tr class="invoice-table-head">
		<td width="80%" class="invoice-table-head-2">[[Description]]</td>
		<td width="20%" class="align_right">[[Amount]]</td>
	</tr>
	{foreach from=$complexElements key="complexElementKey" item="complexElementItem"}
	<tr>
		<td>
			{display property='qty' complexParent=$complexField complexStep=$complexElementKey assign="qty"}
			{display property='custom_item' complexParent=$complexField complexStep=$complexElementKey assign="custom_item"}
			{assign var="qty" value=$qty scope=parent}
			{assign var="custom_item" value=$custom_item scope=parent}
			{display property='products' complexParent=$complexField complexStep=$complexElementKey template="items_list.tpl"}
		</td>
		<td class="align_right">
			{capture assign="complexAmount"}{display property='amount' complexParent=$complexField complexStep=$complexElementKey}{/capture}
			{currencyFormat amount=$complexAmount}
		</td>
	</tr>
	{/foreach}
	<tr class="invoice-amount-total">
		<td>[[Sub Total]]</td>
		<td>
			{capture assign="complexSubTotal"}{display property="sub_total"}{/capture}
			{currencyFormat amount=$complexSubTotal}
		</td>
	</tr>
	{if $include_tax}
	<tr class="invoice-amount-total">
		<td>{$tax.tax_name}</td>
		<td>
			{capture assign="taxAmount"}{tr type="float"}{$tax.tax_amount}{/tr}{/capture}
			{currencyFormat amount=$taxAmount}
		</td>
	</tr>
	{/if}
	<tr class="invoice-amount-total">
		<td class="test">[[Total]]</td>
		<td>
			{capture assign="invoiceAmountTotal"}{display property="total"}{/capture}
			{currencyFormat amount=$invoiceAmountTotal}
		</td>
	</tr>
	</tbody>
</table>
{assign var="complexField" value=false scope=global} {* nwy: Если не очистить переменную то в последующих полях начинаются проблемы (некоторые воспринимаются как комплексные)*}