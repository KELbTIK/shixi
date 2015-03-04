{breadcrumbs}<a href="{$GLOBALS.site_url}/manage-users/{$user_group_info.id|lower}/?restore=1">[[Manage {if $user_group_info.id == 'Employer' || $user_group_info.id == 'JobSeeker'}{$user_group_info.name}s{else}'{$user_group_info.name}' Users{/if}]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-user/?user_sid={$user_sid}">[[Edit {$user_group_info.name}</a> &#187; Products]]{/breadcrumbs}
<script type="text/javascript">
var progbar = "<img src='{$GLOBALS.user_site_url}/system/ext/jquery/progbar.gif'>";
$(function() {
	$(".addProduct").click(function(){
		$("#dialog").dialog('destroy');
		$("#dialog").attr({ title: "[[Loading]]"});
		$("#dialog").html(progbar).dialog({ width: 180});
		var link = $(this).attr("href");
		$.get(link, function(data){
			$("#dialog").dialog('destroy');
			$("#dialog").attr({ title: "[[Add a new product]]"});
			$("#dialog").dialog({
				width: 560,
				close: function(event, ui) {
					if(parentReload == true) {
						parent.document.location.reload();
				}}
			}).html(data);
		});
		return false;
	});
});
function deleteProduct(link) 
{
	if (confirm('[[Are you sure you want to delete this user product?]]'))
		location.href=link;
}
</script>

<h1><img src="{image}/icons/shoppingcart32.png" border="0" alt="" class="titleicon"/>[[Manage User Products]]</h1>
<p><a href="{$GLOBALS.site_url}/add-user-product/?user_sid={$user_sid}" target="_blank" class="addProduct grayButton">[[Add a new product]]</a></p>

<div id="dialog" style="display: none"></div>
<table>
	<thead>
		<tr>
			<th>[[Product Name]]</th>
			<th>[[Activation Date]]</th>
			<th>[[Expiration / Renewal Date]]</th>
			<th>[[Stats]]</th>
			<th>[[Status]]</th>
			<th>[[Actions]]</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$contracts item=contract}
			<tr class="{cycle values = 'evenrow,oddrow'}">
				<td>[[{$contract.product.name}]]</td>
				<td>{tr type="date"}{$contract.creation_date}{/tr}</td>
				<td>{if $contract.expired_date}{tr type="date"}{$contract.expired_date}{/tr}{else}[[Unlimited]]{/if}</td>
				<td>
					{if $contract.listingAmount}
						{foreach from=$contract.listingAmount item=listingAmount}
							<div>[[{$listingAmount.name}s Left to Post]]: [[{$listingAmount.listingsLeft}]]</div>
						{/foreach}
					{/if}
					{if $contract.availableViews}
						{foreach from=$contract.availableViews item=availableViews}
							<div>[[{$availableViews.name}s Left to View]]: [[{$availableViews.viewsLeft}]]</div>
						{/foreach}
					{/if}
					{if $contract.availableContactViews}
						{foreach from=$contract.availableContactViews item=availableContactViews}
							<div>[[{$availableContactViews.name} Contact details left to view]]: {if $availableContactViews.viewsLeft === 0}0{else}[[{$availableContactViews.viewsLeft}]]{/if}</div>
						{/foreach}
					{/if}
				</td>
				<td>[[{$contract.status|capitalize}]]</td>
				<td>
					<input type="button" name="button" value="[[Remove]]" class="deletebutton" onclick="deleteProduct('{$GLOBALS.site_url}/user-products/?action=remove&user_sid={$user_sid}&contract_id={$contract.id}');">
					{if $contract.status == 'pending'}
						<input type="button" name="button" value="[[Activate]]" class="grayButton" onclick="location.href='{$GLOBALS.site_url}/user-products/?action=activate&user_sid={$user_sid}&contract_id={$contract.id}'">
					{/if}
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>