{if !isset($extraInfo.listing_duration)}
	{$extraInfo.listing_duration = (isset($contract['listing_duration'])) ? $contract['listing_duration'] : $productInfo['listing_duration']}
{/if}

{if $extraInfo.listing_duration}
	{if $listing['activation_date']}
		{$maxExpirationDate = strftime("{$GLOBALS.current_language_data.date_format}", strtotime("+{$extraInfo.listing_duration} day", strtotime($listing['activation_date'])))}
	{else}
		{$maxExpirationDate = strftime("{$GLOBALS.current_language_data.date_format}", strtotime("+{$extraInfo.listing_duration} day"))}
	{/if}
{/if}

{$fullPeriod = $maxExpirationDate == $value || $value == ''}
<div class="table-responsive">
	<table id="expiration-date">
	<tr>
		<td valign="top">
			<input {if $fullPeriod}checked="checked"{/if} {if $expired}disabled="disabled"{/if} class="inputRadio{if $complexField} complexField{/if}" name="exp_date" value="1" type="radio" id="maximum-length" />
		</td>
		<td>
			<label for="maximum-length">[[Maximum length]] ({if $extraInfo.listing_duration}{$extraInfo.listing_duration} [[days]]{else}[[Unlimited]]{/if})</label>
			<input type="hidden" {if !$fullPeriod}disabled="disabled"{/if} id="{$id}_1" value="{if $maxExpirationDate}{$maxExpirationDate}{/if}" name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}"/>
		</td>
	</tr>
	<tr>
		<td valign="top">
			<input {if !$fullPeriod}checked="checked"{/if} {if $expired}disabled="disabled"{/if} class="inputRadio{if $complexField} complexField{/if}" name="exp_date" value="2" type="radio" id="until" />
		</td>
		<td>
			<label for="until">[[Until]]:</label>
			<input {if $fullPeriod}disabled="disabled"{/if} type="text" id="{$id}_2" readonly value="{if !$fullPeriod}{tr type="date"}{if $mysql_date && !$complexField}{$mysql_date}{else}{$value}{/if}{/tr}{/if}" class="input_date displayDate {if $complexField}complexField{/if}" name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}"/>
		</td>
	</tr>
</table>
</div>

<script type="text/javascript">
	var dFormat = '{$GLOBALS.current_language_data.date_format}';
	var maxExpirationDate = '{$maxExpirationDate}';
	var id = '{$id}';
	var listingDuration = '{$extraInfo.listing_duration}';
	var expired = '{$expired}';
	if (expired) {
		$("#" + id + "_2").datepicker('hide');
	} else {
		dFormat = dFormat.replace('%m', "mm");
		dFormat = dFormat.replace('%d', "dd");
		dFormat = dFormat.replace('%Y', "yy");
		var dp = $("#" + id + "_2").datepicker({
			dateFormat: dFormat,
			showOn: 'button',
			changeMonth: true,
			changeYear: true,
			minDate: '+1d',
			yearRange: '-99:+99',
			buttonImage: '{$GLOBALS.user_site_url}/system/ext/jquery/calendar.gif',
			buttonImageOnly: true
		});
		if (listingDuration) {
			dp.datepicker("option", "maxDate", maxExpirationDate);
		}
		$(".ui-datepicker-trigger").click(function() {
			$("[name=exp_date]:not(:first)").attr('checked', true);
			$("#" + id + "_2").removeAttr("disabled").val(maxExpirationDate);
		});
		$("[name = exp_date]").click(function(){
			var checked = $("[name = exp_date]:radio:checked").val();
			var unchecked = $("[name = exp_date]:radio:not(:checked)").val();
			if (checked == 1) $("#"+ id +"_"+ unchecked).val("");
			if (checked == 2) $("#"+ id +"_"+ checked).val(maxExpirationDate);
			$("#" + id + "_"+ unchecked).attr("disabled", "disabled");
			$("#" + id + "_"+ checked).removeAttr("disabled");
		});
	}
</script>
