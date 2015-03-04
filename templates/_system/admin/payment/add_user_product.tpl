<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/jquery.form.js"></script>
<script type="text/javascript">
	function formSubmit() {
		var options = {
				  target: "#dialog",
				  url:  $("#addProductForm").attr("action")
				}; 
		$("#addProductForm").ajaxSubmit(options);
		return false;
	}
	{if $contract_added == 1}
		var progbar = "<img src='{$GLOBALS.site_url}/../system/ext/jquery/progbar.gif' />";
		$("#dialog").dialog('destroy').html("[[Please wait ...]]" + progbar).dialog( {ldelim}width: 200{rdelim});
		parent.document.location.reload();
	{/if}
</script>

{if $errors}
    {foreach from=$errors key=error_code item=error_message}
		<p class="error">
			{if $error_code == 'UNDEFINED_PRODUCT_SID'} [[Product ID is not defined]]{/if}
		</p>
	{/foreach}
{/if}

<form action="{$GLOBALS.site_url}/add-user-product/" method="POST" id="addProductForm" onsubmit='return formSubmit();'>
	[[Select Product]]:
	<select name="product_sid" id="product_sid" onChange="viewListingBlock()">
	{foreach from=$products item=product}
		<option value="{$product.sid}">[[{$product.name}]]</option>
	{/foreach}
	</select>
	<br/>
	{foreach from=$products item=product}
		<div id="block_{$product.sid}" style="display: none;">
			{if $product.count_listings}	
				[[Number of Listings]]:
				<select name="number_of_listings_{$product.sid}" id="number_of_listings" style="width: 50px;">
					{foreach from=$product.count_listings item=count_listings}
						<option value="{$count_listings}">[[{$count_listings}]]</option>
					{/foreach}
				</select>
			{/if}
		</div>
	{/foreach}
	<input type="hidden" name="user_sid" value="{$user_sid}" />
	<input type="hidden" name="action" value="add_product" />
	<span class="greenButtonEnd"><input type="submit" id="add" name="add" value="[[Add]]" class="greenButton" /></span>
</form>

<script>
	function viewListingBlock(){
        $("#product_sid option").each(function () {
        	$("#block_"+this.value).css('display', 'none');
          });
	
        $("#product_sid option:selected").each(function () {
           $("#block_"+this.value).css('display', 'block');
         });
	}	
	viewListingBlock();
</script>

