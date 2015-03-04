<ul>
    <li><a href="{$GLOBALS.site_url}/edit-profile/">[[My Profile]]</a></li>
	{foreach from=$listingTypesInfo item="listingTypeInfo"}
		{if ($acl->isAllowed('post_'|cat:$listingTypeInfo.id)) || $listingTypeInfo.id == 'Resume'}
			<li>
				<a href="{$GLOBALS.site_url}/my-listings/{$listingTypeInfo.id}">
					{if in_array($listingTypeInfo.id, array('Job', 'Resume'))}[[My {$listingTypeInfo.name}s]]{else}[[My {$listingTypeInfo.name} Listings]]{/if}
				</a>
			</li>
		{/if}
	{/foreach}
    <li><a href="{$GLOBALS.site_url}/my-products/">[[My Products]]</a></li>
	<li><a href="{$GLOBALS.site_url}/system/applications/view/">[[My Applications]]</a></li>
	{if $acl->isAllowed('save_job')}
		<li><a href="{$GLOBALS.site_url}/saved-jobs/">[[Saved Jobs]]</a></li>
	{elseif $acl->getPermissionParams('save_job') == "message"}
		<li><a href="{$GLOBALS.site_url}/saved-jobs/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_job', 300, '[[Saved Jobs]]'); return false;">[[Saved Jobs]]</a></li>
	{/if}
	{if $acl->isAllowed('use_job_alerts')}
		<li><a href="{$GLOBALS.site_url}/job-alerts/">[[Job Alerts]]</a></li>
	{elseif $acl->getPermissionParams('use_job_alerts') == "message"}
		<li><a href="{$GLOBALS.site_url}/job-alerts/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_job_alerts', 300, '[[Job Alerts]]'); return false;">[[Job Alerts]]</a></li>
	{/if}
	{if $acl->isAllowed('save_searches')}
		<li><a href="{$GLOBALS.site_url}/saved-searches/">[[Saved Searches]]</a></li>
	{elseif $acl->getPermissionParams('save_searches') == "message"}
		<li><a href="{$GLOBALS.site_url}/saved-searches/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_searches', 300, '[[Saved searches]]'); return false;">[[Saved Searches]]</a></li>
	{/if}
	<li><a href="{$GLOBALS.site_url}/user-notifications/">[[My Notifications]]</a></li>
	<li><a href="{$GLOBALS.site_url}/my-invoices/">[[My Invoices]]</a></li>
	{if $acl->isAllowed('use_private_messages')}
		<li><a href="{$GLOBALS.site_url}/private-messages/inbox/">[[Private messages]]</a></li>
	{elseif $acl->getPermissionParams('use_private_messages') == "message"}
		<li><a href="{$GLOBALS.site_url}/private-messages/inbox/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 300, '[[Private messages]]'); return false;">[[Private messages]]</a></li>
	{/if}
</ul>