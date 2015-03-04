{breadcrumbs}<a href="{$GLOBALS.site_url}/manage-breadcrumbs/">[[Manage Breadcrumbs]]</a> &#187; [[Edit Breadcrumb Item]]{/breadcrumbs}
<h1><img src="{image}/icons/glitter32.png" border="0" alt="" class="titleicon"/>[[Edit '$editElement.name' item]]</h1>

<form method="post">
	<input type="hidden" name="action" value="edit">
	<input type="hidden" name="element_id" value="{$editElement.id}">
	<table>
		<tr>
			<td>[[Name]]</td>
			<td><input type="text" name="item_name" value="{$editElement.name}" /></td>
		</tr>
		<tr>
			<td>[[URI]]</td>
			<td><input type="text" name="item_uri" value="{$editElement.uri}" /></td>
		</tr>
		<tr>
			<td colspan="2">
				<div class="floatRight"><input type="submit" name="updateElement" value="[[Save]]" class="grayButton" /></div>
			</td>
		</tr>
	</table>
</form>