{if $account_activated}
	<p class="message">
		[[Your account was successfully activated. Thank you.]]
	</p>
{/if}
<div class="MyAccount">
	<div class="MyAccountHead"><h1>[[My Account]]</h1></div>
	<!-- LEFT COLUMN MY ACCOUNT -->
	<div class="leftColumnMA">
		<ul>
			<li><img src="{image}account/myprofile_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/edit-profile/">[[My Profile]]</a></li>
			{capture assign="myListingsBlock"}
				{foreach from=$listingTypesInfo item="listingTypeInfo"}
					{if ($acl->isAllowed('post_'|cat:$listingTypeInfo.id)) || $listingTypeInfo.id == 'Job'}
						<li>
							<img src="{image}account/myresumes_ico.png" alt=""/>
							<a href="{$GLOBALS.site_url}/my-listings/{$listingTypeInfo.id}/">
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
					<li><img src="{image}account/subscription_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/my-products/">[[My Products]]</a></li>
					<li><img src="{image}account/billing_hist_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/my-invoices/">[[My Invoices]]</a></li>
				{/if}
			{else}
				<li><img src="{image}account/subscription_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/my-products/">[[My Products]]</a></li>
				<li><img src="{image}account/billing_hist_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/my-invoices/">[[My Invoices]]</a></li>
			{/if}
			{if $acl->isAllowed('save_resume')}
				<li><img src="{image}account/saved_listings_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/saved-resumes/">[[Saved Resumes]]</a></li>
			{elseif $acl->getPermissionParams('save_resume') == "message"}
				<li><img src="{image}account/saved_listings_ico.png" alt=""/>
					<a onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_resume', 400, '[[Saved Resumes]]', true, false); return false;"
						href="{$GLOBALS.site_url}/access-denied/?permission=save_resume">[[Saved Resumes]]</a>
				</li>
			{/if}
			{if $acl->isAllowed('use_resume_alerts')}
				<li><img src="{image}account/resume_alerts_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/resume-alerts/">[[Resume Alerts]]</a></li>
			{elseif $acl->getPermissionParams('use_resume_alerts') == "message"}
				<li><img src="{image}account/resume_alerts_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/resume-alerts/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_resume_alerts', 300, '[[Resume Alerts]]'); return false;">[[Resume Alerts]]</a></li>
			{/if}
			{if $acl->isAllowed('save_searches')}
				<li><img src="{image}account/save_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/saved-searches/">[[Saved Searches]]</a></li>
			{elseif $acl->getPermissionParams('save_searches') == "message"}
				<li><img src="{image}account/save_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/saved-searches/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_searches', 300, '[[Saved searches]]'); return false;">[[Saved Searches]]</a></li>
			{/if}
		</ul>
	</div>
	<!-- END LEFT COLUMN MY ACCOUNT -->

	<!-- RIGHT COLUMN MY ACCOUNT -->
	<div class="rightColumnMA">
		<ul>
			{if $GLOBALS.current_user.subuser}
				{if $acl->isAllowed('subuser_add_listings', $GLOBALS.current_user.subuser.sid) || $acl->isAllowed('subuser_manage_listings', $GLOBALS.current_user.subuser.sid)}
					<li><img src="{image}account/applications_track_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/system/applications/view/">[[Application Tracking]]</a></li>
				{/if}
			{else}
				<li><img src="{image}account/applications_track_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/system/applications/view/">[[Application Tracking]]</a></li>
			{/if}
			{if $GLOBALS.current_user.subuser}
				{if $acl->isAllowed('use_screening_questionnaires', $GLOBALS.current_user.subuser.sid)}
					<li><img src="{image}account/questionnaires.png" alt=""/> <a href="{$GLOBALS.site_url}/screening-questionnaires/">[[Screening Questionnaires]]</a></li>
				{elseif $acl->getPermissionParams('use_screening_questionnaires') == "message"}
					<li><a href="{$GLOBALS.site_url}/screening-questionnaires/"  onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_screening_questionnaires', 300, '[[Screening Questionnaires]]'); return false;">[[Screening Questionnaires]]</a></li>
				{/if}
			{else}
				{if $acl->isAllowed('use_screening_questionnaires')}
					<li><img src="{image}account/questionnaires.png" alt=""/> <a href="{$GLOBALS.site_url}/screening-questionnaires/">[[Screening Questionnaires]]</a></li>
				{elseif $acl->getPermissionParams('use_screening_questionnaires') == "message"}
					<li><a href="{$GLOBALS.site_url}/screening-questionnaires/"  onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_screening_questionnaires', 300, '[[Screening Questionnaires]]'); return false;">[[Screening Questionnaires]]</a></li>
				{/if}
			{/if}
			{if $acl->isAllowed('create_sub_accounts') && !$GLOBALS.current_user.subuser}
				<li><img src="{image}account/subaccounts.png" alt=""/> <a href="{$GLOBALS.site_url}/sub-accounts/">[[Sub Accounts]]</a></li>
			{elseif $acl->getPermissionParams('create_sub_accounts') == "message"}
				<li><img src="{image}account/subaccounts.png" alt=""/> <a href="{$GLOBALS.site_url}/sub-accounts/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=create_sub_accounts', 300, '[[Sub Accounts]]'); return false;">[[Sub Accounts]]</a></li>
			{/if}
			{if !$GLOBALS.current_user.subuser}
				<li><img src="{image}account/notifications_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/user-notifications/">[[My Notifications]]</a></li>
			{/if}
			<li><img src="{image}account/icon-reports.png" alt=""/> <a href="{$GLOBALS.site_url}/my-reports/">[[My Reports]]</a></li>
			{if $acl->isAllowed('use_private_messages')}
	   			<li><img src="{image}account/message_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/private-messages/inbox/">[[Private messages]]</a>
					<div class="pm-sub-menu">
						&#187; <a href="{$GLOBALS.site_url}/private-messages/inbox/">[[Inbox]] ({$GLOBALS.current_user.new_messages})</a><br/>
						&#187; <a href="{$GLOBALS.site_url}/private-messages/outbox/">[[Outbox]]</a>
					</div>
	   			</li>
	   		{elseif $acl->getPermissionParams('use_private_messages') == "message"}
	   			<li><img src="{image}account/message_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/private-messages/inbox/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 300, '[[Private messages]]'); return false;">[[Private messages]]</a>
					<div class="pm-sub-menu">
						&#187; <a href="{$GLOBALS.site_url}/private-messages/inbox/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 300, '[[Inbox]]'); return false;">[[Inbox]] ({$GLOBALS.current_user.new_messages})</a><br/>
						&#187; <a href="{$GLOBALS.site_url}/private-messages/outbox/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 300, '[[Outbox]]'); return false;">[[Outbox]]</a>
					</div>
	   			</li>
			{/if}
		</ul>
	</div>
	<!-- END RIGHT COLUMN MY ACCOUNT -->
	<div class="MyAccountFoot"> </div>
</div>
<div id="adSpaceAccount">
	<div id="my-account-stats">{module name="statistics" function="my_reports"}</div>
	{module name="static_content" function="show_static_content" pageid="AccountEmpAdSpace"}
</div>