<script language="JavaScript" type="text/javascript" src="{common_js}/pagination.js"></script>
{capture name="trResend"}[[Resend]]{/capture}
{capture name="trCancel"}[[Cancel]]{/capture}
{capture name="trAreYouSureToResend"}[[Are you sure you want to resend selected emails?]]{/capture}
{capture name="displayJobProgressBar"}<img style="vertical-align: middle;" src="{$GLOBALS.user_site_url}/system/ext/jquery/progbar.gif" alt="[[Please wait ...]]" /> [[Please wait ...]]{/capture}

<script type="text/javascript">

	var doNotShowAttachmentNotificationDialog = parseInt("{$doNotShowAttachmentNotification}");

	function showDialogMessage(message) {
		var dialog = $("#messageBox");
		dialog.dialog('destroy');
		dialog.attr({ title: "" });
		dialog.html(message).dialog({ width: 300 });
	}

	function checkCurrentEmailAndGo(emailSID) {
		var checkbox = $("#tr_email_" + emailSID).find("input:checkbox");
		checkbox.attr("checked", "checked");
		isPopUp('{$paginationInfo.translatedText.chooseAction|escape:"javascript"}','{$paginationInfo.translatedText.delete|escape:"javascript"}');
	}

	function isPopUp(chooseItem, textToDelete) {
		if(! $("input:checked").length) {
			showDialogMessage(chooseItem);
		} else {
			if (confirm('{$smarty.capture.trAreYouSureToResend|escape:"javascript"}')){
				if (doNotShowAttachmentNotificationDialog) {
					submitEmailListForm();
				} else {
					showAttachmentNotifAndSubmitForm();
				}
			}
		}
	}

	function showAttachmentNotifAndSubmitForm() {
		$('#attachmentNotification').dialog({
			title: "",
			width: '500px',
			modal: true,
			resizable: false,
			buttons: [
				{
					text: "{$smarty.capture.trCancel|escape:"quotes"}",
					click: function() {
						$(this).dialog("close");
					}
				},
				{
					text: "{$smarty.capture.trResend|escape:"quotes"}",
					click: function() {
						var doShow = $("#doNotShowAttachmentNotificationDialog:checked").length;
						document.getElementById('doNotShowAttachmentNotification').value = doShow;
						submitEmailListForm();
						$(this).dialog("close");
					}
				}
			]
		});
	}

	function submitEmailListForm() {
		document.getElementById('action_name').value = 'resend';
		var form = document.emailListForm;
		form.submit();
	}
</script>

{if !$doNotShowAttachmentNotification}
	<div id="attachmentNotification" style="display: none;">
		<form action="" method="POST" id="resendEmailDialogForm">
			<fieldset>
				<span>[[Please note that attachments will not be resent because they are not stored in the database.]]</span><br/>
				<input type="checkbox" id="doNotShowAttachmentNotificationDialog" name="doNotShowAttachmentNotificationDialog" value="1"/>
				<label for="doNotShowAttachmentNotificationDialog">[[Do not show anymore]]</label>
			</fieldset>
		</form>
	</div>
{/if}

{if !empty($errors)}
	{foreach from=$errors item="error"}
		<p class="error">[[{$error}]]</p>
	{/foreach}
{/if}
{if !empty($message)}
	<p class="note">[[{$message}]]</p>
{/if}

<div class="clr"><br/></div>

<div class="box" id="displayResults">
	<form method="post" name="emailListForm">
		<input type="hidden" id="action_name" name="action_name" value="view_log"/>
		<input type="hidden" name="searchFields" value="{$searchFields}"/>
		<input type="hidden" id="doNotShowAttachmentNotification" name="doNotShowAttachmentNotification" value="{$doNotShowAttachmentNotification}"/>

		<div class="box-header">
			{include file="../pagination/pagination.tpl" layout="header"}
		</div>
		<div class="innerpadding">
			<div id="displayResultsTable">
				<table width="100%">
					<thead>
						{include file="../pagination/sort.tpl"}
					</thead>
					<tbody>
						{foreach from=$found_emails item="found_email" name="emails_block"}
							<tr id="tr_email_{$found_email.sid}" class="{cycle values = 'evenrow,oddrow'}">
								<td><input type="checkbox" name="emails[]" value="{$found_email.sid}" id="checkbox_{$smarty.foreach.emails_block.iteration}" /></td>
								<td>{tr type="date"}{$found_email.date}{/tr}<br />{$found_email.date|date_format:"%H:%M:%S"}</td>
								<td><a href="{$GLOBALS.site_url}/email-log/display-message/" onClick="popUpWindow('{$GLOBALS.site_url}/email-log/?action_name=display_message&sid={$found_email.sid}',600, 600, '[[Viewing Email Message]]'); return false;">{$found_email.subject}</a></td>
								<td>{$found_email.email}</td>
								<td>
									{if $found_email.user.username}
										{$found_email.user.username}
										{elseif $found_email.admin.username}
										{$found_email.admin.username}
										{if $found_email.admin.sid}
											([[subadmin]])
											{else}
											([[admin]])
										{/if}
										{else}
										[[Undefined]]
									{/if}
								</td>
								<td>
									{if $found_email.error_msg}
										<a href="{$GLOBALS.site_url}/email-log/display-message/" onClick="popUpWindow('{$GLOBALS.site_url}/email-log/?action_name=display_message&display_error=1&sid={$found_email.sid}',400, 200, '[[Viewing Error Message]]'); return false;"><strong>[[{$found_email.status}]]</strong></a>
									{else}
										[[{$found_email.status}]]
									{/if}
								</td>
								<td>
									<a href="" title="{$smarty.capture.trResend|escape:'html'}" class="editbutton"
									   onclick="checkCurrentEmailAndGo({$found_email.sid});return false;">[[{$smarty.capture.trResend|escape:'html'}]]</a>
								</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
		<div class="box-footer">
			<div class="pagination">
				{foreach from=$pages item=page}
					{if $page == $currentPage}
						<strong>{$page}</strong>
					{else}
						{if $page == $totalPages && $currentPage < $totalPages-3} ... {/if}
						<a href="?page={$page}{if $sorting_field ne null}&amp;sorting_field={$sorting_field}{/if}{if $sorting_order ne null}&amp;sorting_order={$sorting_order}{/if}&amp;items_per_page={$items_per_page}{$searchFields}">{$page}</a>
						{if $page == 1 && $currentPage > 4} ... {/if}
					{/if}
				{/foreach}
			</div>
		</div>
	</form>
</div>