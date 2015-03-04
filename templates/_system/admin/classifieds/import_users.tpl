{breadcrumbs}[[Import Users]]{/breadcrumbs}
<h1><img src="{image}/icons/boxdownload32.png" border="0" alt="" class="titleicon" />[[Import Users]]</h1>
{include file="error.tpl"}
<form method="post"  enctype="multipart/form-data" onsubmit="disableSubmitButton('submitImport');" >
	<table>
		<thead>
		 	<tr>
				<th colspan="2">[[System Import Values]]</th>
			</tr>
		</thead>
		<tbody>
			<tr class="evenrow">
		        <td>[[Type]]</td>
				<td>
					<select name="user_group_id">
						{foreach from=$user_groups item=user_group}
							<option value="{$user_group.id}">[[{$user_group.name}]]</option>
						{/foreach}
					</select>
				</td>
			</tr>
	    	<tr id="clearTable"><td colspan="2">&nbsp;</td></tr>
	   	</tbody>
	   	<thead>
		    <tr>
				<th colspan="2">[[Data Import]]</th>
			</tr>
			<tr class="oddrow">
				<td>[[File]]:</td>
				<td><input type="file" name="import_file" value="" /> <small>([[max.]] {$uploadMaxFilesize} M)</small></td>
			</tr>
			<tr class="evenrow">
				<td>[[File Type]]:</td>
				<td>
					<select name="file_type">
						<option value="csv">CSV</option>
						<option value="xls">Excel</option>
					</select>
				</td>
			</tr>
			<tr class="oddrow">
				<td>[[Fields Delimiter]]:<br /><small>([[for CSV-file only]])</small></td>
				<td>
					<select name="csv_delimiter">
						<option value="semicolon">[[Semicolon]]</option>
						<option value="comma">[[Comma]]</option>
						<option value="tab">[[Tabulator]]</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>[[Encoding]]<br /><small>([[for CSV-file only]])</small></td>
				<td>
					<select name="encodingFromCharset" >
						<option value="UTF-8">[[Default]]</option>
						{foreach from=$charSets item=charSet}
							<option value="{$charSet}">{$charSet}</option>
						{/foreach}
					</select>
					<div class="commentSmall">[[Select appropriate encoding for your language  in case you have problems with import of certain symbols]]</div>
				</td>
			</tr>
		</thead>
	    <tr id="clearTable">
			<td colspan="2" align="right">
				<div class="floatRight">
					<input type="submit" name="action" value="[[Import]]" class="greenButton" id="submitImport" />
				</div>
            </td>
		</tr>
	</table>
</form>
