<script type="text/javascript">

	function windowMessage(message) {
		$("#messageBox").dialog( 'destroy' ).html(message);
		$("#messageBox").dialog({
			width: 370,
			height: 170,
			title: '[[Error]]',
			buttons: {
				OK: function(){
					$(this).dialog('close');
				}
			}
			
		}).dialog('open');
		return false;
	}
</script>

{capture assign="trToDelete"}[[Are you sure you want to delete this product?]]{/capture}
{capture assign="trToCannotActivateProduct"}[[The product cannot be activated. Please change the availability date.]]{/capture}
{capture assign="trToProductForEmployers"}[[The product cannot be activated. This product is only for Employers. Please change the User Group.]]{/capture}
<div id="messageBox"></div>

{breadcrumbs}[[Products]]{/breadcrumbs}
<h1><img src="{image}/icons/shoppingcart32.png" border="0" alt="" class="titleicon"/>[[Products]]</h1>
<p><a href="{$GLOBALS.site_url}/add-product/" class="grayButton">[[Add a new product]]</a></p>
{if $errors}
	{foreach from=$errors key=error_code item=error_message}
		<p class="error">
			{if $error_code == 'PRODUCT_IS_IN_USE'} [[This product is in use. To delete the product, you need to first remove it from invoices and user subscriptions using it.]]{/if}
		</p>
	{/foreach}
{/if}
<div class="box" id="displayResults">
	<div class="box-header"><br/></div>
	<div class="innerpadding">
		<div id="displayResultsTable">
			<table width="100%">
				<thead>
					<tr>
						<th>[[ID]]</th>
						<th>[[Name]]</th>
						<th>[[Type]]</th>
						<th>[[User Group]]</th>
						<th>[[Price]]</th>
						<th>[[Subscribed Users]]</th>
						<th>[[Listings Posted]]</th>
						<th>[[Status]]</th>
						<th colspan="4" class="actions" width="1%">[[Actions]]</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$products item=product}
					<tr class="{cycle values = 'evenrow,oddrow'}">
						<td>{$product.sid}</td>
						<td><strong>[[{$product.name|escape:'html'}]]</strong></td>
						<td>{$product.product_type}</td>
						<td>[[{$product.user_group.name}]]</td>
						<td>
							{capture assign="productPrice"}{tr type="float"}{$product.price}{/tr}{/capture}
							{if $product.pricing_type == 'volume_based'}
								[[Starting at]] {currencyFormat amount=$productPrice}
							{elseif $product.period}
								{if $product.period_name == 'unlimited'}
									{currencyFormat amount=$productPrice}
								{else}
									{currencyFormat amount=$productPrice} [[per]] {$product.period} {if $product.period > 1 }[[{$product.period_name|capitalize}s]]{else}[[{$product.period_name|capitalize}]]{/if}
								{/if}
							{else}
								{currencyFormat amount=$productPrice}
							{/if}
						</td>
						<td>{$product.subscribed_users}</td>
						<td>{$product.number_of_postings}</td>
						<td>{if $product.active == 1}[[Active]]{else}[[Not Active]]{/if}</td>

						{if $product.active == 1}
							<td nowrap="nowrap"><input type="button" value="[[Deactivate]]" class="deletebutton" onclick="location.href='{$GLOBALS.site_url}/products/?action=deactivate&sid={$product.sid}'"/></td>
						{else}
							<td nowrap="nowrap"><input type="button" value="[[Activate]]" class="editbutton greenbtn" {if $product.expired}onclick="windowMessage('{$trToCannotActivateProduct|escape}');"{elseif $product.invalid_user_group}onclick="windowMessage('{$trToProductForEmployers|escape}');"{else}onclick="location.href='{$GLOBALS.site_url}/products/?action=activate&sid={$product.sid}'"/>{/if}</td>
						{/if}
							<td nowrap="nowrap"><a href="{$GLOBALS.site_url}/edit-product/?sid={$product.sid}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
						{if $product.subscribed_users || $product.invoices}
							<td nowrap="nowrap">&nbsp;</td>
						{else}
							<td nowrap="nowrap"><a href="{$GLOBALS.site_url}/products/?action=delete&sid={$product.sid}" onClick="return confirm('{$trToDelete|escape}');" title="[[Delete]]" class="deletebutton">[[Delete]]</a></td>
						{/if}
						<td nowrap="nowrap"><input type="button" value="[[Clone]]" class="grayButton" onclick="location.href='{$GLOBALS.site_url}/clone-product/?sid={$product.sid}'"/></td>

					</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</div>
