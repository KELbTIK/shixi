{breadcrumbs}[[Import Listings]]{/breadcrumbs}
<h1><img src="{image}/icons/boxdownload32.png" border="0" alt="" class="titleicon" /> [[Import Listings]]</h1>
{include file="error.tpl"}
<form method="post"  enctype="multipart/form-data" onsubmit="disableSubmitButton('submitImport');">
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
					<select name="listing_type_id">
						{foreach from=$listing_types item=listing_type}
							<option value="{$listing_type.id}">[[{$listing_type.name}]]</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr class="oddrow">
				<td>[[Product]]</td>
				<td>
					<select name="product_sid">
						{foreach from=$products item=product}
							<option value="{$product.sid}">[[{$product.name}]]</option>
						{/foreach}
					</select>
				</td>
		    </tr>
			<tr class="evenrow">
				<td>[[Active status]]</td>
				<td><input type="checkbox" name="active" value="1" /></td>
		    </tr>
			<tr class="oddrow">
				<td>[[Activation date]]</td>
				<td><input type="text" name="activation_date" value="" id="activation_date_import" /></td>
			</tr>
		    <tr id="clearTable">
				<td colspan="2">&nbsp;</td>
			</tr>
			</tbody>
			<thead>
			    <tr>
					<th colspan="2">[[Data Import]]</th>
				</tr>
			</thead>
			<tbody>
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
					<select name="csv_delimiter" >
						<option value="semicolon">[[Semicolon]]</option>
						<option value="comma">[[Comma]]</option>
						<option value="tab">[[Tabulator]]</option>
					</select>
				</td>
			</tr>
			<tr class="evenrow">
				<td>[[Values not found in DB will be]]</td>
				<td>
		            <select name="non_existed_values" />
						<option value="ignore">[[ignored]]</option>
						<option value="add">[[added to DB]]</option>
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
		    <tr id="clearTable">
				<td colspan="2">
                    <div class="clr"><br/></div>
					<div class="floatRight">
						<input type="submit" name="action" value="[[Import]]" id="submitImport" class="greenButton" />
					</div>
                </td>
			</tr>
		</tbody>
	</table>
</form>

<script>
$(function () {

	var dFormat = '{$GLOBALS.current_language_data.date_format}';

	dFormat = dFormat.replace('%m', "mm");
	dFormat = dFormat.replace('%d', "dd");
	dFormat = dFormat.replace('%Y', "yy");
	
	$("#activation_date_import").datepicker({
		dateFormat: dFormat,
		showOn: 'both',
		yearRange: '-99:+99',
		buttonImage: '{image}icons/icon-calendar.png'
	});

});
</script>
