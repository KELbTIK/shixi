{breadcrumbs}<a href="{$GLOBALS.site_url}/states/?country_sid={$country_sid}">[[States/Regions]]</a> &#187; [[Import States/Regions]]{/breadcrumbs}
<h1><img src="{image}/icons/usersplus32.png" border="0" alt="" class="titleicon" />[[Import States/Regions]]</h1>

{include file="state_errors.tpl"}

<form method="post"  enctype="multipart/form-data">
	<input type="hidden" name="country_sid"  value="{$country_sid}" />
	<table width="400">
		<thead>
		 	<tr>
				<th colspan="2">[[Import from Excel File]]</th>
			</tr>
		</thead>
		<tbody>
			<tr class="oddrow">
				<td valign="top">[[Select File]]</td>
				<td>
					<div><input type="file" name="import_file" value="" /> <small>([[max.]] {$uploadMaxFilesize} M)</small></div>
					<div class="commentSmall">[[First column should be State/Region Code, second column should be State/Region Name]]</div>
					<div class="clr"><br/></div>
					<div class="floatRight"><input type="submit" name="action" value="[[Import]]" class="greenButton" /></div>
				</td>
			</tr>
		</tbody>
	</table>
</form>