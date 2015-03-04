{if $account_activated}
	<p class="message">
		[[Your account was successfully activated. Thank you.]]
	</p>
{/if}
{if $confirmation}
	<form action="{$GLOBALS.site_url}/payment-page/" method="post" enctype="multipart/form-data" >
		<input type="hidden" name="action" value="pay_for_products" />
		<input type="hidden" name="sub_total_price" value="{$sub_total_price}" />
		<p class="information">[[You have selected the following recurring product(s). Please choose whether you want to buy them (pay once) or subscribe for them (pay every billing period):]]</p>
		{foreach from=$products item=product name=products_block}
			{if $product.recurring}<p><span class="strong">[[{$product.name}]]</span></p>{/if}
		{/foreach}
		<div class="clr"><br/></div>
		<div class="text-center"><input type="submit" name="buy" value="[[Buy]]" > &nbsp; <input type="submit" name="subscribe" value="[[Subscribe]]" ></div>
	</form>
{else}
	<h1>[[Shopping Cart]]</h1>
	<div class="clr"><br/></div>
	{if $error == 'user_group'}
		<p class="error">[[You have logged in as {$GLOBALS.current_user.group.caption} but the products you have chosen belong to another User Group. They were automatically deleted from your Shopping Cart]]</p>
	{elseif $error == 'trial_product'}
		<p class="error">[[One of the products in your Shopping Cart seems to be a Trial Product. You cannot subscribe for a Trial Product again because you have already subscribed for it. The Trial Product was automatically removed from your Shopping Cart]]</p>
	{/if}
	<form action="" method="post" enctype="multipart/form-data" name="shoppingCartForm" onsubmit="disableSubmitButton('checkoutSubmit');">
	<input type="hidden" name="action" value="checkout" />
	<input type="hidden" name="total_price" value="{$total_price}" />
	<input type="hidden" name="discount_total_amount" value="{$discountTotalAmount}" />
	<input type="hidden" name="sub_total_price" value="0" />
	<table cellspacing="0" id="shoppingCartTable">
		<thead>
			<tr>
				<th class="tableLeft"> </th>
				<th>[[Item in the cart]]</th>
				<th>[[Qty/Period]]</th>
				<th>[[Price]]</th>
				<th>&nbsp;</th>
				<th class="tableRight"> </th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$products item=product}
				<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
					<td></td>
					<td width="48%" valign="top">
						<span class="strong">[[{$product.name|paymentTranslate}]]</span>
						{if $product.custom_info.extraDescription}<br /><small>[[{$product.custom_info.extraDescription}]]</small>{/if}
					</td>
					<td width="40%" valign="top">
						{if $product.period}
							{if $product.period_name != 'unlimited'}
								{$product.period} {if $product.period > 1 }[[{$product.period_name|capitalize}s]]{else}[[{$product.period_name|capitalize}]]{/if}
							{else}
								[[unlimited]]
							{/if}
						{elseif $product.QtyPeriod}
							{$product.QtyPeriod}
						{elseif $product.volume_based_pricing}
							<select name="number_of_listings[{$product.sid}][{$product.item_sid}]" id="number_of_listings_{$product.sid}_{$product.item_sid}" onChange="getPrice(this.value, '{$product.sid}', '{$product.item_sid}')" class="numberOfListings">
								{foreach from=$product.count_listings item=count_listings_info}
									<option value="{$count_listings_info.number_of_listings}" {if $count_listings_info.number_of_listings == $product.number_of_listings} selected="selected" {/if} >[[{$count_listings_info.number_of_listings}]]</option>
								{/foreach}
							</select>
							{foreach from=$product.count_listings item=count_listings_info}
								<input type="hidden"
										name="price_per_unit_{$product.sid}_{$product.item_sid}_{$count_listings_info.number_of_listings}"
										id="price_per_unit_{$product.sid}_{$product.item_sid}_{$count_listings_info.number_of_listings}"
										value="{$count_listings_info.price}" primaryPrice="{$count_listings_info.primaryPrice}"
										percentPromoAmount="{$count_listings_info.percentPromoAmount}"
										percentPromoCode="{$count_listings_info.percentPromoCode}"
										productItemSid = "{$product.item_sid}" />
							{/foreach}
							<br/>
						{else}
							{if $product.expiration_period} {$product.expiration_period} [[days]]{else}[[unlimited]]{/if}
						{/if}
					</td>
					<td valign="top" width="10%" id="price_{$product.sid}_{$product.item_sid}">
						{capture assign="productPrice"}{tr type="float"}{$product.primaryPrice}{/tr}{/capture}
						{currencyFormat amount=$productPrice}
					</td>
					<td valign="top"><a class="remove" href="#" onClick="location.href = '{$GLOBALS.site_url}/shopping-cart/?action=delete&amp;item_sid={$product.item_sid}'">[[Remove]]</a></td>
					<td></td>
				</tr>
				{if $product.code_info}
					{if $product.pricing_type == 'volume_based' && $product.code_info.type != 'fixed'}
						<input type="hidden" id="product_{$product.item_sid}_primary_amount" value="{$product.count_listings[$product.number_of_listings].percentPromoAmount}">
					{/if}
				{/if}
			{/foreach}
			{if $promotionCodeInfo}
				<tr>
					<td colspan="3" style="text-align:right">
						<span class="strong">
						<a href="#"><img id="delete-promocode" src="{image}delete.png"></a>
						{capture assign="promoCodeDiscount"}{tr type="float"}{$promotionCodeInfo.discount}{/tr}{/capture}
						{$promotionCodeInfo.code} ({if $promotionCodeInfo.type == 'percentage'}{$promoCodeDiscount}%{else}{currencyFormat amount=$promoCodeDiscount}{/if}):
					</td>
					<td>
						<span style="color: #cc0000"> -
							<span id="code{$promotionCodeInfo.code}promoAmount">
								{capture assign="discountTotalAmount"}{tr type="float"}{$discountTotalAmount}{/tr}{/capture}
								{currencyFormat amount=$discountTotalAmount}
							</span>
						</span>
					</td>
					<td colspan="2"></td>
				</tr>
			{/if}
		<tr>
			<td colspan="3" style="text-align:right"><span class="strong">[[Sub Total]]:</span></td>
			<td>
				{capture assign="subTotalPrice"}{tr type="float"}{$total_price}{/tr}{/capture}
				<div id="sub_total_price">{currencyFormat amount=$subTotalPrice}</div>
			</td>
			<td colspan="2"></td>
		</tr>
		{if $tax.tax_name}
		<tr>
			<td colspan="3" style="text-align:right"><span class="strong">[[{$tax.tax_name}]]:</span></td>
			<td>
				{capture assign="taxAmount"}{tr type="float"}{$tax.tax_amount}{/tr}{/capture}
				<div class="floatRight" id="tax_amount"> {currencyFormat amount=$tax.taxAmount}</div>
			</td>
			<td colspan="2"></td>
		</tr>
		{/if}
		<tr>
			<td colspan="3" id="productTotal"><span class="strong">[[Total]]:</span></td>
			<td>
				{capture assign="totalPrice"}{tr type="float"}{$total_price}{/tr}{/capture}
				<div id="total_price"> {currencyFormat amount=$totalPrice}</div>
			</td>
			<td colspan="2"></td>
		</tr>
		</tbody>
	</table>

	{foreach from=$errors item=caption key=error}
		{if $error eq 'EMPTY_VALUE'}
			<p class="error">'[[{$caption}]]' [[is empty]]</p>
		{elseif $error eq 'NOT_VALID'}
			<p class="error">[[{$caption}]]</p>
		{/if}
	{/foreach}
	{if $applied_products}
		<p class="message">
			[[You have successfully applied the promotion code to the following product(s):]]<br/>
			{foreach from=$applied_products item=applied_product name=applied_product_block}
				[[{$applied_product.name}]]
				{if !$smarty.foreach.applied_product_block.last}
					,&nbsp;
				{/if}
			{/foreach}
		</p>
		<p class="information">[[You have received a discount of]] {strip}
				{if $code_info.type == 'percentage'}
					{$code_info.discount}%
				{else}
					{capture assign="discount"}{tr type="float"}{$code_info.discount}{/tr}{/capture}
					{currencyFormat amount=$discount}
				{/if}
			{/strip} [[for the above item(s).]]</p>
	{/if}
	{if $GLOBALS.settings.enable_promotion_codes == 1 && !$promotionCodeAlreadyUsed}
		<div id="promotionCode">
			<span class="strong">[[Promotion code]]:</span> <input type="text" name="promotion_code" id="inputPromotionCode" value="" />
			<input type="submit" name="applyPromoCode" value="[[Apply]]" id="applyPromoCode" />
		</div>
	{/if}

	<br/>
	<div class="continue-shopping"><input type="button" name="continue" value="[[Continue Shopping]]" onClick="location.href = '{$GLOBALS.site_url}/{if $userGroupID}{$userGroupID|lower}-products{else}products{/if}/'" /></div>
	<div><input type="submit" id="checkoutSubmit" name="submit" value="[[Checkout]]" {if !$GLOBALS.current_user.logged_in}onclick="popUpWindow('{$GLOBALS.site_url}/login/?shopping_cart=checkout&ajaxRelocate=1', 410, '[[Login]]', false, false); return false;"{/if} /></div>
	<div style="visibility: hidden;"><input type="submit" name="shoppingCartForm" value="[[Checkout]]" id="shoppingCartForm" /></div>
	<div class="clr"></div>
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

	function getPrice(number_of_listings, product_sid, item_sid)
	{ldelim}
		var price = formatNumber(roundNumber($("#price_per_unit_"+product_sid+"_"+item_sid+"_"+number_of_listings).attr('primaryPrice')));
		var productItemSid = $("#price_per_unit_"+product_sid+"_"+item_sid+"_"+number_of_listings).attr('productItemSid');
		var percentPromoCode = '{$product.code_info.code}';
		var type = '{$product.code_info.type}';
		$("#price_" + product_sid + "_" + item_sid).html(price);
		if (type != 'fixed') {ldelim}
			var currentAmount = $("#price_per_unit_"+ product_sid+"_"+item_sid+"_"+number_of_listings).attr('percentPromoAmount') * 1;
			var primaryAmount = $("#product_" + item_sid + "_primary_amount").val() * 1;
			var discountTotalAmount = $("input[name='discount_total_amount']").val() * 1;
			discountTotalAmount = discountTotalAmount - primaryAmount + currentAmount;
			if (!isNaN(discountTotalAmount)) {
				$("#product_" + item_sid + "_primary_amount").val(currentAmount);
				$("input[name='discount_total_amount']").val(discountTotalAmount);
				$('#code' + percentPromoCode + 'promoAmount').html(formatNumber(roundNumber(discountTotalAmount)));
			}
		{rdelim}
		totalPrice();
	{rdelim}
	
	function totalPrice()
	{ldelim}
		var sub_total = {$total_price};
		var total = 0;
		var tax = 0;
		$("#shoppingCartTable select").each(function(item) {ldelim}
			var id = $(this).attr('id').replace('number_of_listings_', '');
			var numberOfListings = $(this).val();
			var price = $("#price_per_unit_"+id+"_"+numberOfListings).val()*1;
			if (price < 0)
				price = 0;
			sub_total += price;
		{rdelim});

		sub_total = roundNumber(sub_total);
		var rate = {$tax.tax_rate|default:0};
		var price_includes_tax = {$tax.price_includes_tax|default:0};
		if ({$GLOBALS.settings.enable_taxes}) {ldelim}
			tax = calcTaxAmount(sub_total, rate, price_includes_tax);
			if (price_includes_tax) {ldelim}
				total = sub_total;
			{rdelim} else {ldelim}
				total = parseFloat(sub_total) + parseFloat(tax);
			{rdelim}
			tax = formatNumber(tax);
			$("#tax_amount").html(tax);
		{rdelim} else {ldelim}
			total = sub_total;
		{rdelim}
		total = roundNumber(total);
		$("input[name='total_price']").val(total);
		$("input[name='sub_total_price']").val(sub_total);
		$("#total_price").html(formatNumber(total));
		$("#sub_total_price").html(formatNumber(sub_total));
	{rdelim}
	totalPrice();

	$(function() {
		$('#delete-promocode').click(function(event) {
			event.preventDefault();
			if (confirm('[[Are you sure you want to remove the promotion code?]]')) {
				$("input[name='action']").val('deletePromoCode')
				$("input[name='shoppingCartForm']").click();
			}
		})
	})
	</script>
{/if}