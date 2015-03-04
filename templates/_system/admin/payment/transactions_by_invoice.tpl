<h4>[[Transactions]]</h4>
<table>
	<tr>
		<th>[[Date]]</th>
		<th>[[Transaction Id]]</th>
		<th>[[Payment Method]]</th>
		<th>[[Amount]]</th>
	</tr>
		{foreach from=$transactions item=field}
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td>{$field.date}</td>
			<td>{$field.transaction_id}</td>
			<td>{$field.payment_method}</td>
			<td>{tr type="float"}{$field.amount}{/tr}</td>
		</tr>
		{/foreach}
</table>
