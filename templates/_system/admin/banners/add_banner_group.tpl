{breadcrumbs}<a href="{$GLOBALS.site_url}/manage-banner-groups/">[[Banners]]</a> &#187; [[Add a New Group]]{/breadcrumbs}
<h1><img src="{image}/icons/slide32.png" border="0" alt="" class="titleicon"/>[[Add a New Group]]</h1>
{module name='flash_messages' function='display'}

<fieldset>
	<legend>[[Add a New Group]]</legend>
	<form method="post" enctype="multipart/form-data" onsubmit="disableSubmitButton('submitAdd');">
		<input type="hidden" name="action" value="add" />
		<table>
			<tr>
				<td valign="middle">[[Group ID]]</td>
				<td>
					<input type="text" name="groupID" maxlength="20" />
					<input type="submit" value="[[Add]]" class="grayButton" id="submitAdd" />
				</td>
			</tr>
		</table>
	</form>
</fieldset>