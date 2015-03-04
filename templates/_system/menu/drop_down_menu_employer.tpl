<ul>
	<li><a href="{$GLOBALS.site_url}/edit-profile/">[[My Profile]]</a></li>

	{if $GLOBALS.current_user.subuser}
		{if $acl->isAllowed('subuser_add_listings', $GLOBALS.current_user.subuser.sid) || $acl->isAllowed('subuser_manage_listings', $GLOBALS.current_user.subuser.sid)}
			{foreach from=$listingTypesInfo item="listingTypeInfo"}
				{if ($acl->isAllowed('post_'|cat:$listingTypeInfo.id)) || $listingTypeInfo.id == 'Job'}
					<li>
						<a href="{$GLOBALS.site_url}/my-listings/{$listingTypeInfo.id}/">
							{if in_array($listingTypeInfo.id, array('Job', 'Resume'))}[[My {$listingTypeInfo.name}s]]{else}[[My {$listingTypeInfo.name} Listings]]{/if}
						</a>
					</li>
				{/if}
			{/foreach}
		{/if}
	{else}
		{foreach from=$listingTypesInfo item="listingTypeInfo"}
			{if ($acl->isAllowed('post_'|cat:$listingTypeInfo.id)) || $listingTypeInfo.id == 'Job'}
				<li>
					<a href="{$GLOBALS.site_url}/my-listings/{$listingTypeInfo.id}/">
						{if in_array($listingTypeInfo.id, array('Job', 'Resume'))}[[My {$listingTypeInfo.name}s]]{else}[[My {$listingTypeInfo.name} Listings]]{/if}
					</a>
				</li>
			{/if}
		{/foreach}
	{/if}

	{if $GLOBALS.current_user.subuser}
		{if $acl->isAllowed('subuser_manage_subscription', $GLOBALS.current_user.subuser.sid)}
			<li><a href="{$GLOBALS.site_url}/my-products/">[[My Products]]</a></li>
		{/if}
	{else}
		<li><a href="{$GLOBALS.site_url}/my-products/">[[My Products]]</a></li>
	{/if}

	{if $GLOBALS.current_user.subuser}
		{if $acl->isAllowed('subuser_add_listings', $GLOBALS.current_user.subuser.sid) || $acl->isAllowed('subuser_manage_listings', $GLOBALS.current_user.subuser.sid)}
			<li><a href="{$GLOBALS.site_url}/system/applications/view/">[[Application Tracking]]</a></li>
		{/if}
	{else}
		<li><a href="{$GLOBALS.site_url}/system/applications/view/">[[Application Tracking]]</a></li>
	{/if}

	{if $acl->isAllowed('save_resume')}
		<li><a href="{$GLOBALS.site_url}/saved-resumes/">[[Saved Resumes]]</a></li>
	{elseif $acl->getPermissionParams('save_resume') == "message"}
		<li>
			<a onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_resume', 400, '[[Saved Resumes]]', true, false); return false;"
				href="{$GLOBALS.site_url}/access-denied/?permission=save_resume">
				[[Saved Resumes]]
			</a>
		</li>
	{/if}

	{if $acl->isAllowed('use_resume_alerts')}
		<li><a href="{$GLOBALS.site_url}/resume-alerts/">[[Resume Alerts]]</a></li>
	{elseif $acl->getPermissionParams('use_resume_alerts') == "message"}
		<li><a href="{$GLOBALS.site_url}/resume-alerts/"  onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_resume_alerts', 300, '[[Resume Alerts]]'); return false;">[[Resume Alerts]]</a></li>
	{/if}

	{if $acl->isAllowed('save_searches')}
		<li><a href="{$GLOBALS.site_url}/saved-searches/">[[Saved Searches]]</a></li>
	{elseif $acl->getPermissionParams('save_searches') == "message"}
		<li><a href="{$GLOBALS.site_url}/saved-searches/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_searches', 300, '[[Saved searches]]'); return false;">[[Saved Searches]]</a></li>
	{/if}

	{if $GLOBALS.current_user.subuser}
		{if $acl->isAllowed('use_screening_questionnaires', $GLOBALS.current_user.subuser.sid)}
			<li><a href="{$GLOBALS.site_url}/screening-questionnaires/">[[Screening Questionnaires]]</a></li>
		{elseif $acl->getPermissionParams('use_screening_questionnaires') == "message"}
			<li><a href="{$GLOBALS.site_url}/screening-questionnaires/"  onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_screening_questionnaires', 300, '[[Screening Questionnaires]]'); return false;">[[Screening Questionnaires]]</a></li>
		{/if}
	{else}
		{if $acl->isAllowed('use_screening_questionnaires')}
			<li><a href="{$GLOBALS.site_url}/screening-questionnaires/">[[Screening Questionnaires]]</a></li>
		{elseif $acl->getPermissionParams('use_screening_questionnaires') == "message"}
			<li><a href="{$GLOBALS.site_url}/screening-questionnaires/"  onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_screening_questionnaires', 300, '[[Screening Questionnaires]]'); return false;">[[Screening Questionnaires]]</a></li>
		{/if}
	{/if}

	{if $acl->isAllowed('create_sub_accounts') && !$GLOBALS.current_user.subuser}
		<li><a href="{$GLOBALS.site_url}/sub-accounts/">[[Sub Accounts]]</a></li>
	{elseif $acl->getPermissionParams('create_sub_accounts') == "message"}
		<li><a href="{$GLOBALS.site_url}/sub-accounts/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=create_sub_accounts', 300, '[[Sub Accounts]]'); return false;">[[Sub Accounts]]</a></li>
	{/if}

	{if !$GLOBALS.current_user.subuser}
		<li><a href="{$GLOBALS.site_url}/user-notifications/">[[My Notifications]]</a></li>
	{/if}

	<li><a href="{$GLOBALS.site_url}/my-invoices/">[[My Invoices]]</a></li>

	<li><a href="{$GLOBALS.site_url}/my-reports/">[[My Reports]]</a></li>

	{if $acl->isAllowed('use_private_messages')}
		<li><a href="{$GLOBALS.site_url}/private-messages/inbox/">[[Private messages]]</a></li>
	{elseif $acl->getPermissionParams('use_private_messages') == "message"}
		<li><a href="{$GLOBALS.site_url}/private-messages/inbox/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 300, '[[Private messages]]'); return false;">[[Private messages]]</a></li>
	{/if}
</ul>