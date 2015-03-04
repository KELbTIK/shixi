<div class="clr"><br/></div>
<div class="box" id="displayResults">
	<table width="100%">
		<thead>
			<tr>
				<th class="tableLeft"> </th>
				<th width="10%">
					<a href="?restore=1&amp;sorting_field=sid&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'sid'}DESC{else}ASC{/if}">[[Invoice]]&nbsp;#</a>
					{if $sorting_field == 'sid'}{if $sorting_order == 'DESC'}<img src="{image}b_down_arrow.png" alt="" />{else}<img src="{image}b_up_arrow.png" alt="" />{/if}{/if}
				</th>
				{if $isSubUserExists}
					<th>
						<a href="?restore=1&amp;sorting_field=payer&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'payer'}DESC{else}ASC{/if}">[[Payer]]</a>
						{if $sorting_field == 'payer'}{if $sorting_order == 'DESC'}<img src="{image}b_down_arrow.png" alt="" />{else}<img src="{image}b_up_arrow.png" alt="" />{/if}{/if}
					</th>
				{/if}
				<th width="10%">
					<a href="?restore=1&amp;sorting_field=date&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'date'}DESC{else}ASC{/if}">[[Date]]</a>
					{if $sorting_field == 'date'}{if $sorting_order == 'DESC'}<img src="{image}b_down_arrow.png" alt="" />{else}<img src="{image}b_up_arrow.png" alt="" />{/if}{/if}
				</th>
				<th>
					<a href="?restore=1&amp;sorting_field=payment_method&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'payment_method'}DESC{else}ASC{/if}">[[Payment Method]]</a>
					{if $sorting_field == 'payment_method'}{if $sorting_order == 'DESC'}<img src="{image}b_down_arrow.png" alt="" />{else}<img src="{image}b_up_arrow.png" alt="" />{/if}{/if}
				</th>
				<th>
					<a href="?restore=1&amp;sorting_field=status&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'status'}DESC{else}ASC{/if}">[[Status]]</a>
					{if $sorting_field == 'status'}{if $sorting_order == 'DESC'}<img src="{image}b_down_arrow.png" alt="" />{else}<img src="{image}b_up_arrow.png" alt="" />{/if}{/if}
				</th>
				<th>
					<a href="?restore=1&amp;sorting_field=total&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'total'}DESC{else}ASC{/if}">[[Total]]</a>
					{if $sorting_field == 'total'}{if $sorting_order == 'DESC'}<img src="{image}b_down_arrow.png" alt="" />{else}<img src="{image}b_up_arrow.png" alt="" />{/if}{/if}
				</th>
				<th width="5%" class="invoice-actions">[[Actions]]</th>
				<th class="tableRight"> </th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$found_invoices item=invoice name=invoices_block}
			<tr class="{cycle values="oddrow,evenrow"}">
				<td> </td>
				<td>&nbsp;<a href="{$GLOBALS.site_url}/print-invoice/?sid={$invoice.sid}&action=print" target="_blank">{display property='sid' object_sid=$invoice.sid}</a></td>
				{if $isSubUserExists}
					<td>
						{display property='subuser_sid' object_sid=$invoice.sid assign="subuser"}
						{if $subUser}
							<a href="{$GLOBALS.site_url}/sub-accounts/edit/?user_id={$subUser}"><span class="longtext-25">{display property='payer' object_sid=$invoice.sid}</span></a>
						{else}
							<a href="{$GLOBALS.site_url}/edit-profile/?user_id={display property='user_sid' object_sid=$invoice.sid}"><span class="longtext-25">{display property='payer' object_sid=$invoice.sid}</span></a>
						{/if}
					</td>
				{/if}
				<td>{display property='date' object_sid=$invoice.sid}</td>
				<td>{display property='payment_method' object_sid=$invoice.sid assign = payment_method}{$payment_method}</td>
				<td>{display property='status' object_sid=$invoice.sid}</td>
				<td>
					{capture assign="totalAmount"}{display property='total' object_sid=$invoice.sid}{/capture}
					{currencyFormat amount=$totalAmount}
				</td>
				<td class="align_right" nowrap="nowrap">
					{if $invoice.status != 'Paid' && $invoice.status != 'Pending'}
						<a href="{$GLOBALS.site_url}/payment-page/?invoice_sid={$invoice.sid}">[[Pay Invoice]]</a>
						&nbsp;
					{/if}
					<a href="{$GLOBALS.site_url}/print-invoice/?sid={$invoice.sid}&action=print" target="_blank">[[View Invoice]]</a>
				</td>
				<td> </td>
			</tr>
			{foreachelse}
				<tr>
					<td {if $isSubUserExists}colspan="10"{else}colspan="9"{/if}><br/><div class="text-center">[[You do not have any invoices so far]]</div><br/></td>
				</tr>
			{/foreach}
		</tbody>
	</table>
</div>