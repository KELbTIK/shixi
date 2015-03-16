<script  type="text/javascript" src="{common_js}/pagination.js"></script>
<div class="clr"><br/></div>
<form method="post" name="invoices_form" action="{$GLOBALS.site_url}/manage-invoices/">
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
					{foreach from=$found_invoices item=invoice name=invoices_block}
					<tr class="{cycle values="oddrow,evenrow"}">
						<td><input type="checkbox" name="invoices[{$invoice.sid}]" value="1" id="checkbox_{$smarty.foreach.invoices_block.iteration}" /></td>
						<td><a href="{$GLOBALS.site_url}/edit-invoice/?sid={$invoice.sid}">{display property='sid' object_sid=$invoice.sid}</a></td>
						<td>
							{display property='subuser_sid' object_sid=$invoice.sid assign=subuser}
							{if $invoice.userExists}
								{if $subuser}
									<a href="{$GLOBALS.site_url}/edit-user/?user_sid={$subuser}">{display property='username' object_sid=$invoice.sid}</a>
								{else}
									<a href="{$GLOBALS.site_url}/edit-user/?user_sid={display property='user_sid' object_sid=$invoice.sid}">{display property='username' object_sid=$invoice.sid}</a>
								{/if}
							{else}
								<span class="invoice-washy">[[User deleted]]</span>
							{/if}
						</td>
						<td>{display property='date' object_sid=$invoice.sid}</td>
						<td>{display property='payment_method' object_sid=$invoice.sid}</td>
						<td>
							{capture assign="invoiceTotal"}{display property='total' object_sid=$invoice.sid}{/capture}
							{currencyFormat amount=$invoiceTotal}
						</td>
						<td>{display property='status' object_sid=$invoice.sid}</td>
						<td>
						{if $invoice.status != 'Paid'}
							<input type="button" name="action" value="[[Mark Paid]]" class="editbutton"  onclick="$('#checkbox_{$smarty.foreach.invoices_block.iteration}').attr('checked', 'checked'); submitForm('paid');" style="text-align: center;"/>
						{else}
							<input type="button" name="action" value="[[Mark Unpaid]]" class="editbutton" onclick="$('#checkbox_{$smarty.foreach.invoices_block.iteration}').attr('checked', 'checked'); submitForm('unpaid');" style="text-align: center;"/>
						{/if}
						</td>
						{capture name=trTextToDelete}[[Are you sure you want to delete this invoice?]]{/capture}
						<td style="border-left: 0px; "><input type="button" name="action" value="[[Delete]]" class="deletebutton" onclick="if (confirm('{$smarty.capture.trTextToDelete|escape}')) $('#checkbox_{$smarty.foreach.invoices_block.iteration}').attr('checked', 'checked'); submitForm('delete');" /></td>
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
