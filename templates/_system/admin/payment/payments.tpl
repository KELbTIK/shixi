<script language="JavaScript" type="text/javascript" src="{common_js}/pagination.js"></script>
{include file="errors.tpl"}
<div class="clr"><br/></div>
<form method="get" name="transactions_form" action="{$GLOBALS.site_url}/payments/">
	<input type="hidden" name="action_name" id="action_name" value="" />
	<div class="box" id="displayResults">
		<div class="box-header">
			{include file="../pagination/pagination.tpl" layout="header"}
		</div>
		<div class="innerpadding">
			<div id="displayResultsTable">
				<table width="100%">
					<thead>
						{include file="../pagination/sort.tpl"}
					</thead>
					<tbody>
					{foreach from=$found_transactions_sids item=trans_sid name=transactions_block}
					<tr class="{cycle values="oddrow,evenrow"}">
						<td><input type="checkbox" name="transactions[{$trans_sid}]" value="1" id="checkbox_{$smarty.foreach.transactions_block.iteration}" /></td>
						<td>{display property='date' object_sid=$trans_sid}</td>
						<td>{display property='transaction_id' object_sid=$trans_sid}</td>
						<td>
							<a href="{$GLOBALS.site_url}/edit-user/?user_sid={display property='user_sid' object_sid=$trans_sid}">{display property='username' object_sid=$trans_sid}</a>
						</td>
						{display property='invoice_sid' object_sid=$trans_sid assign=invoice_sid}
						<td>[[Payment for Invoice #]]&nbsp;<a href="{$GLOBALS.site_url}/edit-invoice/?sid={$invoice_sid}">{$invoice_sid}</a></td>
						<td>{display property='payment_method' object_sid=$trans_sid}</td>
						<td>
							{capture assign="paymentAmount"}{display property='amount' object_sid=$trans_sid}{/capture}
							{currencyFormat amount=$paymentAmount}
						</td>
					</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
		</div>
		<div class="box-footer">
			{include file="../pagination/pagination.tpl" layout="footer"}
		</div>
	</div>
</form>
