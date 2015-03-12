{title}{tr}Post {$listingTypeID|escape:'html'}{/tr|escape:'html'}{/title}
<script type="text/javascript">
<!--
	var listingProductChoiceErrorMessage = '[[Please select a Product]]';
	{literal}
	$(document).ready(function() {
		$("#listing-product-choice-form").validate({
			rules: {
				listing_package_id: "required"
			},
			errorLabelContainer: "#listing-product-choice-message",
			errorClass: "error",
			errorElement: "p",
			messages: {
				listing_package_id: listingProductChoiceErrorMessage
			}
		});
	});
	{/literal}
//-->
</script>
<h1>[[Select a Product]]</h1>
<form id="listing-product-choice-form" method="post" action="">
	{foreach from=$products_info item="product" name="products" key="contract_id" }
		<div class="radio object-non-visible animated object-visible fadeInUpSmall" data-animation-effect="fadeInUpSmall">
			<label for="product-{$contract_id}">
				<input type="radio" value="{$contract_id}" name="contract_id" id="product-{$contract_id}" />
				<span>[[{$product.product_name|escape:'html'}]]</span> {if $product.expired_date}([[exp: {$product.expired_date}]]){/if}
			</label>
		</div>
	{/foreach}
	<input type="hidden" name="listing_id" value="{$listing_id}" />
	<input type="hidden" name="listing_type_id" value="{$listingTypeID|escape:'html'}" />

	{if $cloneJob}<input type="hidden" name="tmp_listing_id" value="{$tmp_listing_id}" />{/if}
	<div id="listing-product-choice-message"></div>
	<p><input type="submit" value="[[Next]]" class="btn btn-group btn-default btn-sm" /></p>
</form>
