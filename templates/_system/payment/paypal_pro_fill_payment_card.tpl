<form id="formPayment" method="post" action="{$form_submit_url}" class="form-horizontal">
	<input type="hidden" id="action" name="action" value="BUTTON_PRESSED"/>{$hiddenFields}
	{if $errors}
		{foreach from=$errors item=error}
			<div class="error alert alert-danger">[[$error]]</div>
		{/foreach}
		<br/>
	{/if}
	<div class="form-group has-feedback">
		<div class="col-sm-8 col-sm-offset-3">
			<div class="orderInfo"><h1><span class="strong">[[Order Information]]</span></h1></div>
			<div class="orderInfo"><strong>[[Invoice Number]]:</strong> {$invoiceInfo.invoiceNumber}</div>
			<div class="orderInfo"><strong>[[Description]]:</strong> [[{$invoiceInfo.description}]]</div>
			<br/>
			<div class="orderInfo"><strong>[[Total]]:</strong> {capture assign = "totalPrice"}{tr type="float"}{$invoiceInfo.totalPrice}{/tr}{/capture}{currencyFormat amount=$totalPrice}</div>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group has-feedback">
		<label class="inputName control-label col-sm-3" ><span class="strong">[[Credit Card Information]]:</span></label>
		<div class="inputName col-sm-8 padding_correct">
			{foreach from=$creditCards item=card}
				<img class="img_block-inline" src="{image}/creditcards/{$card}.gif" height="26"/>
			{/foreach}
		</div>
	</div>





	<div class="form-group has-feedback">
		<label class="inputName col-sm-3 control-label" >[[Card Number]]:<span class="small text-danger">&nbsp;*</span></label>
		<div class="inputField col-sm-8" >
			<input type="text" class="input_text form-control" id="card_number" name="card_number" value="{$formFields.card_number}" maxLength="16"/>
			<div>([[enter number without spaces or dashes]])</div>
		</div>

	</div>
	<div class="form-group has-feedback">
		<label class="inputName col-sm-3 control-label" >[[Expiration Date]]:<span class="small text-danger">&nbsp;*</span></label>
		<div class="inputField col-sm-8" >
			<div class="row">
				<div class="col-sm-6">
					<select class="form-control"   id="exp_date_mm" name="exp_date_mm">
						{foreach from=$monthList item="month"}
							<option value="{$month}" {if $formFields.exp_date_mm == $month}selected="selected"{/if}>{$month}</option>
						{/foreach}
					</select> [[Month]]
				</div>
				<div class="col-sm-6">
					<select class="form-control"   id="exp_date_yy" name="exp_date_yy">
					{foreach from=$yearList item="yearListItem"}
						<option value="{$yearListItem}" {if $formFields.exp_date_yy == $yearListItem}selected="selected"{/if}>{$yearListItem}</option>
					{/foreach}
					</select> [[Year]]
				</div>
			</div>
		</div>
	</div>
	<div class="form-group has-feedback">
		<label class="inputName col-sm-3 control-label" >[[Credit card CSC value]]:<span class="small text-danger">&nbsp;*</span></label>
		<div class="inputField col-sm-8" >
			<input type="text" class="input_text form-control" id="csc_value" name="csc_value" value="{$formFields.csc_value}" maxLength="20"/>
		</div>
	</div>
	<div class="form-group has-feedback">
		<label class="inputName col-sm-3 control-label" >[[First Name]]:<span class="small text-danger">&nbsp;*</span></label>
		<div class="inputField col-sm-8" >
			<input type="text" class="input_text form-control" id="first_name" name="first_name" value="{$formFields.first_name}" maxLength="50"/>
		</div>
	</div>
	<div class="form-group has-feedback">
		<label class="inputName col-sm-3 control-label" >[[Last Name]]:<span class="small text-danger">&nbsp;*</span></label>
		<div class="inputField col-sm-8" >
			<input type="text" class="input_text form-control" id="last_name" name="last_name" value="{$formFields.last_name}" maxLength="50"/>
		</div>
	</div>
	<div class="form-group has-feedback">
		<label class="inputName col-sm-3 control-label" >[[Billing Address]]:<span class="small text-danger">&nbsp;*</span></label>
		<div class="inputField col-sm-8" >
			<input type="text" class="input_text form-control" id="address" name="address" value="{$formFields.address}" maxLength="60"/>
		</div>
	</div>
	<div class="form-group has-feedback">
		<label class="inputName col-sm-3 control-label" >[[Zip Code]]:<span class="small text-danger">&nbsp;*</span></label>
		<div class="inputField col-sm-8" >
			<input type="text" class="input_text form-control" id="zip" name="zip" value="{$formFields.zip}" maxLength="60"/>
		</div>
	</div>
	<div class="form-group has-feedback">
		<label class="inputName col-sm-3 control-label" >[[Country]]:<span class="small text-danger">&nbsp;*</span></label>
		<div class="inputField col-sm-8" >
			{$selCountry = ($formFields.country) ? $formFields.country : $curUserCountryInfo.country_code}
			<select class="form-control"  id="country" name="country">
				<option value="">[[Select Country]]</option>
				{foreach from=$CountryList key="Country" item="countryCode"}
					<option value="{$countryCode}" {if $selCountry == $countryCode}selected="selected"{/if}>{$Country}</option>
				{/foreach}
			</select>
		</div>
	</div>
	<div class="form-group has-feedback">
		<label class="inputName col-sm-3 control-label" >[[City]]:<span class="small text-danger">&nbsp;*</span></label>
		<div class="inputField col-sm-8" >
			<input type="text" class="input_text form-control" id="city" name="city" value="{$formFields.city}" maxLength="40"/>
		</div>
	</div>
	<div class="form-group has-feedback">
		<label class="inputName col-sm-3 control-label" >[[State/Region]]:<span class="small text-danger">&nbsp;{if in_array($selCountry, array("US", "GB", "AU", "CA"))}*{/if}</span></label>
		<div class="inputField col-sm-8 padding_correct" >
			{if in_array($selCountry, array("US", "GB", "AU", "CA"))}
				<select class="form-control"  id="state" name="state">
					<option value="">[[Select State/Region]]</option>
				</select>
			{else}
				<input type="text" class="input_text form-control" id="state" name="state" value="{$formFields.state}" maxLength="40"/>
			{/if}
		</div>
	</div>
	<div class="form-group has-feedback">
		<label class="inputName col-sm-3 control-label" >[[Email]]:<span class="small text-danger">&nbsp;*</span></label>
		<div class="inputField col-sm-8" >
			<input type="text" class="input_text form-control" id="email" name="email" value="{$formFields.email}" maxLength="255"/>
		</div>
	</div>
	<div class="form-group has-feedback">
		<label class="inputName col-sm-3 control-label" >[[Phone Number]]:<span class="small text-danger">&nbsp;*</span></label>
		<div class="inputField col-sm-8" >
			<input type="text" class="input_text form-control" id="phone" name="phone" value="{$formFields.phone}" maxLength="25"/>
		</div>
	</div>

	<div class="form-group has-feedback">
		<div class="inputName" >&nbsp;</div>
		{capture name="trPayNow"}[[Pay Now]]{/capture}
		<div class="inputName col-sm-8 col-sm-offset-3" ><input class="btn btn-success" type="submit" value="{$smarty.capture.trPayNow|escape:'quotes'}" /></div>
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
