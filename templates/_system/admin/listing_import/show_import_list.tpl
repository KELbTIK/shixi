{breadcrumbs}[[XML Import]]{/breadcrumbs}
<h1><img src="{image}/icons/boxdownload32.png" border="0" alt="" class="titleicon" /> [[XML Import]]</h1>
<p>[[XML import allows you to import job postings from other data sources]]</p>
<p><a href="{$GLOBALS.site_url}/add-import/?add_level=1" class="grayButton">[[Add new data source]]</a></p>

{assign var="counter" value=0}

<p><strong>[[XML Data Sources]]</strong></p>

<table>
	<thead>
		<tr>
			<th>[[ID]]</th>
			<th>[[Name]]</th>
			<th>[[Description]]</th>
			<th nowrap="nowrap" class="actions">[[Actions]]</th>
			<th>[[Status]]</th>
		</tr>
	</thead>
	{foreach from=$systemParsers item=curr}
		{if $curr.active == 0}
			{assign var="stat" value="off"}
			{assign var="action" value="activate"}
			{assign var="title" value="[[Not active. Click to activate]]"}
		{else}
			{assign var="stat" value="on"}
			{assign var="action" value="deactivate"}
			{assign var="title" value="[[Active. Click to deactivate]]"}
		{/if}
		{assign var="counter" value=$counter+1}
		<tr class="{if $counter is odd}oddrow{else}evenrow{/if}">
			<td>{$curr.id}</td>
			<td>{$curr.name}</td>
			<td>[[{$curr.description}]]</td>
			<td nowrap="nowrap"><a href="{$GLOBALS.site_url}/run-import/?id={$curr.id}" class="editbutton greenbtn">[[Run]]</a> <a href="{$GLOBALS.site_url}/edit-import/?id={$curr.id}" class="editbutton">[[Edit]]</a> <a href="{$GLOBALS.site_url}/delete-import/?id={$curr.id}" onclick="return confirm('[[Are you sure?]]');" class="deletebutton">[[Delete]]</a></td>
			<td><center><a href="?action={$action}&id={$curr.id}"><img title="{$title}" border=0 src="{image}{$stat}.gif"></a></center></td>
		</tr>
	{/foreach}
</table>