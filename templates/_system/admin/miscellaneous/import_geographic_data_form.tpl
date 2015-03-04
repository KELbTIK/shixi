{breadcrumbs}<a href="{$GLOBALS.site_url}/geographic-data/">[[Geographic Data]]</a> &#187; [[Import Data]]{/breadcrumbs}
<h1><img src="{image}/icons/boxdownload32.png" border="0" alt="" class="titleicon"/>[[Import Data]]</h1>

{foreach from=$errors item=error key=field_caption}
	{if $error eq 'EMPTY_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[is empty]]</p>
	{elseif $error eq 'NOT_UNIQUE_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[this value is already used in the system]]</p>
	{elseif $error eq 'NOT_CONFIRMED'}
		<p class="error">'[[{$field_caption}]]' [[not confirmed]]</p>
	{elseif $error eq 'DATA_LENGTH_IS_EXCEEDED'}
		<p class="error">'[[{$field_caption}]]' [[length is exceeded]]</p>
	{elseif $error eq 'NOT_INT_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[is not an integer value]]</p>
	{elseif $error eq 'OUT_OF_RANGE'}
		<p class="error">'[[{$field_caption}]]' [[value is out of range]]</p>
	{elseif $error eq 'NOT_FLOAT_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[is not an float value]]</p>
	{elseif $error eq 'FILE_NOT_UPLOADED'}
		<p class="error">'[[{$field_caption}]]' [[is not uploaded]]</p>
	{elseif $error eq 'WRONG_FORMAT'}
		<p class="error">'[[Please choose Excel or csv file]]</p>
    {elseif $error eq 'DO_NOT_MATCH_SELECTED_FILE_FORMAT'}
        <p class="error">[[The file type do not match with selected file format]]</p>
	{elseif $error eq 'UPLOAD_ERR_INI_SIZE'}
		<p class="error">[[File size exceeds system limit. Please check the file size limits on your hosting or upload another file.]]</p>
	{elseif $error eq 'UPLOAD_ERR_NO_FILE'}
		<p class="error">'[[{$field_caption}]]' [[is empty]]</p>
	{elseif $error eq 'CHARSET_INCORRECT'}
		<p class="error">[[The file encoding is incorrect. Please select appropriate encoding and try again.]]</p>
	{else}
		<p class="error">[[{$error}]]</p>
	{/if}
{/foreach}
{if $imported_location_count !== NULL}<p class="message">[[Imported locations]]: {$imported_location_count}</p>
{else}
<div id="dialog" style="display: none"></div>

<div class="setting_button" id="mediumButton">[[Show Import Help]]<div class="setting_icon"><div id="accordeonClosed"></div></div></div>
<div class="setting_block" style="display: none">
	<small>
		<p>[[To import locations(zip-codes) following information should be indicated]]:</p>
		<table width="100%">
			<tr class="{cycle values = 'evenrow,oddrow' advance=false}">
				<td>[[File]]</td>
				<td>[[your file that contains necessary data]]<small>([[max.]] {$uploadMaxFilesize} M)</small></td>
			</tr>
			<tr class="{cycle values = 'evenrow,oddrow' advance=false}">
				<td>[[File Format]]</td>
				<td>[[format in which the data is contained]]</td>
			</tr>
			<tr class="{cycle values = 'evenrow,oddrow' advance=false}">
				<td>[[Fields Delimiter]]</td>
				<td>[[applicable to CSV-files. The symbol which separates columns from each other]]</td>
			</tr>
			<tr class="{cycle values = 'evenrow,oddrow' advance=false}">
				<td>[[Start Line]]</td>
				<td>[[the number of the line within the file from which the data import will start]]</td>
			</tr>
			<tr class="{cycle values = 'evenrow,oddrow' advance=false}">
				<td>[[Name, Longitude & Latitude Columns]]</td>
				<td>[[the number of the corresponding columns in the file]]</td>
			</tr>
		</table>
		<p>[[For example, there is a file in CSV format to import]]:</p>
		<textarea cols="40" rows="5">
[[postcode,longitude,latitude]]
AB10,57.135,-2.117
AB11,57.138,-2.092
AB12,57.101,-2.111
AB13,57.108,-2.237
AB14,57.101,-2.27
AB15,57.138,-2.164
AB16,57.161,-2.156
		</textarea>
		<p>[[To import the file correctly we need to indicate following parameters]].</p>
		<table width="100%">
			<tr class="{cycle values = 'evenrow,oddrow' advance=false}">
				<td>[[File Format]]</td>
				<td>[[CSV]]</td>
			</tr>
			<tr class="{cycle values = 'evenrow,oddrow' advance=false}">
				<td>[[Fields Delimiter]]</td>
				<td>[[Comma]]</td>
			</tr>
			<tr class="{cycle values = 'evenrow,oddrow' advance=false}">
				<td>[[Start Line]]</td>
				<td>[[2 ( in the "1"  line we have the headers of the table which we do not need)]]</td>
			</tr>
			<tr class="{cycle values = 'evenrow,oddrow' advance=false}">
				<td>[[Name Column]]</td>
				<td>1</td>
			</tr>
			<tr class="{cycle values = 'evenrow,oddrow' advance=false}">
				<td>[[Longitude Column]]</td>
				<td>2</td>
			</tr>
			<tr class="{cycle values = 'evenrow,oddrow' advance=false}">
				<td>[[Latitude Column]]</td>
				<td>3</td>
			</tr>
			<tr class="{cycle values = 'evenrow,oddrow' advance=false}">
				<td>[[City Column]]</td>
				<td>4</td>
			</tr>
			<tr class="{cycle values = 'evenrow,oddrow' advance=false}">
				<td>[[State Column]]</td>
				<td>5</td>
			</tr>
			<tr class="{cycle values = 'evenrow,oddrow' advance=false}">
				<td>[[State Code Column]]</td>
				<td>6</td>
			</tr>
		</table>
	</small>
</div>

<div class="clr"><br/></div>

<form method="post" enctype="multipart/form-data" id="geographicDataForm">
<fieldset>
<legend>[[Import Data]]</legend>
	<table>

		<tr>
			<td>[[File]]</td>
			<td><input type="file" name="imported_geo_file" /> <span class="required">*</span> <small>([[max.]] {$uploadMaxFilesize} M)</small></td>
		</tr>
		<tr>
			<td>[[File Format]]</td>			<td>
				<select name="file_format">
				<option value="csv">[[CSV]]</option><option value="excel" {if $imported_file_config.file_format == excel}selected="selected"{/if}>[[Excel]]</option></select>
				<span class="required">*</span>
			</td>
		</tr>
		<tr>
			<td>[[Fields Delimiter]]:<br /><small>([[for CSV-file only]])</small></td>
			<td>
				<select name="fields_delimiter">
					<option value="comma">[[Comma]]</option>
					<option value="tab"{if $imported_file_config.fields_delimiter == tab} selected="selected"{/if}>[[Tabulator]]</option>
					<option value="semicolon"{if $imported_file_config.fields_delimiter == semicolon} selected="selected"{/if}>[[Semicolon]]</option>
				</select>
				<span class="required">*</span>
			</td>
		</tr>
		<tr>
			<td>[[Start Line]]</td>
			<td><input type="text" name="start_line" value="{$imported_file_config.start_line}" /> <span class="required">*</span></td>
		</tr>
		<tr>
			<td>[[Name Column]]</td>
			<td><input type="text" name="name_column" value="{$imported_file_config.name_column}" /> <span class="required">*</span></td>
		</tr>
		<tr>
			<td>[[Longitude Column]]</td>
			<td><input type="text" name="longitude_column" value="{$imported_file_config.longitude_column}" /> <span class="required">*</span></td>
		</tr>
		<tr>
			<td>[[Latitude Column]]</td>
			<td><input type="text" name="latitude_column" value="{$imported_file_config.latitude_column}" /> <span class="required">*</span></td>
		</tr>
		<tr>
			<td>[[City Column]]</td>
			<td><input type="text" name="city_column" value="{$imported_file_config.city_column}" /></td>
		</tr>
		<tr>
			<td>[[State Column]]</td>
			<td><input type="text" name="state_column" value="{$imported_file_config.state_column}" /></td>
		</tr>
		<tr>
			<td>[[State Code Column]]</td>
			<td><input type="text" name="state_code_column" value="{$imported_file_config.state_code_column}" /></td>
		</tr>
		<tr>
			<td>[[Country]]</td>
			<td>
				<select name="country_sid" >
					<option value="">[[Select Country]]</option>
					{foreach from=$countries item=country}
						<option value="{$country.id}" {if $country_sid == $country.id} selected = "selected"{/if} >{$country.caption}</option>
					{/foreach}
				</select> <span class="required">*</span>
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
		<tr>
			<td>[[Preview]]</td>
			<td>
				<input type="checkbox" name="preview"/>
			</td>
		</tr>
		<tr>
			<td colspan="2"><div class="floatRight"><input type="submit" value="[[Import]]" class="grayButton" /></div></td>
		</tr>

	</table>
</fieldset>
</form>
{/if}
<script type="text/javascript">
$(function() {
	$(".setting_button").click(function(){
		var butt = $(this);
		$(this).next(".setting_block").slideToggle("normal", function(){
				if ($(this).css("display") == "block") {
					butt.children(".setting_icon").html("<div id='accordeonOpen'></div>");
					butt.children("b").text("Hide Import Help");
				} else {
					butt.children(".setting_icon").html("<div id='accordeonClosed'></div>");
					butt.children("b").text("Show Import Help");
				}
			});
	});
});
$("#geographicDataForm").submit(function() {
	if ($("input[name='preview']").is(':checked')) {
		var formData = new FormData($(this)[0]);
		$("#dialog").dialog('destroy').html('{capture name="progressBar"}<img style="vertical-align: middle;" src="{$GLOBALS.user_site_url}/system/ext/jquery/progbar.gif" alt="[[Please wait ...]]" /> [[Please wait ...]]{/capture}{$smarty.capture.progressBar|escape:'quotes'}');
		$("#dialog").dialog({
			width: 700,
			height: 330,
			title: "[[Preview]]",
			position: "center",
			buttons: {
				"[[Import]]": function () {
					$("input[name='preview']").attr('checked', false);
					$("#geographicDataForm").submit();
				},
				"[[Cancel]]": function () {
					$(this).dialog("close");
				}
			}
		}).dialog('open');

		$.ajax({
			url: $(this).attr("action") + '?preview=1',
			type: 'POST',
			data: formData,
			async: false,
			success: function (data) {
				$("#dialog").html(data);
			},
			cache: false,
			contentType: false,
			processData: false
		});
		return false;
	}
	return true;
});
</script>