{if $ERROR eq "EMPTY_TEMPLATE_NAME"}
	<p class="error">[[Please enter non empty name]]</p>
{elseif $ERROR eq "WRONG_FILENAME"}
	<p class="error">[[Only letters are acceptable!]]</p>
{/if}

<form>
	<fieldset>
		<legend>[[Create Template]]</legend>
		<input type="hidden" name="action" value="create_page_template">
		<table>
			<tr>
				<td>[[New Template Name]]</td>
				<td><input type="text" name="new_template_name" value="{$new_template_name}"></td>
				<td><span class="greenButtonEnd"><input type="submit" value="[[Create]]" class="greenButton" /></span></td>
			</tr>
		</table>
	</fieldset>
</form>

<table>
	<thad>
		<tr>
			<th>[[Template Name]]</th>
			<th align="center" class="actions">[[Actions]]</th>
		</tr>
	</thad>
	<tbody>
		{assign var="counter" value=0}
		{foreach from=$template_list item="template_info" key="system_template_name"}
			{assign var="counter" value=$counter+1}
			<tr class="{if $counter is odd}oddrow{else}evenrow{/if}">
				<td>{$template_info.name}</td>
				<td align="center"> <a href="?template_name={$system_template_name}" title="[[Edit]]"><img src="{image}edit.gif" hspace="5" border=0 alt="[[Edit]]"></a><a href="?action=delete_template&del_template_name={$system_template_name}" title="[[Delete]]"><img src="{image}delete.gif" hspace="5" border=0 alt="[[Delete]]" onclick="return confirm('[[Are you sure you want to delete]] \'{$template_info.name}\' [[template?]]')"></a></td>
			</tr>
		{foreachelse}
			<tr><td colspan="2">[[No templates]]</td></tr>
		{/foreach}
	</tbody>
</table>
