<h1>[[{$productInfo.name}]]</h1>
{if $errors}
	{foreach from=$errors item=error}
		{if $error eq "PRODUCT_IS_ONLY_ONCE_AVAILABLE"}
			<p class="error">[[This product is available only once]]</p>
		{elseif $error eq 'UPLOAD_ERR_INI_SIZE'}
			<p class="error">[[File size exceeds system limit]]</p>
		{elseif $error eq 'UPLOAD_ERR_FORM_SIZE'}
			<p class="error">[[File size exceeds system limit]]</p>
		{elseif $error eq 'UPLOAD_ERR_PARTIAL'}
			<p class="error">[[There was an error during file upload]]</p>
		{elseif $error eq 'UPLOAD_ERR_NO_FILE'}
			<p class="error">[[file not specified]]</p>
		{elseif $error eq 'NOT_UPLOAD_FILE'}
			<p class="error">[[The file was not uploaded. Please try another file]]</p>
		{else}
			<p class="error">[[{$error}]]</p>
		{/if}
	{/foreach}
{/if}
{if $mayChooseProduct}
	<form action="" method="post" enctype="multipart/form-data" >
		<input type="hidden" name="product_sid" value="{$productSID}" />
		<input type="hidden" name="event" value="add_product" />
		<div id="productDetails">
			[[{$productInfo.detailed_description}]]
			{capture assign="productPrice"}{tr type="float"}{$productInfo.price}{/tr}{/capture}
			{if $productInfo.period}
				{if $productInfo.period_name != 'unlimited'}
					<div class="productDetails-name">[[Period]]:</div>
					<div class="productDetails-info">{$productInfo.period} {if $productInfo.period > 1 }[[{$productInfo.period_name|capitalize}s]]{else}[[{$productInfo.period_name|capitalize}]]{/if}</div>
					<div class="clr"></div>
				{/if}
				<div class="productDetails-name">[[Price]]:</div>
				<div class="productDetails-info viewProductsPrice">{currencyFormat amount=$productPrice}</div>
			{elseif $productInfo.fixed_period}
				<div class="productDetails-name">[[Qty]]:</div>
				<div class="productDetails-info">{$productInfo.number_of_listings}</div>
				<div class="clr"></div>
				<div class="productDetails-name">[[Price]]:</div>
				<div class="productDetails-info viewProductsPrice">{currencyFormat amount=$productPrice}</div>
			{elseif $productInfo.pricing_type == custom_period}
				{if $productInfo.expiration_period}
					<div class="productDetails-name">[[Period]]:</div>
					<div class="productDetails-info">{$productInfo.expiration_period} [[days]]</div>
					<div class="clr"></div>
				{/if}
				<div class="productDetails-name">[[Price]]:</div>
				<div class="productDetails-info viewProductsPrice">{currencyFormat amount=$productPrice}</div>
			{elseif $productInfo.volume_based_pricing}
				<table cellspacing="0" style="width: 50%">
					<thead>
						<tr>
							<th class="tableLeft"> </th>
							<th>[[Qty]]</th>
							<th class="text-center">[[Price per Posting]]</th>
							<th class="text-center">[[Savings]]</th>
							<th class="tableRight"> </th>
						</tr>
					</thead>
					<tbody>
					{foreach from=$productInfo.volume_based_pricing item=pricing}
						<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
							<td></td>
							<td>{$pricing.range.from}{if $pricing.range.to} - {$pricing.range.to}{/if}</td>
							<td class="text-center">
								{capture assign="price"}{tr type="float"}{$pricing.price}{/tr}{/capture}
								{currencyFormat amount=$price}
							</td>
							<td class="text-center">{if $pricing.savings}{$pricing.savings}%{/if}</td>
							<td></td>
						</tr>
					{/foreach}
					</tbody>
				</table>
				<br/><br/>
				<div id="productsSelect">
					<select name="number_of_listings" id="number_of_listings" class="numberOfListings" onChange="getPrice(this.value)">
						{foreach from=$productInfo.count_listings item=count_listings}
							<option value="{$count_listings}">[[{$count_listings}]]</option>
						{/foreach}
					</select>
				</div>
				<div id="volume_price">&nbsp;</div>
			{else}
				{if $productInfo.number_of_listings}
					<div class="productDetails-name">[[Qty]]:</div>
					<div class="productDetails-info">{$productInfo.number_of_listings}</div>
					<div class="clr"></div>
				{/if}
				{if $productInfo.expiration_period}
					<div class="productDetails-name">[[Period]]:</div>
					<div class="productDetails-info">{$productInfo.expiration_period} [[days]]</div>
					<div class="clr"></div>
				{/if}
				<div class="productDetails-name">[[Price]]:</div>
				<div class="productDetails-info viewProductsPrice">{currencyFormat amount=$productPrice}</div>
			{/if}
		</div>
		{if $productInfo.product_type == 'banners'}
			<br/><br/>
			<table>
				<tr>
					<td><span class="strong">[[Required Banner Width]]:</span></td>
					<td>&nbsp;&nbsp;&nbsp;{$productInfo.width} Pixels</td>
				</tr>
				<tr>
					<td><span class="strong">[[Required Banner Height]]:</span></td>
					<td>&nbsp;&nbsp;&nbsp;{$productInfo.height} Pixels</td>
				</tr>
				<tr>
					<td><span class="strong">[[Banner Name]]:</span></td>
					<td><div class="inputReq">&nbsp;*</div><input type="{$banner_fields.title.type}" name="{$banner_fields.title.id}" value="{$banner_fields.title.value}" /></td>
				</tr>
				<tr>
					<td><span class="strong">[[Banner Link]]:</span></td>
					<td><div class="inputReq">&nbsp;*</div><input type="{$banner_fields.link.type}" name="{$banner_fields.link.id}" value="{$banner_fields.link.value}" /></td>
				</tr>
				<tr>
					<td><span class="strong">[[Banner File]]:</span></td>
					<td><div class="inputReq">&nbsp;*</div><input type="{$banner_fields.image.type}" name="{$banner_fields.image.id}" value="{$banner_fields.image.value}" /></td>
				</tr>
			</table>
		{/if}
		<div class="clr"></div>
		<br />
		<div class="product-detail-button"><input type="button" name="continue" value="[[Back to Products]]" onClick="location.href = '{$GLOBALS.site_url}/{$userGroupID|lower}-products/'" /></div>
		{if $GLOBALS.settings.allow_to_post_before_checkout == '1' && ($productInfo.product_type == 'post_listings' || $productInfo.product_type == 'mixed_product')}
			<div class="product-detail-button">
				<input type="hidden" name="productSID" value="{$productSID}" />
				<input type="hidden" name="proceed_to_posting" value="done" />
				<input type="hidden" name="listing_type_id" value="{$productInfo.listingTypeID}" />
				<input type="button" value="[[Proceed to Posting]]" id="proceedToPosting" onclick="submitProductDetailForm('proceedToPosting');" />
			</div>
		{/if}
		<div>
			<input type="button" name="checkout" id="addToCart" value="[[Add to Cart]]" onclick="submitProductDetailForm('addToCart');" />
		</div>
	</form>
	<script language="javascript" type="text/javascript">
		var langSettings = {
			thousands_separator : '{$GLOBALS.current_language_data.thousands_separator}',
			decimal_separator : '{$GLOBALS.current_language_data.decimal_separator}',
			decimals : '{$GLOBALS.current_language_data.decimals}',
			currencySign: '{$GLOBALS.settings.transaction_currency}',
			showCurrencySign: 1,
			currencySignLocation: '{$GLOBALS.current_language_data.currencySignLocation}',
			rightToLeft: {$GLOBALS.current_language_data.rightToLeft}
		};

		function submitProductDetailForm(buttonToDisable)
		{
			disableSubmitButton(buttonToDisable);
			if (buttonToDisable == "proceedToPosting") {
				$("form").attr("action", "{$GLOBALS.site_url}/add-listing/?listing_type_id={$productInfo.listingTypeID}");
			}
			$("form").submit();
		}

		function getPrice(number_of_listings)
		{ldelim}
			var price = 0;
			{foreach from=$productInfo.volume_based_pricing item=pricing}
				{if is_array($pricing.range)}
					{if $pricing.range.to}
						if (number_of_listings >= {$pricing.range.from} && number_of_listings <= {$pricing.range.to}) {ldelim}
							price = {$pricing.price}*number_of_listings;
						{rdelim}
					{else}
						if (number_of_listings == {$pricing.range.from}) {ldelim}
							price = {$pricing.price}*number_of_listings;
						{rdelim}
					{/if}
				{/if}
			{/foreach}
			price = formatNumber(roundNumber(price));
			$("#volume_price").html(price);
		{rdelim}

		var number_of_listings = $("#number_of_listings").val();
		getPrice(number_of_listings);
	</script>
{/if}