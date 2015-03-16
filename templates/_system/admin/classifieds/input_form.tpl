<script  type="text/javascript" src="{common_js}/picture_actions.js"></script>
{title}{tr}Post $listingType.id{/tr|escape:'html'}{/title}
{if $edit_user}
	{breadcrumbs}
		<a href="{$GLOBALS.site_url}/manage-users/{$userGroupInfo.id|lower}">
			[[Manage {if $userGroupInfo.id == 'Employer' || $userGroupInfo.id == 'JobSeeker'}{$userGroupInfo.name}s{else}'{$userGroupInfo.name}' Users{/if}]]
		</a>
		&#187; <a href="{$GLOBALS.site_url}/edit-user/?user_sid={$userSID}">[[Edit User]]</a> 
		&#187; <a href="{$GLOBALS.site_url}/add-listing/?listing_type_id={$listingType.id}&username={$username}&edit_user=1">
			[[Add New {$listingType.id}]]
		</a> 
		&#187; [[Add New {$listingType.id}]]
	{/breadcrumbs}
{else}
	{breadcrumbs}
		<a href="{$GLOBALS.site_url}/manage-{$listingType.link}/">
			[[Manage {$listingType.name}s]]
		</a> 
		&#187; [[Add New {$listingType.name}]]
	{/breadcrumbs}
{/if}
<h1><img src="{image}/icons/linedpaper32.png" border="0" alt="" class="titleicon"/> {tr}Add New {$listingType.name}{/tr|escape:'html'} </h1>

{include file='field_errors.tpl'}

<fieldset class="wide-fieldset">
	<legend>[[Add New {$listingType.name}s]]</legend>
	<form method="post" enctype="multipart/form-data" action="{$GLOBALS.site_url}/add-listing/" id="addListingForm" class="inputForm" onsubmit="disableSubmitButton('submitAdd');">
		<input type="hidden" name="action" value="add" />
		<input type="hidden" name="listing_type_id" value="{$listingType.id|lower}" />
		<input type="hidden" id="listing_id" name="listing_id" value="{$listing_id}" />
		<input type="hidden" name="username" value="{$username}" />
		<input type="hidden" name="product_sid" value="{$product_sid}" />
		<input type="hidden" name="edit_user" value="{$edit_user}" />
		{set_token_field}
		<table>
			{foreach from=$form_fields item=form_field}
				{if $form_field.id == 'video' || $form_field.id == 'youtube'}
					{if $productInfo.video}
						<tr>
							<td class="caption-td">[[{$form_field.caption}]]</td>
							<td class="required">{if $form_field.is_required}*{/if}</td>
							<td>{input property=$form_field.id}</td>
						</tr>
					{/if}
				{elseif ($listingType.id == "Job" || $listing.type.id == "Job") && $form_field.id == "anonymous"}
					{* this empty place of 'anonymous' checkbox in 'Job' listing *}
				{elseif ($listingType.id == "Resume" || $listing.type.id == "Resume") && $form_field.id == "anonymous"}
						<tr>
							<td class="caption-td">[[{$form_field.caption}]]</td>
							<td class="required">{if $form_field.is_required}*{/if}</td>
							<td> {input property=$form_field.id}</td>
						</tr>
				{elseif $form_field.id == "access_type"}
					{if $listingType.id != "Job" && $listing.type.id != "Job"}{* *}
						<tr>
							<td class="caption-td">[[{$form_field.caption}]]</td>
							<td class="required">{if $form_field.is_required}*{/if}</td>
							<td> {input property=$form_field.id template='resume_access.tpl'}</td>
						</tr>
					{/if}
				{elseif ($listingType.id == "Job" || $listing.type.id == "Job") && $form_field.id == 'ApplicationSettings'}
						<tr>
							<td class="caption-td">[[{$form_field.caption}]]</td>
							<td class="required">{if $form_field.is_required}*{/if} </td>
							<td> {input property=$form_field.id template='applicationSettings.tpl'}</td>
						</tr>
				{elseif ($listingType.id == "Job" || $listing.type.id == "Job") && $form_field.id == 'expiration_date'}
					{capture assign="expirationField"}
					<tr>
						<td class="caption-td">[[$form_field.caption]]</td>
						<td class="required">{if $form_field.is_required}*{/if} </td>
						<td> {input property=$form_field.id template='expiration_date.tpl'}</td>
					</tr>
					{/capture}
				{elseif $form_field.type == 'location'}
						{input property=$form_field.id}
				{else}
					<tr>
						{assign var="fixInstructionsForComplexField" value=false}
						{if $form_field.type != 'complex'}
							{assign var="fixInstructionsForComplexField" value=true}
						{/if}
						{if $form_field.id == 'ListingLogo'}
							<td class="caption-td">[[{$form_field.caption}]]</td>
							<td class="required">{if $form_field.is_required}*{/if}</td>
							<td> {input property=$form_field.id template="logo_listing.tpl"}</td>
						{else}
							<td class="caption-td">[[{$form_field.caption}]]</td>
							<td class="required">{if $form_field.is_required}*{/if}</td>
							<td><div style="float: left">{input property=$form_field.id}</div>
							 {if in_array($form_field.type, array('tree', 'multilist'))}
								<div id="count-available-{$form_field.id}" class="mt-count-available"></div>
							 {/if}
							</td>
						{/if}
					</tr>
				{/if}
			{/foreach}
			{if $pic_limit > 0}
				<tr>
					<td class="caption-td"> [[Add Pictures]]</td>
					<td>&nbsp;{if $form_field.is_required}*{/if}</td>
					<td>
						<div id="loading-progbar" class="add-picture-loading" style="display:none;">
							<img class="progBarImg" src="{$GLOBALS.user_site_url}/system/ext/jquery/progbar.gif" alt="[[Please wait ...]]" /> [[Please wait ...]]
						</div>
						<div id="UploadPics" value="{$GLOBALS.site_url}/manage-pictures/?product_sid={$product_sid}&amp;listing_sid={$listing_id}"></div>
					</td>
				</tr>
			{/if}
			{if $expirationField}{$expirationField}{/if}
			<tr>
				<td colspan="3">
					<div class="floatRight">
						<input type="submit" value="[[Add]]" class="grayButton" id="submitAdd"  />
					</div>
                </td>
			</tr>
		</table>
	</form>
</fieldset>

<script type="text/javascript">

	$(document).ready(function() {
		url = $("#UploadPics").attr("value");
		loadPicturesForm(url);
	});

</script>