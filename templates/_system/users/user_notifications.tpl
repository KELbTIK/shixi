<h1>[[My Notifications]]</h1>
{if !empty($errors)}
	{include file="field_errors.tpl"}
{else}
	{if $isSaved}
		<P class="message">[[Your notifications have been saved]]</P>
	{/if}
	<form method="post" action="">
		<input type="hidden" name="action" value="save" />
		{foreach from=$userNotificationGroups item="userNotificationGroup" key="userNotificationGroupID"}
			{if !empty($userNotifications.$userNotificationGroupID)}
				<h4>[[{$userNotificationGroup}]]</h4>
				{foreach from=$userNotifications.$userNotificationGroupID item="userNotification" key="notificationID"}
					{if !$approve_setting && ($userNotification.id eq 'notify_on_listing_approve' || $userNotification.id eq 'notify_on_listing_reject')}
						{* continue: if listings must NOT be approved by admin we are skipping these notifications *}
					{elseif $GLOBALS.current_user.group.id != "JobSeeker"
						&& ($userNotification.id eq 'notify_on_application_reject' || $userNotification.id eq 'notify_on_application_approve')}
						{* continue: application notifications only for JobSeekers *}
					{elseif in_array($userNotification.id, array('notify_listing_expire_date_days', 'notify_subscription_expire_date_days'))}
						{* continue: this fields will be later *}
					{else}
						<fieldset>
							<div class="notCheck">{input property=$notificationID}</div>
							<div class="notDesc">[[{$userNotification.caption}]]</div>
							{if in_array($userNotification.id, array('notify_listing_expire_date', 'notify_subscription_expire_date'))}
								{capture name="notificationID"}{$userNotification.id}_days{/capture}
								<div class="notCheck notif-date-days">{input property=$smarty.capture.notificationID}</div>
								<div class="notDesc">[[Days before]]</div>
							{/if}
						</fieldset>
					{/if}
				{/foreach}
			{/if}
		{/foreach}
		<fieldset>
			<div class="notDesc"><input type="submit" class="button" value="[[Save:raw]]" /></div>
		</fieldset>
	</form>
{/if}
