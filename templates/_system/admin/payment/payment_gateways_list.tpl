{breadcrumbs}[[Payment Gateways]]{/breadcrumbs}
<h1><img src="{image}/icons/paperlock32.png" border="0" alt="" class="titleicon"/>[[Payment Gateways]]</h1>
{if $gatewaySaved}<p class="message">[[Your changes were successfully saved]]</p>{/if}
<table>
	<thead>
		<tr>
			<th>[[Name]]<br><small>[[click to configure]]<small></th>
			<th>[[Status]]</th>
			<th class="actions">[[Actions]]</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$gateways item=gateway key=gateway_id}
			<tr class="{cycle values="oddrow,evenrow"}">
				<td><a href={$GLOBALS.site_url}/configure-gateway/?gateway={$gateway.id} title="set up gateway"> {$gateway.caption} </a></td>
				<td>{if $gateway.active} [[Active]] {else} [[Inactive]] {/if} </td>
				<td>
					{if $gateway.active}
						<a href="?action=deactivate&gateway={$gateway.id}" class="deletebutton">[[Deactivate]]</a>
					{else}
						<a href="?action=activate&gateway={$gateway.id}" class="editbutton">[[Activate]]</a>
					{/if}
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>