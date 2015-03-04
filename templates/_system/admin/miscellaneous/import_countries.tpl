{breadcrumbs}<a href="{$GLOBALS.site_url}/countries/">[[Countries]]</a> &#187; [[Import Countries]]{/breadcrumbs}
<h1><img src="{image}/icons/usersplus32.png" border="0" alt="" class="titleicon" />[[Import Countries]]</h1>

{include file="country_errors.tpl"}

<form method="post"  enctype="multipart/form-data">
	<table width="400">
		<thead>
		 	<tr>
				<th colspan="2">[[Import from Excel File]]</th>
			</tr>
		</thead>
		<tbody>
			<tr class="oddrow">
				<td valign="top">[[Select File]]:</td>
				<td>
					<div><input type="file" name="import_file" value="" /> <small>([[max.]] {$uploadMaxFilesize} M)</small></div>
					<div class="small-text">[[First column should be Country Code, second column should be Country Name]]</div>
					<div class="clr"><br/></div>
					<div class="floatRight"><input type="submit" name="action" value="[[Import]]" class="greenButton" /></div>
				</td>
			</tr>
		</tbody>
	</table>
</form>