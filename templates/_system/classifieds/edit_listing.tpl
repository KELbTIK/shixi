<script language="JavaScript" type="text/javascript" src="{common_js}/picture_actions.js"></script>
<h1>[[Edit Listing]]</h1>
{if $errors}
	{foreach from=$errors item="error_data" key="error_id"}
		{if $error_id == 'MAX_FILE_SIZE_EXCEEDED'}
			<p class="error">[[Maximum file size is exceeded]]. [[Max available size]] {$post_max_size}</p>
		{elseif $error_id == 'NOT_OWNER_OF_LISTING'}
			{assign var="listing_id" value=$error_data}
			<p class="error">[[You're not the owner of this posting]]</p>
		{elseif $error_id == 'NO_SUCH_FILE'}<p class="error">[[No such file found in the system]]</p>
		{elseif $error_id == 'NOT_LOGGED_IN'}
			<p class="error">[[You are not logged in]]</p>
			[[Please log in to edit this posting. If you do not have an account, please]] <a href="{$GLOBALS.site_url}/registration/">[[Register.]]</a>
			<br/><br/>
			{module name="users" function="login"}
		{/if}
	{/foreach}
{else}
	{include file='field_errors.tpl'}
	{if $form_is_submitted && !$errors && !$field_errors}
		<p class="message">[[Your changes were successfully saved]]</p>
	{/if}
	{* SOCIAL PLUGINGS: AUTOFILL *}
	{if $socialAutoFillData.allow}
	<div id="social_autoFill" class="{$socialAutoFillData.network}_16">
		{if $socialAutoFillData.logged && $socialAutoFillData.network}
		<a href="{$GLOBALS.site_url}/edit-{$listing.type.id|lower}/?listing_id={$listing.id}&amp;autofill" title="">[[Auto-fill resume from my {$socialAutoFillData.network} profile]]</a>
		{elseif $socialAutoFillData.network}
		<a href="{$GLOBALS.site_url}/social/?network={$socialAutoFillData.network}">[[Login with {$socialAutoFillData.network} to Auto-fill resume from my {$socialAutoFillData.network} profile]]</a>
		{/if}
	</div>
	{/if}
	{* END / SOCIAL PLUGINGS: AUTOFILL *}
	[[Fields marked with an asterisk (]]<font color="red">*</font>[[) are mandatory]]<br/>
	<form method="post" action="" enctype="multipart/form-data" {if $listing.ApplicationSettings}onsubmit="return validateForm('editListingForm');"{/if} id="editListingForm" class="inputForm">
		<input type="hidden" name="action" value="save_info" />
		<input type="hidden" name="listing_id" id="listing_id" value="{$listing.id}" />

		{set_token_field}

		{if $listing.priceForUpgradeToFeatured && !$listing.featured}
			<br/><a href="{$GLOBALS.site_url}/make-featured/?listing_id={$listing.id}">[[Upgrade to Featured]]</a>
		{/if}
		{if $listing.priceForUpgradeToPriority && !$listing.priority}
			<br/><a href="{$GLOBALS.site_url}/make-priority/?listing_id={$listing.id}">[[Upgrade to Priority]]</a>
		{/if}
		{if $display_preview}
			{if $listing.type.id eq "Job"}
				{assign var='link' value='my-job-details'}
			{elseif $listing.type.id eq 'Resume'}
				{assign var='link' value='my-resume-details'}
			{/if}
		{/if}
		{assign var=package value=$listing.package}
		
		{foreach from=$pages item=form_fields key=page name=editBlock}
			{if $countPages > 1 }
				<div class="page_button"><div class="page_icon">+</div>[[{$page}]]</div>
				<div class="page_block" style="display: none">
			{else}
				<div>
			{/if}
			{$showPic = ($smarty.foreach.editBlock.first && $pic_limit > 0) ? true : false}
			{include file="input_form_default.tpl" showPic=$showPic picValue="{$GLOBALS.site_url}/manage-pictures/?listing_sid={$listing.id}"}
			{if !$smarty.foreach.editBlock.last}</div>{/if}
		{/foreach}
		
		
		</div>
		
		<fieldset>
			<div class="inputName">&nbsp;</div>
			<div class="inputReq">&nbsp;</div>
			<div class="inputField">
				<input type="submit" name="preview_listing" value="[[Preview]]" class="button" id="listingPreview"/>
				<input type="submit" value="[[Post]]" class="button" />
			</div>
		</fieldset>
	</form>
{/if}

<script type="text/javascript">
{literal}
	$(document).ready(function() {
		url = $("#UploadPics").attr("value");
		loadPicturesForm(url);
	});

	$(".page_button").click(function() {
		var butt = $(this);
		$(this).next(".page_block").slideToggle("normal", function(){
			if ($(this).css("display") == "block") {
				butt.children(".page_icon").html("-");
			} else {
				butt.children(".page_icon").html("+");
			}
		});
	});
{/literal}
</script>