{breadcrumbs}[[Banners]]{/breadcrumbs}
<h1><img src="{image}/icons/slide32.png" border="0" alt="" class="titleicon"/>[[Banners Groups]]</h1>

{foreach from=$errors item=error}
	[[{$error}]]
{/foreach} 
<p><a href="{$GLOBALS.site_url}/add-banner-group/" class="grayButton">[[Add a new group]]</a></p>

<table>
	<thead>
		<tr>
			<th>[[Group ID]]</th>
			<th colspan="2" class="actions">[[Actions]]</th>
		</tr>
	</thead>
	{foreach from=$bannerGroups item=group}
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td><a href="{$GLOBALS.site_url}/edit-banner-group/?groupSID={$group.sid|escape}" title="[[Edit]]">{$group.id}</a></td>
			<td><a href="{$GLOBALS.site_url}/edit-banner-group/?groupSID={$group.sid|escape}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
			<td>
				{capture name="delete_confirm_script"} return confirm('[[Do you want to delete]] \'{$group.id|escape:"javascript"}\' [[banner group]]? \n ([[All banners in group will be deleted]])') {/capture}
				<a href="?action=delete_banner_group&amp;groupSID={$group.sid|escape}" onclick="{$smarty.capture.delete_confirm_script|escape:"html"}" title="[[Delete]]" class="deletebutton">[[Delete]]</a>
			</td>
		</tr>
	{/foreach}
</table>