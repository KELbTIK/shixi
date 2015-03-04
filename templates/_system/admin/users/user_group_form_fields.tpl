<table>
{foreach from=$form_fields key=field_id item=form_field}
	{if !$switchColumns}
		{if $form_field.id == 'after_registration_redirect_to'}
			{assign var="switchColumns" value=true}
		{/if}
		<tr>
			<td valign="top" width="42%">[[{$form_field.caption}]]</td>
			<td>{if $form_field.is_required}<span class="color-red">*</span>{/if}</td>
			{if $form_field.id == 'after_registration_redirect_to'}
				<td>{input property=$form_field.id template="list_empty.tpl"}
					<div id="after_registration_redirect_to_instruction" style="display: none; font-size: 11px; margin-top:5px;">
						[[If a user group doesn't have any default products allowing to post users will be redirected to the Products page first]]
					</div>
				</td>
			{else}
				<td>{input property=$form_field.id}</td>
			{/if}
		</tr>
	{else}
		{* continue: notifications will appear below *}
	{/if}
{/foreach}
</table>

{* >>>> NOTIFICATIONS >>>> *}
<div id="mediumButton" class="setting_button">[[Notification Settings]]<div class="setting_icon"><div id="accordeonClosed"></div></div></div>
<div  class="setting_block" style="display: none">
	<table>
		<tr>
			<td colspan="3" style='font-size:11px'>
				* [[These settings will be applied by default for newly registered users of this group]].<br/>
				* [[Select "None" if you want to disable sending of notification]].</td>
		</tr>

		{foreach from=$notifications item="notificationsByGroup" key="notificationGroupID"}
			<tr>
				<td colspan="3" style='font-size:16px;font-weight: bold;'>[[{$notificationGroups.$notificationGroupID}]]:</td>
			</tr>
			{foreach from=$notificationsByGroup key="notificationID" item="notification"}
				{assign var="form_field" value=$form_fields.$notificationID}
				{if $form_field.type == 'integer'}
					<tr>
						<td>&nbsp;</td>
						<td class="notifications">{input property=$form_field.id}</td>
						<td>[[Days before]]</td>
					</tr>
				{elseif $user_group_info.id != 'JobSeeker' && ($form_field.id eq 'notify_on_application_approve' || $form_field.id eq 'notify_on_application_reject')}
					{* continue: do not show application notifications for Employer Group *}
				{else}
					<tr>
						<td colspan="1">[[{$form_field.caption}]]{if $form_field.is_required}<span class="required">*</span>{/if}</td>
						<td colspan="2">
							{input property=$form_field.id template="list_none.tpl"}
							{if $user_group_info[$notificationID] && $user_group_info[$notificationID] != 'DoNotSend'}
								<a href="{$GLOBALS.site_url}/edit-email-templates/{$notificationGroupID}/{$user_group_info[$notificationID]}" target="_blank" title="[[Edit]] {$form_field.caption}" class="edit-email-template"></a>
							{/if}
						</td>
					</tr>
				{/if}

			{/foreach}

		{/foreach}
	</table>
</div>
{* <<<< NOTIFICATIONS <<<< *}


<script type="text/javascript">
	$(".setting_button").click(function(){
		var butt = $(this);
		$(this).next(".setting_block").slideToggle("normal", function(){
			if ($(this).css("display") == "block") {
				butt.children(".setting_icon").html("<div id='accordeonOpen'></div>");
			} else {
				butt.children(".setting_icon").html("<div id='accordeonClosed'></div>");
			}
		});
	});

	var afterRegistrationElement = $(".searchList[name|=after_registration_redirect_to]");

	if (afterRegistrationElement.val() == 'posting_page') {
		$("#after_registration_redirect_to_instruction").show();
	}
	afterRegistrationElement.change(function() {
		if (this.value == 'posting_page') {
			$("#after_registration_redirect_to_instruction").show();
		} else {
			$("#after_registration_redirect_to_instruction").hide();
		}
	});

</script>