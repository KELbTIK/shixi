{capture name="trSelectItemsMessasge"}[[Please, select banners first]]{/capture}
{capture name="trInformation"}[[Information]]{/capture}
{capture name="trConfirmToActivate"}[[Are you sure you want to activate selected banner(s)?]]{/capture}
{capture name="trConfirmToDeactivate"}[[Are you sure you want to deactivate selected banner(s)?]]{/capture}
{capture name="trConfirmToApprove"}[[Are you sure you want to approve selected banner(s)?]]{/capture}
{capture name="trConfirmToDelete"}[[Are you sure you want to delete selected banner(s)?]]{/capture}
<p><a href="{$GLOBALS.site_url}/add-banner/?groupSID={$bannerGroup.sid}" class="grayButton">[[Add a New Banner]]</a></p>
<div class="clr"><br/></div>
{foreach from=$errors item=error}
	[[{$error}]]
{/foreach}

<div id="dialog" style="display: none"></div>
<form method="post" name="banners_form" id="banners_form">

	<div id="banner_reject_dialog" style="display: none">
		[[Enter Reject Reason]]:
		<textarea name="rejection_reason_text" id="rejection_reason_text"></textarea>
		<div class="clr"><br/></div>
		<span class="greenButtonEnd"><input type="submit" id="banner_reject_send_button" name="banner_reject_send_button" value="[[Reject]]" class="greenButton" /></span>
	</div>
	<input type="hidden" name="rejection_reason" id="rejection_reason" value="">
	<input type="hidden" name="action" id="action" value="" />

	<input type="button" name="action" value="[[Activate]]" class="grayButton" onclick="submitForm('activate', '{$smarty.capture.trConfirmToActivate|escape:"javascript"}');" />
	<input type="button" name="action" value="[[Deactivate]]" class="grayButton" onclick="submitForm('deactivate', '{$smarty.capture.trConfirmToDeactivate|escape:"javascript"}');" />
	<input type="button" value="[[Approve]]" class="grayButton" onclick="submitForm('approve', '{$smarty.capture.trConfirmToApprove|escape:"javascript"}');"/>
	<input type="button" value="[[Reject]]" class="grayButton" onclick="rejectReason();"/>
	&nbsp;<input type="button" name="action" value="[[Delete]]" class="deletebutton" onclick="submitForm('delete_banner', '{$smarty.capture.trConfirmToDelete|escape:"javascript"}');" />

	<div class="clr"><br/></div>

	<table>
		<thead>
			<tr>
				<th><input type="checkbox" id="all_checkboxes_control" /></th>
				<th>[[ID]]</th>
				<th>[[Status]]</th>
				<th>[[Approval Status]]</th>
				<th>[[Title]]</th>
				<th>[[Link]]</th>
				<th>[[User]]</th>
				<th colspan="2" class="actions">[[Actions]]</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$banners item=banner name=banner_block}
			<tr class="{cycle values = 'evenrow,oddrow'}">
				<td><input type="checkbox" name="banners[{$banner.id}]" value="1" id="checkbox_{$smarty.foreach.banner_block.iteration}" /></td>
				<td width="30px"><a href="{$GLOBALS.site_url}/edit-banner/?bannerId={$banner.id|escape}" title="[[Edit]]">{$banner.id|escape}</a></td>
				<td width="80px"><a href="{$GLOBALS.site_url}/edit-banner/?bannerId={$banner.id|escape}" title="[[Edit]]">{if $banner.active == '1'}[[active]]{else}[[not active]]{/if}</a></td>
				<td width="80px"><a >[[{$banner.status|ucfirst}]]</a></td>
				<td width="150px"><a href="{$GLOBALS.site_url}/edit-banner/?bannerId={$banner.id|escape}">{$banner.title|escape}</a></td>
				<td width="200px"><a href="{$GLOBALS.site_url}/edit-banner/?bannerId={$banner.id|escape}">{$banner.link|escape}</a></td>
				<td>{if $banner.user}<a href="{$GLOBALS.site_url}/edit-user/?user_sid={$banner.user.sid} ">{$banner.user.username}</a>{/if}</td>
				<td><a href="{$GLOBALS.site_url}/edit-banner/?bannerId={$banner.id|escape}" title="Edit" class="editbutton">[[Edit]]</a></td>
				<td>
					{capture name="delete_confirm_script"} return confirm('[[Do you want to delete]] \'{$banner.title|escape:"javascript"}\' [[banner]]?') {/capture}
					<a href="?action=delete_banner&amp;bannerId={$banner.id|escape}" onclick="{$smarty.capture.delete_confirm_script|escape:"html"}" title="[[Delete]]" class="deletebutton">[[Delete]]</a>
				</td>
			</tr>
			<tr>
				<td colspan="7">
					{if $banner.type == 'application/x-shockwave-flash'}
						<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://active.macromedia.com/flash2/cabs/swflash.cab#version=4,0,0,0" ID="banner"" WIDTH="{$banner.width}" HEIGHT="{$banner.height}">
						<PARAM NAME="movie" VALUE="{$bannersPath}{$banner.image_path}">
						<PARAM NAME="quality VALUE="high">
						<PARAM NAME="loop" VALUE="true">
						<EMBED FlashVars="sjb_banner_link={capture name="banner_link"}{$GLOBALS.user_site_url}/go-link/?bannerId={$banner.id}{/capture}{$smarty.capture.banner_link|escape:'url'}&sjb_banner_window={$banner.openBannerIn}" src="{$bannersPath}{$banner.image_path}" loop="true" quality="high"  WIDTH="{$banner.width}" HEIGHT="{$banner.height}" TYPE="application/x-shockwave-flash"  PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
						</EMBED>
					{else}
						{if $banner.bannerType == 'code'}
							{$banner.code}
						{else}
							<image src="{$bannersPath}{$banner.image_path}" width="{$banner.width}" height="{$banner.height}" />
						{/if}
					{/if}
				</td>
				<td colspan="2">[[Impressions]]:&nbsp;{$banner.show}<br />[[Clicks]]:&nbsp;{$banner.click}<br />[[CTR]]:&nbsp;{$banner.ctr|default:'0'|string_format:"%.3f"} %</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
    <div class="clr"><br/></div>
	<input type="button" name="action" value="[[Activate]]" class="grayButton" onclick="submitForm('activate', '{$smarty.capture.trConfirmToActivate|escape:"javascript"}');" />
	<input type="button" name="action" value="[[Deactivate]]" class="grayButton" onclick="submitForm('deactivate', '{$smarty.capture.trConfirmToDeactivate|escape:"javascript"}');" />
	&nbsp;<input type="button" name="action" value="[[Delete]]" class="deletebutton" onclick="submitForm('delete_banner', '{$smarty.capture.trConfirmToDelete|escape:"javascript"}');" />
</form>
<script>
	var total={$smarty.foreach.banner_block.total};
	$.ui.dialog.prototype.options.bgiframe = true;
	var progbar = "<img src='{$GLOBALS.site_url}/../system/ext/jquery/progbar.gif'>";
	
	function set_checkbox(param) {
		for (i = 1; i <= total; i++) {
			if (checkbox = document.getElementById('checkbox_' + i))
				checkbox.checked = param;
		}
	}
	
	$("#all_checkboxes_control").click(function() {
		set_checkbox(this.checked);
	});

	function isAnyElementSelected() {
		if ($("#banners_form input:checked").length <= 0) {
			$("#dialog").html("{$smarty.capture.trSelectItemsMessasge|escape:"javascript"}").dialog({ width: 300, title: "{$smarty.capture.trInformation|escape:"javascript"}" });
			return false;
		}
		return true;
	}
	
	function submitForm(action, confirmText) {
		if (isAnyElementSelected()) {
			if (confirmText) {
				if (!confirm(confirmText)) {
					return false;
				}
			}
			document.getElementById('action').value = action;
			var form = document.banners_form;
			form.submit();
		}
	}

	function rejectReason() {
		$("#banner_reject_dialog").dialog('destroy');
		if (isAnyElementSelected()) {
			$("#banner_reject_dialog").dialog({ title: "[[Banner Rejection]]", width: 350 });
		}
	}
	
	$(function() {
		$("#banner_reject_send_button").click(function(){
			val = $("#rejection_reason_text").val();
			$("#rejection_reason").val(val);
			$("input[name='action_name']").val('reject');
			$("#banner_reject_dialog").dialog('destroy').html("[[Please wait...]]" + progbar).dialog( { width: 200});
			document.getElementById('action').value = 'reject';
			$("form[name='banners_form']").submit();
		});
	});
</script>