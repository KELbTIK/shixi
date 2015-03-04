{if $products_number > 0}
	<div id="shoppingCart">
		<a href="{$GLOBALS.site_url}/shopping-cart/"><img src="{image}shoppingCart.png" border="0" alt="[[Shopping Cart]]" title="[[Shopping Cart]]"/></a>
		&nbsp;{capture assign="productsPrice"}{tr type="float"}{$total_price}{/tr}{/capture}{currencyFormat amount=$productsPrice}
	</div>
{/if}