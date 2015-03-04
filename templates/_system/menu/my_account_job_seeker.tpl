<div class="MyAccount">
	<div class="MyAccountHead"><h1>[[My Account]]</h1></div>

	<!-- LEFT COLUMN MY ACCOUNT -->
	<div class="leftColumnMA">
		<ul>
			<li><img src="{image}account/myprofile_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/edit-profile/">[[My Profile]]</a></li>
			{foreach from=$listingTypesInfo item="listingTypeInfo"}
				{if ($acl->isAllowed('post_'|cat:$listingTypeInfo.id)) || $listingTypeInfo.id == 'Resume'}
					<li>
						<img src="{image}account/myresumes_ico.png" alt=""/>
						<a href="{$GLOBALS.site_url}/my-listings/{$listingTypeInfo.id}/">
							{if in_array($listingTypeInfo.id, array('Job', 'Resume'))}[[My {$listingTypeInfo.name}s]]{else}[[My {$listingTypeInfo.name} Listings]]{/if}
						</a>
					</li>
				{/if}
			{/foreach}
			<li><img src="{image}account/subscription_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/my-products/">[[My Products]]</a></li>
			{if $acl->isAllowed('save_job')}
				<li><img src="{image}account/saved_listings_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/saved-jobs/">[[Saved Jobs]]</a></li>
			{elseif $acl->getPermissionParams('save_job') == "message"}
				<li><img src="{image}account/saved_listings_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/saved-jobs/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_job', 300, '[[Saved Jobs]]'); return false;">[[Saved Jobs]]</a></li>
			{/if}
			{if $acl->isAllowed('use_job_alerts')}
				<li><img src="{image}account/resume_alerts_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/job-alerts/">[[Job Alerts]]</a></li>
			{elseif $acl->getPermissionParams('use_job_alerts') == "message"}
				<li><img src="{image}account/resume_alerts_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/job-alerts/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_job_alerts', 300, '[[Job Alerts]]'); return false;">[[Job Alerts]]</a></li>
			{/if}
			{if $acl->isAllowed('save_searches')}
				<li><img src="{image}account/save_ico.png"  alt=""/> <a href="{$GLOBALS.site_url}/saved-searches/">[[Saved Searches]]</a></li>
			{elseif $acl->getPermissionParams('save_searches') == "message"}
				<li><img src="{image}account/save_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/saved-searches/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_searches', 300, '[[Saved searches]]'); return false;">[[Saved Searches]]</a></li>
			{/if}
		</ul>
	</div>
	<!-- END LEFT COLUMN MY ACCOUNT -->

	<!-- RIGHT COLUMN MY ACCOUNT -->
	<div class="rightColumnMA">
		<ul>
            <li><img src="{image}account/applications_track_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/system/applications/view/">[[My Applications]]</a></li>
			<li><img src="{image}account/notifications_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/user-notifications/">[[My Notifications]]</a></li>
			<li><img src="{image}account/billing_hist_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/my-invoices/">[[My Invoices]]</a></li>
			{if $acl->isAllowed('use_private_messages')}
				<li><img src="{image}account/message_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/private-messages/inbox/">[[Private messages]]</a>
				<div class="pm-sub-menu">
				&#187; <a href="{$GLOBALS.site_url}/private-messages/inbox/">[[Inbox]] ({$GLOBALS.current_user.new_messages})</a><br/>
				&#187; <a href="{$GLOBALS.site_url}/private-messages/outbox/">[[Outbox]]</a>
				</div></li>
			{elseif $acl->getPermissionParams('use_private_messages') == "message"}
				<li><img src="{image}account/message_ico.png" alt=""/> <a href="{$GLOBALS.site_url}/private-messages/inbox/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 300, '[[Private Message]]'); return false;">[[Private messages]]</a>
				<div class="pm-sub-menu">
				&#187; <a href="{$GLOBALS.site_url}/private-messages/inbox/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 300, '[[Inbox]]'); return false;">[[Inbox]] ({$GLOBALS.current_user.new_messages})</a><br/>
				&#187; <a href="{$GLOBALS.site_url}/private-messages/outbox/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 300, '[[Outbox]]'); return false;">[[Outbox]]</a>
				</div></li>
			{/if}
		</ul>
	</div>
	<!-- END RIGHT COLUMN MY ACCOUNT -->
	<div class="MyAccountFoot"> </div>
</div>
<div id="adSpaceAccount">{module name="static_content" function="show_static_content" pageid="AccountJsAdSpace"}</div>
<div class="clr"></div>
{module name="classifieds" function="recently_viewed_listings" count_listing="10"}
{module name="classifieds" function="suggested_jobs" count_listing="5"}