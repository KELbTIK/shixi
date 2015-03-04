<div class="clr"><br/></div>
<div class="box" id="displayResults">
	<div class="box-header">
		{include file="../pagination/pagination.tpl" layout="header"}
	</div>
	<div class="innerpadding">
		<div id="displayResultsTable">
			<table width=100%>
				<thead>
					{include file="../pagination/sort.tpl"}
				</thead>
			{foreach from=$found_payments item=found_payment name=payments_block}
				<tr id="users[{$found_user.sid}]" class="{cycle values = 'evenrow,oddrow'}">
					<td width=10%>{tr type="date"}{$found_payment.date}{/tr}</td>
					<td width=16%>{$found_payment.gateway}</td>
					<td><a href="{$GLOBALS.site_url}/payment-log/display-message/"
					       onClick="popUpWindow('{$GLOBALS.site_url}/payment-log/?action=display_message&sid={$found_payment.sid}',420, 450, '[[Viewing Gateway Response]]'); return false;">
						<pre>{$found_payment.message|truncate:100}</pre>
					</a></td>
					<td width=11%>[[{$found_payment.status}]]</td>
				</tr>
			{/foreach}
			</table>
		</div>
	</div>
	<div class="box-footer">
		{include file="../pagination/pagination.tpl" layout="footer"}
	</div>
</div>