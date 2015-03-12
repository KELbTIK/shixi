<h1>[[My Notifications]]</h1>
{if !empty($errors)}
	{include file="field_errors.tpl"}
{else}
	{if $isSaved}
		<div class="message alert alert-success">[[Your notifications have been saved]]</div>
	{/if}
	<form method="post" action="">
		<input type="hidden" name="action" value="save" />
		{foreach from=$userNotificationGroups item="userNotificationGroup" key="userNotificationGroupID"}
			{if !empty($userNotifications.$userNotificationGroupID)}
				<h4>[[{$userNotificationGroup}]]</h4>
                <div class="separator-2"></div>
				{foreach from=$userNotifications.$userNotificationGroupID item="userNotification" key="notificationID"}
					{if !$approve_setting && ($userNotification.id eq 'notify_on_listing_approve' || $userNotification.id eq 'notify_on_listing_reject')}
						{* continue: if listings must NOT be approved by admin we are skipping these notifications *}
					{elseif $GLOBALS.current_user.group.id != "JobSeeker"
						&& ($userNotification.id eq 'notify_on_application_reject' || $userNotification.id eq 'notify_on_application_approve')}
						{* continue: application notifications only for JobSeekers *}
					{elseif in_array($userNotification.id, array('notify_listing_expire_date_days', 'notify_subscription_expire_date_days'))}
						{* continue: this fields will be later *}
					{else}

                            <div class="form-group">
                                <div class="notCheck checkbox">
                                    <label>
                                        {input property=$notificationID}
                                        [[{$userNotification.caption}]]
                                    </label>
                                    {if in_array($userNotification.id, array('notify_listing_expire_date', 'notify_subscription_expire_date'))}
                                        {capture name="notificationID"}{$userNotification.id}_days{/capture}
                                        <span class="form-inline">
                                            &nbsp; {input property=$smarty.capture.notificationID}
                                            <label class="notDesc">[[Days before]]</label>
                                        </span>
                                    {/if}
                                </div>

                            </div>

					{/if}
				{/foreach}
			{/if}
		{/foreach}
		<div class="form-group">
			<div class="notDesc"><input type="submit" class="button btn btn-success" value="[[Save:raw]]" /></div>
		</div>
	</form>
{/if}
