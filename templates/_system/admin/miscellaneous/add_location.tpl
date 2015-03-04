<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/jquery.js"></script>
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
	{/if}
{/foreach}

{if $locationAdded}
<p class="message">[[Zip Code was successfully added]]</p>
{/if}

<form method="post" action="{$GLOBALS.site_url}/geographic-data/add/" id="addLocationForm">
	<input type="hidden" name="action" value="add">
	<table class="popup-table">
		<tr>
			<td>[[Zip Code]]</td>
			<td><span class="required">*</span></td>
			<td><input type="text" name="name" value="{$location_info.name|escape:'html'}"></td>
		</tr>
		<tr>
			<td>[[Longitude]]</td>
			<td><span class="required">*</span></td>
			<td><input type="text" name="longitude" value="{$location_info.longitude|escape:'html'}"></td>
		</tr>
		<tr>
			<td>[[Latitude]]</td>
			<td><span class="required">*</span></td>
			<td><input type="text" name="latitude" value="{$location_info.latitude|escape:'html'}"></td>
		</tr>
		<tr>
			<td>[[Country]]</td>
			<td><span class="required">*</span></td>
			<td>
				<select id="country" name="country_sid" onchange="getStates(this.value);">
					<option value="">[[Select Country]]</option>
					{foreach from=$countries item=country}
						<option value="{$country.id}" {if $location_info.country_sid == $country.id} selected = "selected"{/if} >{$country.caption}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td>[[State]]</td>
			<td></td>
			<td>
				<select id="state" name="state" disabled="true" data-old-value="{$location_info.state}">
					<option value=""></option>
				</select>
			</td>
		</tr>
		<tr>
			<td>[[City]]</td>
			<td></td>
			<td><input type="text" name="city" value="{$location_info.city|escape:'html'}"></td>
		</tr>
		<tr>
			<td colspan="3">
				<div class="floatRight"><input type="submit" value="[[Add]]" class="grayButton"/></div>
			</td>
		</tr>
	</table>
</form>

<script language='JavaScript' type='text/javascript'>
	function getStates(countrySID) {
		if ($("#country").val() == '') {
			$("#state").attr("disabled", "disabled");
		} else {
			$("#state").removeAttr("disabled");
		}
		$.get("{$GLOBALS.site_url}/get-states/", { country_sid: countrySID, state_sid: "", parentID: "Location", display_as: "", type: "modifyZipCode" } ,
			function (data) {
				var stateField = $("#state");
				if (data != '') {
					stateField.html(data);
					stateField.find("option[value='" + stateField.attr('data-old-value') + "']").attr("selected", "selected");
				}
			}
		);
	}

	$(document).ready(function () {
			getStates($("#country").val());
		}
	);
</script>
