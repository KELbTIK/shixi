{if $account_activated}
	<p class="message">
		[[Your account was successfully activated. Thank you.]]
	</p>
{/if}
<div class="my-account-head"><h1>[[My Account]]</h1></div>
<div class="my-account">
	<ul>
		<li> <a href="{$GLOBALS.site_url}/edit-profile/"><img src="{image}account/myprofile_ico.png" alt=""/><br/>[[My Profile]]</a></li>

		{capture assign="myListingsBlock"}
			{foreach from=$listingTypesInfo item="listingTypeInfo"}
				{if ($acl->isAllowed('post_'|cat:$listingTypeInfo.id)) || $listingTypeInfo.id == 'Job'}
					<li>
						<a href="{$GLOBALS.site_url}/my-listings/{$listingTypeInfo.id}/">
							<img src="{image}account/myresumes_ico.png" alt=""/><br/>
							{if in_array($listingTypeInfo.id, array('Job', 'Resume'))}[[My {$listingTypeInfo.name}s]]{else}[[My {$listingTypeInfo.name} Listings]]{/if}
						</a>
					</li>
				{/if}
			{/foreach}
		{/capture}
		{if $GLOBALS.current_user.subuser}
			{if $acl->isAllowed('subuser_add_listings', $GLOBALS.current_user.subuser.sid) || $acl->isAllowed('subuser_manage_listings', $GLOBALS.current_user.subuser.sid)}
				{$myListingsBlock}
			{/if}
		{else}
			{$myListingsBlock}
		{/if}

		{if $GLOBALS.current_user.subuser}
			{if $acl->isAllowed('subuser_manage_subscription', $GLOBALS.current_user.subuser.sid)}
				<li> <a href="{$GLOBALS.site_url}/my-products/"><img src="{image}account/subscription_ico.png" alt=""/><br/>[[My Products]]</a></li>
				<li> <a href="{$GLOBALS.site_url}/my-invoices/"><img src="{image}account/billing_hist_ico.png" alt=""/><br/>[[My Invoices]]</a></li>
			{/if}
		{else}
			<li> <a href="{$GLOBALS.site_url}/my-products/"><img src="{image}account/subscription_ico.png" alt=""/><br/>[[My Products]]</a></li>
			<li> <a href="{$GLOBALS.site_url}/my-invoices/"><img src="{image}account/billing_hist_ico.png" alt=""/><br/>[[My Invoices]]</a></li>
		{/if}

		{if $acl->isAllowed('save_resume')}
			<li>
				<a href="{$GLOBALS.site_url}/saved-resumes/">
					<img src="{image}account/saved_listings_ico.png" alt=""/>
					<br/>
					[[Saved Resumes]]
				</a>
			</li>
		{elseif $acl->getPermissionParams('save_resume') == "message"}
			<li>
				<a onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_resume', 400, '[[Saved Resumes]]', true, false); return false;"
						href="{$GLOBALS.site_url}/access-denied/?permission=save_resume">
					<img src="{image}account/saved_listings_ico.png" alt=""/>
					<br/>
					[[Saved Resumes]]
				</a>
			</li>
		{/if}

		{if $acl->isAllowed('use_resume_alerts')}
			<li> <a href="{$GLOBALS.site_url}/resume-alerts/"><img src="{image}account/resume_alerts_ico.png" alt=""/><br/>[[Resume Alerts]]</a></li>
		{elseif $acl->getPermissionParams('use_resume_alerts') == "message"}
			<li> <a href="{$GLOBALS.site_url}/resume-alerts/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_resume_alerts', 300, '[[Resume Alerts]]'); return false;"><img src="{image}account/resume_alerts_ico.png" alt=""/><br/>[[Resume Alerts]]</a></li>
		{/if}

		{if $acl->isAllowed('save_searches')}
			<li> <a href="{$GLOBALS.site_url}/saved-searches/"><img src="{image}account/save_ico.png" alt=""/><br/>[[Saved Searches]]</a></li>
		{elseif $acl->getPermissionParams('save_searches') == "message"}
			<li> <a href="{$GLOBALS.site_url}/saved-searches/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_searches', 300, '[[Saved searches]]'); return false;"><img src="{image}account/save_ico.png" alt=""/><br/>[[Saved Searches]]</a></li>
		{/if}

		{if $GLOBALS.current_user.subuser}
			{if $acl->isAllowed('subuser_add_listings', $GLOBALS.current_user.subuser.sid) || $acl->isAllowed('subuser_manage_listings', $GLOBALS.current_user.subuser.sid)}
				<li> <a href="{$GLOBALS.site_url}/system/applications/view/"><img src="{image}account/applications_track_ico.png" alt=""/><br/>[[Application Tracking]]</a></li>
			{/if}
		{else}
			<li> <a href="{$GLOBALS.site_url}/system/applications/view/"><img src="{image}account/applications_track_ico.png" alt=""/><br/>[[Application Tracking]]</a></li>
		{/if}

		{if $GLOBALS.current_user.subuser}
			{if $acl->isAllowed('use_screening_questionnaires', $GLOBALS.current_user.subuser.sid)}
				<li> <a href="{$GLOBALS.site_url}/screening-questionnaires/"><img src="{image}account/questionnaires.png" alt=""/><br/>[[Screening Questionnaires]]</a></li>
			{elseif $acl->getPermissionParams('use_screening_questionnaires') == "message"}
				<li><a href="{$GLOBALS.site_url}/screening-questionnaires/"  onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_screening_questionnaires', 300, '[[Screening Questionnaires]]'); return false;">[[Screening Questionnaires]]</a></li>
			{/if}
		{else}
			{if $acl->isAllowed('use_screening_questionnaires')}
				<li> <a href="{$GLOBALS.site_url}/screening-questionnaires/"><img src="{image}account/questionnaires.png" alt=""/><br/>[[Screening Questionnaires]]</a></li>
			{elseif $acl->getPermissionParams('use_screening_questionnaires') == "message"}
				<li><a href="{$GLOBALS.site_url}/screening-questionnaires/"  onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_screening_questionnaires', 300, '[[Screening Questionnaires]]'); return false;">[[Screening Questionnaires]]</a></li>
			{/if}
		{/if}

		{if $acl->isAllowed('create_sub_accounts') && !$GLOBALS.current_user.subuser}
			<li> <a href="{$GLOBALS.site_url}/sub-accounts/"><img src="{image}account/subaccounts.png" alt=""/><br/>[[Sub Accounts]]</a></li>
		{elseif $acl->getPermissionParams('create_sub_accounts') == "message"}
			<li> <a href="{$GLOBALS.site_url}/sub-accounts/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=create_sub_accounts', 300, '[[Sub Accounts]]'); return false;"><img src="{image}account/subaccounts.png" alt=""/><br/>[[Sub Accounts]]</a></li>
		{/if}

		{if !$GLOBALS.current_user.subuser}
			<li> <a href="{$GLOBALS.site_url}/user-notifications/"><img src="{image}account/notifications_ico.png" alt=""/><br/>[[My Notifications]]</a></li>
		{/if}

		<li><a href="{$GLOBALS.site_url}/my-reports/"><img src="{image}account/icon-reports.png" alt=""/><br/>[[My Reports]]</a></li>

		{if $acl->isAllowed('use_private_messages')}
			<li> <a href="{$GLOBALS.site_url}/private-messages/inbox/"><img src="{image}account/message_ico.png" alt=""/><br/>[[Private messages]]</a>
				<div class="pm-sub-menu">
					&#187; <a href="{$GLOBALS.site_url}/private-messages/inbox/">[[Inbox]] ({$GLOBALS.current_user.new_messages})</a>
					&#187; <a href="{$GLOBALS.site_url}/private-messages/outbox/">[[Outbox]]</a>
				</div>
			</li>
		{elseif $acl->getPermissionParams('use_private_messages') == "message"}
			<li><img src="{image}account/message_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/private-messages/inbox/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 300, '[[Private messages]]'); return false;">[[Private messages]]</a>
				<div class="pm-sub-menu">
					&#187; <a href="{$GLOBALS.site_url}/private-messages/inbox/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 300, '[[Inbox]]'); return false;">[[Inbox]] ({$GLOBALS.current_user.new_messages})</a>
					&#187; <a href="{$GLOBALS.site_url}/private-messages/outbox/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 300, '[[Outbox]]'); return false;">[[Outbox]]</a>
				</div>
			</li>
		{/if}
	</ul>
	<div id="adSpaceAccount">
		<div id="my-account-stats">{module name="statistics" function="my_reports"}</div>
		{module name="static_content" function="show_static_content" pageid="AccountEmpAdSpace"}
	</div>
	<div class="clr"><br/><br/></div>
</div>