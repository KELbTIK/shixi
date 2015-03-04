<div class="clr"><br/><br/></div>
<table>
	<thead>
		<tr>
			<th>[[Module Name]]</th>
			<th>[[Description]]</th>
			<th class="actions">[[Actions]]</th>
		</tr>
	</thead>
	<tbody>
		{assign var="counter" value=0}
		{foreach from=$module_list item="module_info" key="system_module_name"}
			{assign var="counter" value=$counter+1}
			<tr class="{if $counter is odd}oddrow{else}evenrow{/if}">
				<td>{$module_info.display_name}</td>
				<td>{$module_info.description}</td>
				<td align=center><a href="?module_name={$system_module_name}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
			</tr>
		{foreachelse}
			<tr><td colspan=3>[[There are no modules with templates in the system. If you don't have any, your package either comes without module templates or is damaged.]] </td></tr>
		{/foreach}
	</tbody>
</table>