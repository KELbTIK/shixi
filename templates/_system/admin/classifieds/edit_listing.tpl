<script  type="text/javascript" src="{common_js}/picture_actions.js"></script>
{breadcrumbs}
	<a href="{$GLOBALS.site_url}/manage-{$listingType.link}/?restore=1">
		[[Manage {$listingType.name}s]]
	</a>
	&#187; [[Edit Listing]]
{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperpencil32.png" border="0" alt="" class="titleicon"/>[[Edit Listing]]</h1>

{if $GLOBALS.is_ajax}
	<link type="text/css" href="{$GLOBALS.user_site_url}/system/ext/jquery/themes/green/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
	<script  type="text/javascript">
		var url = "{$GLOBALS.site_url}/edit-listing/";
		$("#editListingForm").submit(function() {
			var options = {
				target: "#messageBox",
				url:  url,
				success: function(data) {
					$("#messageBox").html(data).dialog('width': '200');
				}
			};
			$(this).ajaxSubmit(options);
			return false;
		});
	</script>
{/if}

{include file='field_errors.tpl'}
<p>[[Fields marked with an asterisk (<span class="required">*</span>) are mandatory]]</p>

<p>
{if $comments_total > 0}
	<a href="{$GLOBALS.site_url}/listing-comments/?listing_id={$listing_id}">[[Comments]] ({$comments_total})</a>,
{else}
	[[Comments]] ({$comments_total}),
{/if}
{if $rate}
	<a  href="{$GLOBALS.site_url}/listing-rating/?listing_id={$listing_id}">[[Rate]] ({$rate})</a>
{else}
	[[Rate]] ({$rate})
{/if}
</p>

<fieldset class="wide-fieldset">
	<legend>[[Edit Listing]]</legend>
	<form method="post" enctype="multipart/form-data" action="" {if $form_fields.ApplicationSettings}onsubmit="return validateForm('editListingForm');"{/if} id='editListingForm'>
		<input type="hidden" id="action" name="action" value="save_info"/>
		<input type="hidden" name="listing_id" value="{$listing.id}"/>
		{set_token_field}
		<table>
			{foreach from=$form_fields item=form_field}
				{* Hide 'Reject Reason', 'Approval Status' fields, and Anonymous field for Jobs *}
				{if $form_field.id == 'reject_reason' || $form_field.id == 'status' || (!isset($form_fields.Resume) && $form_field.id == anonymous) }
				{elseif !isset($form_fields.Resume) && $form_field.id =='ApplicationSettings'}
				<tr>
					<td class="caption-td">[[$form_field.caption]]</td>
					<td class="required">&nbsp;{if $form_field.is_required}*{/if}</td>
					<td>{input property=$form_field.id template='applicationSettings.tpl'}</td>
				</tr>
				{elseif !isset($form_fields.Resume) && $form_field.id == 'expiration_date'}
					{capture assign="expirationField"}
					<tr>
						<td class="caption-td">[[$form_field.caption]]</td>
						<td class="required">{if $form_field.is_required}*{/if} </td>
						<td> {input property=$form_field.id template='expiration_date.tpl'}</td>
					</tr>
					{/capture}
				{elseif $form_field.id == "access_type"}
					{if $listingType.id == "Job" || $listing.type.id == "Job"}{* *}
					{else}
						<tr>
							<td class="caption-td">[[$form_field.caption]]</td>
							<td class="required">&nbsp;{if $form_field.is_required}*{/if}</td>
							<td>{input property=$form_field.id template='resume_access.tpl'}</td>
						</tr>
					{/if}
				{elseif $form_field.type == 'location'}
						{input property=$form_field.id}
				{else}
					{if $form_field.id == 'ListingLogo'}
						<tr>
							<td class="caption-td">[[{$form_field.caption}]]</td>
							<td class="required">&nbsp;{if $form_field.is_required}*{/if}</td>
							<td>{input property=$form_field.id template="logo_listing.tpl"}</td>
						</tr>
					{else}
						<tr>
							<td class="caption-td">[[{$form_field.caption}]]</td>
							<td class="required">&nbsp;{if $form_field.is_required}*{/if}</td>
							<td><div style="float: left;">{input property=$form_field.id}</div>
							 {if in_array($form_field.type, array('tree', 'multilist'))}
								<div id="count-available-{$form_field.id}" class="mt-count-available"></div>
							 {/if}
							</td>
						</tr>
					{/if}
				{/if}
			{/foreach}

			{if $pic_limit > 0}
				<tr>
					<td class="caption-td"> [[Add Pictures]] </td>
					<td>&nbsp;</td>
					<td>
						<div class="inputField">
							<div id="loading" class="add-picture-loading" style="display:none;">
								<img class="progBarImg" src="{$GLOBALS.user_site_url}/system/ext/jquery/progbar.gif" alt="[[Please wait ...]]" /> [[Please wait ...]]
							</div>
							<div id="UploadPics" value="{$GLOBALS.site_url}/manage-pictures/?listing_sid={$listing.id}"></div>
						</div>
					</td>
				</tr>
			{/if}

			{if $expirationField}{$expirationField}{/if}

			<tr>
				<td colspan="3">
					<div class="floatRight">
						<input type="submit" id="apply" value="[[Apply]]" class="greenButton"/>
						<input type="submit" value="[[Save]]" class="greenButton" />
					</div>
				</td>
			</tr>
		</table>
	</form>
</fieldset>

<script type="text/javascript">

	$('#apply').click(
		function(){
			$('#action').attr('value', 'apply_info');
		}
	);

	$(document).ready(function() {
		url = $("#UploadPics").attr("value");
		loadPicturesForm(url);
	});

</script>