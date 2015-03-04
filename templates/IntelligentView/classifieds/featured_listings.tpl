<table cellpadding="5" class="indexResultsTable">
	<tr>
		<th width="48%"><span class="strong">[[Job Title]]</span></th>
		<th width="25%">[[Company]]</th>
		<th>[[Location]]</th>
	</tr>
	{if $listings}
		{foreach from=$listings item=listing name=listings_block}
			<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
				<td style="padding-left: 5px;"><a href="{$GLOBALS.site_url}/display-job/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html">{$listing.Title|escape:'html'}</a></td>
				<td>{$listing.user.CompanyName|escape:'html'}</td>
				<td>{locationFormat location=$listing.Location format="short"}</td>
			</tr>
		{foreachelse}
			<td>&nbsp;</td>
		{/foreach}
	{else}
		<tr>
				<td colspan="3">[[There are no listings with requested parameters in the system.]]</td>
		</tr>
	{/if}
</table>