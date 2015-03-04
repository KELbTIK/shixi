<label for="products">[[Products]]{$var_param}</label>
<select id="products" name="products[]" multiple="multiple">
    <option
        {foreach from=$param.products item=planSaved}
            {if $planSaved == 0}selected{/if}
        {/foreach}
            value="0">[[Not Subscribed]]</option>
    {foreach from=$products item=product}
        <option
        {foreach from=$param.products item=productSaved}
            {if $product.sid == $productSaved}selected{/if}
        {/foreach}
            value="{$product.sid}">[[{$product.name}]]</option>
    {/foreach}
</select>
