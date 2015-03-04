<table>
	<thead>
		<tr>
			<th width="1%">&nbsp;</th>
			<th width="48%">[[Job Title]]</th>
			<th width="25%">[[Company]]</th>
			<th>[[Location]]</th>
			<th width="1%">&nbsp;</th>
		</tr>
	</thead>
	{if $listings}
		{foreach from=$listings item=listing name=listings_block}
			<tr class="{cycle values = 'evenrow,oddrow'}">
				<td>&nbsp;</td>
				<td><a href="{$GLOBALS.site_url}/display-job/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html">{$listing.Title|escape:'html'}</a></td>
				<td>{$listing.user.CompanyName|escape:'html'}</td>
				<td>{locationFormat location=$listing.Location format="short"}</td>
				<td>&nbsp;</td>
			</tr>
		{foreachelse}
			<td>&nbsp;</td>
		{/foreach}
	{else}
		<tr>
			<td colspan="5">[[There are no listings with requested parameters in the system.]]</td>
		</tr>
	{/if}
	<tr>
		<td colspan="5"><br/></td>
	</tr>
</table>