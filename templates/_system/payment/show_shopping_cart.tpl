{if $products_number > 0}
	<div id="shoppingCart" class="pull-right">
		<a href="{$GLOBALS.site_url}/shopping-cart/"><i class="fa fa-cart-arrow-down"></i></a>
		&nbsp;{capture assign="productsPrice"}{tr type="float"}{$total_price}{/tr}{/capture}{currencyFormat amount=$productsPrice}
	</div>
{/if}