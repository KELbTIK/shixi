{breadcrumbs}[[Import Language]]{/breadcrumbs}
<h1><img src="{image}/icons/boxdownload32.png" border="0" alt="" class="titleicon"/>[[Import Language]]</h1>

{if $errors}
	{include file="errors.tpl" errors=$errors}
{/if}

<form action="" method="post" enctype="multipart/form-data">
	<table>
		<tr class="evenrow">
			<td>[[Language Import File]]</td>
			<td><input type="file" name="lang_file" /> <small>([[max.]] {$uploadMaxFilesize} M)</small></td>
		</tr>
		<tr id="clearTable">
			<td colspan="2">
				<div class="floatRight">
                    <input type="hidden" name="action" value="import_language" />
                    <input type="submit" class="grayButton" value="[[Import]]" />
                </div>
			</td>
		</tr>
	</table>
</form>