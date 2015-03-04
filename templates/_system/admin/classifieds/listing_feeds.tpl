{breadcrumbs}[[XML Feeds]]{/breadcrumbs}
<h1><img src="{image}/icons/rss32.png" border="0" alt="" class="titleicon" />[[XML Feeds]]</h1>
<p>[[XML feeds allow you to export job postings from your site to other sites like Indeed.com or SimpyHired.com.<br/>Below you can find a list of feeds currently available in the system and tools to create your own feeds.]]</p>
{include file="field_errors.tpl"}
{foreach from=$errors item=error}
	<p class="error">[[{$error.message}]]</p>
{/foreach}
<p><a href="{$GLOBALS.site_url}/listing-feeds/?action=add" class="grayButton">[[Add a New Feed]]</a></p>
<div class="clr"><br/></div>
<table>
	<thead>
		<tr>
			<th>[[ID]]</th>
			<th>[[Name]]</th>
			<th>[[Template]]</th>
			<th>[[Listings Type]]</th>
			<th>[[Listings Limit]]</th>
			<th>[[Description]]</th>
			<th>[[Link]]</th>
			<th colspan="2" class="actions">[[Actions]]</th>
		</tr>
	</thead>
	<tbody>
		{assign var="counter" value=0}
		{foreach from=$feeds item=feed}
		{assign var="counter" value=$counter+1}
		<tr class="{if $counter is odd}oddrow{else}evenrow{/if}">
			<td>{$feed.sid}</td>
			<td>[[{$feed.name}]]</td>
			<td nowrap="nowrap"><a href="{$GLOBALS.site_url}/edit-templates/?module_name=classifieds&amp;template_name={$feed.template}" title="[[Edit Template]]">{$feed.template}</a></td>
			<td>[[{$feed.type}]]</td>
			<td>{$feed.count_listings}</td>
			<td>[[{$feed.description}]]</td>
			<td nowrap="nowrap"><a href="{$siteURL}/listing-feeds/?feedId={$feed.sid}" target="_blank" title="[[Link to this XML feed]]">{$siteURL}/listing-feeds/?feedId={$feed.sid}</a></td>
			<td><a href="{$GLOBALS.site_url}/listing-feeds/?action=edit&amp;feedId={$feed.sid}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
			<td><a href="{$GLOBALS.site_url}/listing-feeds/?action=delete&amp;feedId={$feed.sid}" onclick="return confirm('[[Are you sure you want to delete the \'$feed.name\' item?]]')" title="[[Delete]]" class="deletebutton">[[Delete]]</a></td>
		</tr>
	</tbody>
{/foreach}
</table>