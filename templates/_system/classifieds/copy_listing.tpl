<script language="JavaScript" type="text/javascript" src="{common_js}/picture_actions.js"></script>
<h1>[[Clone Job]]</h1>
	{if $errors}
		{foreach from=$errors item="error_data" key="error_id"}	
			{if $error_id == 'NOT_OWNER_OF_LISTING'}
				{assign var="listing_id" value=$error_data}
				<p class="error">[[You're not the owner of this posting]]</p>
			{/if}
		{/foreach}
	{else}

	{include file='field_errors.tpl'}

	<p>[[Fields marked with an asterisk (]]<font color="red">*</font>[[) are mandatory]]</p>

	<form method="post" action="" enctype="multipart/form-data" onsubmit="disableSubmitButton('submitSave');{if $form_fields.ApplicationSettings} return validateForm('copyListingForm');{/if}" id='copyListingForm'>
		{set_token_field}
		<input type="hidden" name="action" value="save_info" />
		<input type="hidden" name="listing_id" value="{$listing_id}" />
		<input type="hidden" name="contract_id" value="{$contractID}" />
		<input type="hidden" id="tmp_listing_id" name="tmp_listing_id" value="{$tmp_listing_id}" />
		{assign var=package value=$listing.package}
		{foreach from=$pages item=form_fields key=page name=editBlock}
			{if $countPages > 1 }
				<div class="page_button"><div class="page_icon">[+]</div><b>[[{$page}]]</b></div>
				<div class="page_block" style="display: none">
			{else}
				<div>
			{/if}

			{$showPic = ($smarty.foreach.editBlock.first && $pic_limit > 0) ? true : false}
			{include file="input_form_default.tpl" showPic=$showPic picValue="{$GLOBALS.site_url}/manage-pictures/?contract_id={$contractID}&amp;listing_sid={$tmp_listing_id}" }

			{if !$smarty.foreach.editBlock.last}</div>{/if}
		{/foreach}
		</div>
		<table>
			<tr>
				<td>
					<input type="submit" value="[[Save]]" class="button" id="submitSave"/>&nbsp;
				</td>
			</tr>
		</table>
	</form>
	{/if}

{literal}
<script type="text/javascript">
	$(document).ready(function() {
		url = $("#UploadPics").attr("value");
		if (url != undefined) {
			$.ajax({
				url: url,
				beforeSend: function() {
					$("#UploadPics").hide();
				},
				success: function(data){
					$("#UploadPics").html(data);
					$("#UploadPics").show();
			}});
		}
	});
	$(".page_button").click(function() {
		var butt = $(this);
		$(this).next(".page_block").slideToggle("normal", function() {
			if ($(this).css("display") == "block") {
				butt.children(".page_icon").html("[-]");
			} else {
				butt.children(".page_icon").html("[+]");
			}
		});
	});
</script>
{/literal}