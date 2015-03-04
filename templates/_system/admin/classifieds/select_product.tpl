{title}[[Post {$listingType.id}]]{/title}
{if $edit_user}
	{breadcrumbs}
		<a href="{$GLOBALS.site_url}/manage-users/{$userGroupInfo.id|lower}/">
			[[Manage {if $userGroupInfo.id == 'Employer' || $userGroupInfo.id == 'JobSeeker'}{$userGroupInfo.name}s{else}'{$userGroupInfo.name}' Users{/if}]]
		</a>
		&#187; <a href="{$GLOBALS.site_url}/edit-user/?user_sid={$userSID}">[[Edit User]]</a>
		&#187; <a href="{$GLOBALS.site_url}/add-listing/?listing_type_id={$listingType.id|lower}&username={$username}&edit_user=1">[[Add New {$listingType.name}]]</a>
		&#187; [[Select Product]]
	{/breadcrumbs}
{else}
	{breadcrumbs}
		<a href="{$GLOBALS.site_url}/manage-{$listingType.link}/">
			[[Manage {$listingType.name}s]]
		</a>
		&#187; <a href="{$GLOBALS.site_url}/add-listing/?listing_type_id={$listingType.id|lower}">
		[[Add New {$listingType.name}]]
		</a>
		&#187; [[Select Product]]
	{/breadcrumbs}
{/if}
<h1><img src="{image}/icons/linedpaper32.png" border="0" alt="" class="titleicon"/> [[Add New {$listingType.name}]] </h1>
{foreach from=$errors key=error item=message}
	{if $error eq "USER_NOT_FOUND"}
		<p class="error">[[User '$username' not found]]</p>
	{elseif $error eq  "PRODUCT_NOT_SELECTED" && $products}
		<p class="error">[[Please select a product]]</p>
	{/if}
{/foreach}
<fieldset style="width: 500px;">
	<legend>[[Select Product]]</legend>
	<form method="get" action="{$GLOBALS.site_url}/add-listing/">
	<input type="hidden" name="listing_type_id" value="{$listingType.id|lower}" />
	<input type="hidden" name="username" value="{$username}" />
	<input type="hidden" name="action" value="productVerify" />
	<input type="hidden" name="edit_user" value="{$edit_user}" />
	<table>
		<tr>
			<td>
				{foreach from=$products item=product}
					<input type="radio" name="product_sid" value="{$product.sid}" id="product-{$product.sid}" /><label for="product-{$product.sid}"> &nbsp; [[{$product.name}]]</label><br/>
				{foreachelse}
					<p class="error">[[There are no products allowing to post the selected Listing Type for the selected User Group]].</p>
				{/foreach}
			</td>
		</tr>
		<tr>
			<td colspan="3" style="text-align: right;"><input type="button" name="next" value="[[Back]]" class="grayButton"  onclick="location.href='{$GLOBALS.site_url}/add-listing/?listing_type_id={$listingType.id|lower}'" />&nbsp;<input type="submit" name="next" value="[[Next]]" class="grayButton"  /></td>
		</tr>
	</table>
	</form>
</fieldset>