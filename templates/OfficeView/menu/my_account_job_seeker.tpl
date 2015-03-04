<h1>[[My Account]]</h1>
<div id="myAccount">
	<ul class="thumb">
		<li><a href="{$GLOBALS.site_url}/edit-profile/"><img class="expando" width="55" height="55" src="{image}account/myprofile_ico.png" alt=""/><br/><span>[[My Profile]]</span></a></li>
		{foreach from=$listingTypesInfo item="listingTypeInfo"}
			{if ($acl->isAllowed('post_'|cat:$listingTypeInfo.id)) || $listingTypeInfo.id == 'Resume'}
				<li>
					<a href="{$GLOBALS.site_url}/my-listings/{$listingTypeInfo.id}">
						<img class="expando" width="55" height="55" src="{image}account/myresumes_ico.png" alt=""/><br/>
						<span>{if in_array($listingTypeInfo.id, array('Job', 'Resume'))}[[My {$listingTypeInfo.name}s]]{else}[[My {$listingTypeInfo.name} Listings]]{/if}</span>
					</a>
				</li>
			{/if}
		{/foreach}
		<li><a href="{$GLOBALS.site_url}/my-products/"><img class="expando" width="55" height="55" src="{image}account/subscription_ico.png" alt=""/><br/><span>[[My Products]]</span></a></li>
		{if $acl->isAllowed('use_job_alerts')}
			<li><a href="{$GLOBALS.site_url}/job-alerts/"><img class="expando" width="55" height="55" src="{image}account/resume_alerts_ico.png" alt=""/><br/><span>[[Job Alerts]]</span></a></li>
		{elseif $acl->getPermissionParams('use_job_alerts') == "message"}
			<li><a href="{$GLOBALS.site_url}/job-alerts/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_job_alerts', 300, '[[Job Alerts]]'); return false;"><img class="expando" width="55" height="55" src="{image}account/resume_alerts_ico.png" alt=""/><br/><span>[[Job Alerts]]</span></a></li>
		{/if}
		<li><a href="{$GLOBALS.site_url}/user-notifications/"><img class="expando" width="55" height="55" src="{image}account/notifications_ico.png" alt=""/><br/><span>[[My Notifications]]</span></a></li>
		<li><a href="{$GLOBALS.site_url}/system/applications/view/"><img class="expando" width="55" height="55" src="{image}account/applications_track_ico.png" alt=""/><br/><span>[[My Applications]]</span></a></li>
		{if $acl->isAllowed('save_searches')}
			<li><a href="{$GLOBALS.site_url}/saved-searches/"><img class="expando" width="55" height="55" src="{image}account/save_ico.png"  alt=""/><br/><span>[[Saved Searches]]</span></a></li>
		{elseif $acl->getPermissionParams('save_searches') == "message"}
			<li><a href="{$GLOBALS.site_url}/saved-searches/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_searches', 300, '[[Saved searches]]'); return false;"><img class="expando" width="55" height="55" src="{image}account/save_ico.png"  alt=""/><br/><span>[[Saved Searches]]</span></a></li>
		{/if}
		{if $acl->isAllowed('save_job')}
			<li><a href="{$GLOBALS.site_url}/saved-jobs/"><img class="expando" width="55" height="55" src="{image}account/saved_listings_ico.png" alt=""/><br/><span>[[Saved Jobs]]</span></a></li>
		{elseif $acl->getPermissionParams('save_job') == "message"}
			<li><a href="{$GLOBALS.site_url}/saved-jobs/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_job', 300, '[[Saved Jobs]]'); return false;"><img class="expando" width="55" height="55" src="{image}account/saved_listings_ico.png" alt=""/><br/><span>[[Saved Jobs]]</span></a></li>
		{/if}
		<li><a href="{$GLOBALS.site_url}/my-invoices/"><img class="expando" width="55" height="55" src="{image}account/billing_hist_ico.png" alt=""/><br/><span>[[My Invoices]]</span></a></li>
		{if $acl->isAllowed('use_private_messages')}
			<li><a href="{$GLOBALS.site_url}/private-messages/inbox/"><img class="expando" width="55" height="55" src="{image}account/message_ico.png" alt=""/><br/><span>[[Private messages]]</span></a>
			<div class="pm-sub-menu">
				<a href="{$GLOBALS.site_url}/private-messages/inbox/">&#187; [[Inbox]] ({$GLOBALS.current_user.new_messages})</a>
				<a href="{$GLOBALS.site_url}/private-messages/outbox/">&#187; [[Outbox]]</a>
			</div></li>
		{elseif $acl->getPermissionParams('use_private_messages') == "message"}
			<li><a href="{$GLOBALS.site_url}/private-messages/inbox/"  onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 300, '[[Private messages]]'); return false;"><img class="expando" width="55" height="55" src="{image}account/message_ico.png" alt=""/><br/><span>[[Private messages]]</span></a>
			<div class="pm-sub-menu">
				<a href="{$GLOBALS.site_url}/private-messages/inbox/"  onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 300, '[[Inbox]]'); return false;">&#187; [[Inbox]] ({$GLOBALS.current_user.new_messages})</a>
				<a href="{$GLOBALS.site_url}/private-messages/outbox/"  onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 300, '[[Outbox]]'); return false;">&#187; [[Outbox]]</a>
			</div></li>
		{/if}
	</ul>
</div>	
<div id="adSpaceAccount">{module name="static_content" function="show_static_content" pageid="AccountJsAdSpace"}</div>
<div class="clr"></div>
{module name="classifieds" function="recently_viewed_listings" count_listing="10"}
{module name="classifieds" function="suggested_jobs" count_listing="5"}