{if $account_activated}
	<div class="message alert alert-success">
        [[Your account was successfully activated. Thank you.]]
    </div>
{/if}
{if $postingProductsOnly}
	<h1>[[Select Posting Product]]</h1>
{else}
	<h1>[[Products]]</h1>
{/if}

<div class="row" id="productsList" {if $GLOBALS.user_page_uri == "/employer-products/" ||  $GLOBALS.user_page_uri == "/jobseeker-products/"}class="productsList-nl"{/if}>
{if $availableProducts != false}
    <div class="gray-bg section">
    {foreach from=$availableProducts item=product key=id name=pr}


            <div class="col-sm-4">
                <div class="box-style-1 white-bg object-non-visible animated object-visible fadeInUpSmall" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
                <div class="products {cycle values = 'evenrow,oddrow' advance=true}">
                    <div class="productInfo">
                        <i class="fa fa-briefcase"></i>
                        <h2>[[{$product.name}]]</h2>
                        <p>[[{$product.short_description}]]</p>
                        <a href="{$GLOBALS.site_url}/product-details/?product_sid={$product.sid}">[[Product details]]</a>

                    </div>
                    <div class="productLinks">
                        <p class="productsPrice">
                            {capture assign="productPrice"}{tr type="float"}{$product.price}{/tr}{/capture}
                            {if $product.pricing_type == 'volume_based'}
                                [[Starting at]] <span class="strong">{currencyFormat amount=$productPrice}</span>
                            {elseif $product.period}
                                {if $product.period_name == 'unlimited'}
                                    [[Never Expire]]
                                {else}
                                    <span class="strong">{currencyFormat amount=$productPrice}</span> [[per]] <span class="strong">{$product.period} {if $product.period > 1 }[[{$product.period_name|capitalize}s]]{else}[[{$product.period_name|capitalize}]]{/if}</span>
                                {/if}
                            {else}
                                <span class="strong">{currencyFormat amount=$productPrice}</span>
                            {/if}
                        </p>
                        <input type="button" value="[[Buy]]" class="btn btn-default btn-sm" onclick="location.href='{$GLOBALS.site_url}/product-details/?product_sid={$product.sid}'" />
                        {if $GLOBALS.settings.allow_to_post_before_checkout == 1 && ($product.product_type == 'post_listings' || $product.product_type == 'mixed_product')}
                            <form id="add-listing_{$product.sid}" method="post" action="{$GLOBALS.site_url}/add-listing/?listing_type_id={$product.listing_type_id}">
                                <input class="btn btn-default btn-sm" type="hidden" name="productSID" value="{$product.sid}" />
                                <input class="btn btn-default btn-sm" type="hidden" name="listing_type_id" value="{$product.listing_type_id}" />
                                <input class="btn btn-primary btn-sm" type="submit" name="proceed_to_posting" value="[[Proceed to Posting]]"  />
                            </form>
                        {/if}
                    </div>
                    </div>
                </div>
            </div>

    {/foreach}
        <div class="clearfix"></div>
    </div>
{else}
    <div class="col-xs-12">
    <div class="alert alert-danger">[[There is no any Product]]</div>
    </div>
{/if}
</div>
