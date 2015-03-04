{breadcrumbs}[[Breadcrumbs]]{/breadcrumbs}
<h1><img src="{image}/icons/glitter32.png" border="0" alt="" class="titleicon"/>[[Breadcrumbs]]</h1>

{foreach from=$ERRORS item="error_message" key="error"}
	{if $error eq "NOT_ID"}
		<p class="error">[[Not set 'element_id'!]]</p>
	{/if}
{/foreach}


<table>
	<thead>
		<tr>
			<th>[[Item Name]]</th>
			<th class="actions">[[Actions]]</th>
		</tr>
	</thead>
	<tr>
		<td><strong style="color: #B9281F">/</strong></td>
		<td><a href="{$GLOBALS.site_url}/manage-breadcrumbs/?action=add&element_id=0" class="grayButton">[[Add Child]]</a></td>
	</tr>
	{assign var="counter" value=0}
	{foreach from=$navStructure item=navItem name=navForeach}
		{assign var="counter" value=$counter+1}
		{if $navItem.sublevel eq '0'}
		<tr class="{if $counter is odd}oddrow{else}evenrow{/if}">
			<td>&nbsp;&nbsp;&nbsp;<strong style="color: #B9281F">[[{$navItem.name}]]</strong></td>
		{else}
		<tr class="{if $counter is odd}oddrow{else}evenrow{/if}">
			<td>&nbsp;&nbsp;&nbsp;{section name=for loop=$navItem.sublevel start=0 step=1}&nbsp;&nbsp;&nbsp;{/section}<strong>[[{$navItem.name}]]</strong></td>
		{/if}
			<td>
				<a href="{$GLOBALS.site_url}/manage-breadcrumbs/?action=add&element_id={$navItem.id}" class="grayButton">[[Add Child]]</a>
				<a href="{$GLOBALS.site_url}/manage-breadcrumbs/?action=edit&element_id={$navItem.id}" class="editbutton">[[Edit]]</a>
				<a href="{$GLOBALS.site_url}/manage-breadcrumbs/?action=delete&element_id={$navItem.id}" onclick="return confirm('[[Are you sure you want to delete the]] \'{$navItem.name}\' [[item and all child items of it?]]')" title="[[Delete]]" class="deletebutton">[[Delete]]</a>
			</td>
		</tr>
	{/foreach}
</table>