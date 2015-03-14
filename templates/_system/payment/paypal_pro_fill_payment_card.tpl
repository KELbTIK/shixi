<form id="formPayment" method="post" action="{$form_submit_url}">
	<input type="hidden" id="action" name="action" value="BUTTON_PRESSED"/>{$hiddenFields}
	{if $errors}
		{foreach from=$errors item=error}
			<div class="error alert alert-danger">[[$error]]</div>
		{/foreach}
		<br/>
	{/if}
	<div class="form-group has-feedback">
		<div class="orderInfo"><span class="strong">[[Order Information]]</span></div>
		<div class="orderInfo">[[Invoice Number]]: {$invoiceInfo.invoiceNumber}</div>
		<div class="orderInfo">[[Description]]: [[{$invoiceInfo.description}]]</div>
		<div class="orderInfo">[[Total]]: {capture assign = "totalPrice"}{tr type="float"}{$invoiceInfo.totalPrice}{/tr}{/capture}{currencyFormat amount=$totalPrice}</div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group has-feedback">
		<div class="inputName" ><span class="strong">[[Credit Card Information]]:</span></div>
		<div class="inputReq" >&nbsp;</div>
		<div class="inputName">
			{foreach from=$creditCards item=card}
				<img src="{image}/creditcards/{$card}.gif" height="26"" />
			{/foreach}
		</div>
	</div>
	<div class="form-group has-feedback">
		<div class="inputName" >[[Card Number]]:</div>
		<div class="inputReq">&nbsp;*</div>
		<div class="inputField" >
			<input type="text" class="input_text form-control" id="card_number" name="card_number" value="{$formFields.card_number}" maxLength="16"/>
			<br/>([[enter number without spaces or dashes]])
		</div>
	</div>
	<div class="form-group has-feedback">
		<div class="inputName" >[[Expiration Date]]:</div>
		<div class="inputReq">&nbsp;*</div>
		<div class="inputField" >
			<select class="form-control"   id="exp_date_mm" name="exp_date_mm">
				{foreach from=$monthList item="month"}
					<option value="{$month}" {if $formFields.exp_date_mm == $month}selected="selected"{/if}>{$month}</option>
				{/foreach}
			</select> [[Month]]
			<select class="form-control"   id="exp_date_yy" name="exp_date_yy">
			{foreach from=$yearList item="yearListItem"}
				<option value="{$yearListItem}" {if $formFields.exp_date_yy == $yearListItem}selected="selected"{/if}>{$yearListItem}</option>
			{/foreach}
			</select> [[Year]]
		</div>
	</div>
	<div class="form-group has-feedback"><div class="inputName" >[[Credit card CSC value]]:</div>
		<div class="inputReq">&nbsp;*</div>
		<div class="inputField" >
			<input type="text" class="input_text form-control" id="csc_value" name="csc_value" value="{$formFields.csc_value}" maxLength="20"/>
		</div>
	</div>
	<div class="form-group has-feedback"><div class="inputName" >[[First Name]]:</div>
		<div class="inputReq">&nbsp;*</div>
		<div class="inputField" >
			<input type="text" class="input_text form-control" id="first_name" name="first_name" value="{$formFields.first_name}" maxLength="50"/>
		</div>
	</div>
	<div class="form-group has-feedback"><div class="inputName" >[[Last Name]]:</div>
		<div class="inputReq">&nbsp;*</div>
		<div class="inputField" >
			<input type="text" class="input_text form-control" id="last_name" name="last_name" value="{$formFields.last_name}" maxLength="50"/>
		</div>
	</div>
	<div class="form-group has-feedback"><div class="inputName" >[[Billing Address]]:</div>
		<div class="inputReq">&nbsp;*</div>
		<div class="inputField" >
			<input type="text" class="input_text form-control" id="address" name="address" value="{$formFields.address}" maxLength="60"/>
		</div>
	</div>
	<div class="form-group has-feedback"><div class="inputName" >[[Zip Code]]:</div>
		<div class="inputReq">&nbsp;*</div>
		<div class="inputField" >
			<input type="text" class="input_text form-control" id="zip" name="zip" value="{$formFields.zip}" maxLength="60"/>
		</div>
	</div>
	<div class="form-group has-feedback"><div class="inputName" >[[Country]]:</div>
		<div class="inputReq">&nbsp;*</div>
		<div class="inputField" >
			{$selCountry = ($formFields.country) ? $formFields.country : $curUserCountryInfo.country_code}
			<select class="form-control"  id="country" name="country">
				<option value="">[[Select Country]]</option>
				{foreach from=$CountryList key="Country" item="countryCode"}
					<option value="{$countryCode}" {if $selCountry == $countryCode}selected="selected"{/if}>{$Country}</option>
				{/foreach}
			</select>
		</div>
	</div>
	<div class="form-group has-feedback"><div class="inputName" >[[City]]:</div>
		<div class="inputReq">&nbsp;*</div>
		<div class="inputField" >
			<input type="text" class="input_text form-control" id="city" name="city" value="{$formFields.city}" maxLength="40"/>
		</div>
	</div>
	<div class="form-group has-feedback"><div class="inputName" >[[State/Region]]:</div>
		<div class="inputReq">&nbsp;{if in_array($selCountry, array("US", "GB", "AU", "CA"))}*{/if}</div>
		<div class="inputField" >
			{if in_array($selCountry, array("US", "GB", "AU", "CA"))}
				<select class="form-control"  id="state" name="state">
					<option value="">[[Select State/Region]]</option>
				</select>
			{else}
				<input type="text" class="input_text form-control" id="state" name="state" value="{$formFields.state}" maxLength="40"/>
			{/if}
		</div>
	</div>
	<div class="form-group has-feedback"><div class="inputName" >[[Email]]:</div>
		<div class="inputReq">&nbsp;</div>
		<div class="inputField" >
			<input type="text" class="input_text form-control" id="email" name="email" value="{$formFields.email}" maxLength="255"/>
		</div>
	</div>
	<div class="form-group has-feedback">
		<div class="inputName" >[[Phone Number]]:</div>
		<div class="inputReq">&nbsp;</div>
		<div class="inputField" >
			<input type="text" class="input_text form-control" id="phone" name="phone" value="{$formFields.phone}" maxLength="25"/>
		</div>
	</div>
	<div class="form-group has-feedback">
		<div class="inputName" >&nbsp;</div>
	</div>
	<div class="form-group has-feedback">
		<div class="inputName" >&nbsp;</div>
		{capture name="trPayNow"}[[Pay Now]]{/capture}
		<div class="inputName" ><input class="btn btn-default" type="submit" value="{$smarty.capture.trPayNow|escape:'quotes'}" /></div>
	</div>
</form>
<script type="text/javascript">
	function getStates(pickedCountryCode) {
		var parent = $("#state").parent();
		if ($.inArray(pickedCountryCode, ["US", "AU", "CA", "GB"]) != -1) {
			parent.prev().html("&nbsp;*");
			var url = "{$GLOBALS.site_url}/paypal-pro-fill-payment-card";
			$("#state").remove();
			parent.append(
				$("<select>", {
					id: "state",
					name: "state",
					html: "<option value=''>[[Select State/Region]]</option>"
				})
			);
			$.ajax({
				url: url,
				type: "POST",
				data: {
					"countryCode": pickedCountryCode
				},
				success: function(data) {
					var states = $.parseJSON(data);
					$.each(states, function(index, value) {
						if ($.isPlainObject(value)) {
							var optgroup = $("<optgroup>", {
								label: index
							});
							$.each(value, function(index, value) {
								$("<option>", {
									value: value,
									text: index
								}).appendTo(optgroup);
							});
							optgroup.appendTo($("#state"));
						} else {
							$("<option>", {
								value: value,
								text: index
							}).appendTo($("#state"));
						}
					})
				}
			});
		} else {
			parent.prev().html("&nbsp;");
			$("#state").remove();
			parent.append(
				$("<input>", {
					id: "state",
					class: "input_text",
					name: "state"
				})
			);
		}
	}
	
	$(function() {
		$("#country").change(function() {
			getStates(this.value);
		});
		$("#country").trigger("change");
	})
</script>
