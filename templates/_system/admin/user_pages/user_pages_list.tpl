{breadcrumbs}[[Site Pages]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaper32.png" border="0" alt="" class="titleicon"/>[[Site Pages]]</h1>
<p><a href="{$GLOBALS.site_url}/user-pages/?action=new_page" class="grayButton">[[Add a New User Page]]</a></p>

<table>
	<thead>
		<tr>
			<th>
				<a href="?restore=1&amp;sorting_field=uri&amp;sorting_order={if $sort_pages.sorting_order == 'ASC' && $sort_pages.sorting_field == 'uri'}DESC{else}ASC{/if}">[[URI]]</a>
					{if $sort_pages.sorting_field == 'id'}
						{if $sort_pages.sorting_order == 'ASC'}
							<img src="{image}b_up_arrow.gif" alt="Up" />
						{else}
							<img src="{image}b_down_arrow.gif" alt="Down" />
						{/if}
					{/if}
			</th>
			<th>[[Title]]</th>
			<th>[[Module]]</th>
			<th>[[Function]]</th>
			<th colspan="2" class="actions">[[Actions]]</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$pages_list item=page name="foreach"}
			{if not in_array($page.uri, $inactivePages)}
				<tr class="{if $smarty.foreach.foreach.iteration is even}evenrow{else}oddrow{/if}">
					<td><a href="{$GLOBALS.site_url}/..{$page.uri}" target="_blank">{$page.uri}</a></td>
					<td>[[{$page.title}]]</td>
					<td>{$page.module}</td>
					<td>{$page.function}</td>
					<td><a href="?action=edit_page&amp;uri={$page.uri|escape:'url'}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
					<td><a href="?action=delete_page&amp;uri={$page.uri|escape:'url'}" onclick="return confirm('[[Are you sure you want to delete this page?]]')" title="[[Delete]]" class="deletebutton">[[Delete]]</a></td>
				</tr>
			{/if}
		{/foreach}
	</tbody>
</table>
