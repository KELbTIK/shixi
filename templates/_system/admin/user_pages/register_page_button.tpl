<form action='{$GLOBALS.site_url}/user-pages/' method=POST>
	<input type='hidden' name='module' value={$pageInfo.module}>
	<input type='hidden' name='function' value={$pageInfo.function}>
	<input type='hidden' name='access_type' value='user'>
	<input type='hidden' name='action' value='new_page'>
	<input type='hidden' name='parameters' value="{$pageInfo.parameters}">
	<table class='table'>
		<tr>
			<td><span class="greenButtonEnd"><input type="submit" value="[[Create a Page for this {$caption}]]" class="greenButton"></span></td>
		</tr>
	</table>
</form>