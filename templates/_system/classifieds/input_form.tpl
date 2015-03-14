<script language="JavaScript" type="text/javascript" src="{common_js}/picture_actions.js"></script>
{if $account_activated}
	<div class="message alert alert-info">
		[[Your account was successfully activated. Thank you.]]
	</div>
{/if}
{title}{tr}Post {$listingTypeStructure.name}{/tr|escape:'html'}{/title}
{if $nextPage || $prevPage}
	<div class="page-intro">
		<div class="col-sm-12">
			{foreach from=$pages item=page name=page_block}
	<span class="input-form-bc">{if $page.sid == $pageSID}<b>[[{$page.page_name}]]</b>{else}{if $page.order <= $currentPage.order}<a href="{$GLOBALS.site_url}/add-listing/{$listingTypeID|escape:'html'}/{$page.page_id}/{$listingSID}">[[{$page.page_name}]]</a>{else}[[{$page.page_name}]]{/if}{/if}{if !$smarty.foreach.page_block.last} / {/if}&nbsp;</span>

{/foreach}
		</div>
		<div class="clearfix"></div>
	</div>
{/if}
<div class="clearfix"></div>
<h1>[[{$currentPage.page_name}]]</h1>

{* SOCIAL PLUGIN: AUTOFILL *}
{if $socialAutoFillData.allow}
<div id="social_autoFill" class="{$socialAutoFillData.network}_16">
	{if $socialAutoFillData.logged}
	{if $currentPage && $listing_sid}
	<a href="{$GLOBALS.site_url}/add-listing/{$listingTypeID|escape:'html'}/{$currentPage.page_id}/{$listing_sid}/?autofill" title="">[[Auto-fill resume from my {$socialAutoFillData.network} profile]]</a>
	{else}
	<a href="{$GLOBALS.site_url}/add-listing/?listing_type_id={$listingTypeID|escape:'html'}&amp;productSID={$productSID}&amp;contract_id={$contract_id}&amp;{if $proceed_to_posting}proceed_to_posting=1&amp;{/if}autofill" title="">[[Auto-fill resume from my {$socialAutoFillData.network} profile]]</a>
	{/if}
	{else}
	<a href="{$GLOBALS.site_url}/social/?network={$socialAutoFillData.network}">[[Login with Linkedin to Auto-fill resume from my {$socialAutoFillData.network} profile]]</a>
	{/if}
</div>
{/if}
{* END / SOCIAL PLUGIN: AUTOFILL *}
{include file='field_errors.tpl'}
<div class="col-sm-8 col-sm-offset-3">[[{$currentPage.description}]]</div>
<p class="col-sm-8 col-sm-offset-3">[[Fields marked with an asterisk (]]<font color="red">*</font>[[) are mandatory]]</p>
<form method="post" action="{$GLOBALS.site_url}/add-listing/{$listingTypeID|escape:'html'}/{$currentPage.page_id}/{$listingSID}" enctype="multipart/form-data" onsubmit="disableSubmitButton('submitButton');{if $form_fields.ApplicationSettings}return validateForm('addListingForm');{/if}" id="addListingForm" class="inputForm form-horizontal">
	<input type="hidden" name="productSID" value="{$productSID}">
	<input type="hidden" name="contract_id" value="{$contract_id}" />
	<input type="hidden" name="listing_type_id" value="{$listingTypeID|escape:'html'}" />
	<input type="hidden" id="listing_id" name="listing_id" value="{$listing_id}" />

	{if ($contract_id eq 0)}
		<input type="hidden" name="proceed_to_posting" value="done" />
	{/if}
{set_token_field}


{$showPic = ($pic_limit > 0 && !$prevPage) ? true : false}
{$picValue = (!empty($listing_sid)) ? "{$GLOBALS.site_url}/manage-pictures/?listing_sid={$listing_sid}" : "{$GLOBALS.site_url}/manage-pictures/?{if $contract_id != 0}contract_id={$contract_id}&amp;{/if}product_sid={$productSID}&amp;listing_sid={$listing_id}"}
{include file="input_form_default.tpl" showPic=$showPic picValue=$picValue }

<div class="form-group has-feedback">
	<div class="inputName col-sm-3">&nbsp;</div>
	<div class="inputField col-sm-8">
		{if $prevPage}
			<input type="button" name="action_add" value="[[Back]]" class="btn btn-default btn-sm" onclick="window.location = '{$GLOBALS.site_url}/add-listing/{$listingTypeID|escape:'html'}/{$prevPage}/{$listingSID}'" />&nbsp;&nbsp;&nbsp;
		{/if}
		<input type="hidden" name="action_add" id="hidden_action_add" value=""/>
		{if $nextPage}
			<input type="submit" name="action_add" value="[[Next]]" class="btn btn-default btn-sm" id="submitButton"/>
		{else}
			<input type="submit" name="preview_listing" value="[[Preview]]" class="btn btn-default btn-sm" id="listingPreview"/>
			{if $contract_id eq 0}
				<input type="submit" name="action_add" value="[[Proceed to Checkout]]" class="btn btn-default btn-sm" id="submitButton" />
			{else}
				<input type="submit" name="action_add" value="[[Post]]" class="btn btn-default btn-sm" id="submitButton" />
			{/if}
		{/if}
	</div>
</div>
</form>



<script type="text/javascript">
	$(document).ready(function() {
		url = $("#UploadPics").attr("value");
		loadPicturesForm(url);
	});
</script>