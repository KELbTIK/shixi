<div class="MyAccount col-sm-7">
	<div class="row">
		<div class="MyAccountHead"><h1>[[My Account]]</h1></div>
		<!-- LEFT COLUMN MY ACCOUNT -->
		<div class="col-sm-6">
			<div class="row">
				<div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
					<div class="icon-container default-bg">
						<i class="fa fa-user"></i>
					</div>
					<div class="body">
						<h3>[[My Profile]]</h3>
						<a href="{$GLOBALS.site_url}/edit-profile/" class="link"><span>Read More</span></a>
					</div>
				</div>
				{foreach from=$listingTypesInfo item="listingTypeInfo"}
					{if ($acl->isAllowed('post_'|cat:$listingTypeInfo.id)) || $listingTypeInfo.id == 'Resume'}
						<div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
							<div class="icon-container default-bg">
								<i class="fa fa-book"></i>
							</div>
							<div class="body">
								<h3>
									{if in_array($listingTypeInfo.id, array('Job', 'Resume'))}[[My {$listingTypeInfo.name}s]]{else}[[My {$listingTypeInfo.name} Listings]]{/if}
								</h3>
								<a href="{$GLOBALS.site_url}/my-listings/{$listingTypeInfo.id}/" class="link"><span>Read More</span></a>
							</div>
						</div>
					{/if}
				{/foreach}
				<div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
					<div class="icon-container default-bg">
						<i class="fa fa-check-square-o"></i>
					</div>
					<div class="body">
						<h3>
							[[My Products]]
						</h3>
						<a href="{$GLOBALS.site_url}/my-products/" class="link"><span>Read More</span></a>
					</div>
				</div>
				{if $acl->isAllowed('save_job')}
					<div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
						<div class="icon-container default-bg">
							<i class="fa fa-save"></i>
						</div>
						<div class="body">
							<h3>
								[[Saved Jobs]]
							</h3>
							<a href="{$GLOBALS.site_url}/saved-jobs/" class="link"><span>Read More</span></a>
						</div>
					</div>
				{elseif $acl->getPermissionParams('save_job') == "message"}
					<div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
						<div class="icon-container default-bg">
							<i class="fa fa-save"></i>
						</div>
						<div class="body">
							<h3>
								[[Saved Jobs]]
							</h3>
							<a href="{$GLOBALS.site_url}/saved-jobs/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_job', 300, '[[Saved Jobs]]'); return false;" class="link"><span>Read More</span></a>
						</div>
					</div>
				{/if}
				{if $acl->isAllowed('use_job_alerts')}
					<div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
						<div class="icon-container default-bg">
							<i class="fa fa-exclamation"></i>
						</div>
						<div class="body">
							<h3>
								[[Job Alerts]]
							</h3>
							<a href="{$GLOBALS.site_url}/job-alerts/" class="link"><span>Read More</span></a>
						</div>
					</div>
				{elseif $acl->getPermissionParams('use_job_alerts') == "message"}
					<div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
						<div class="icon-container default-bg">
							<i class="fa fa-exclamation"></i>
						</div>
						<div class="body">
							<h3>
								[[Job Alerts]]
							</h3>
							<a href="{$GLOBALS.site_url}/job-alerts/"  onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_job_alerts', 300, '[[Job Alerts]]'); return false;" class="link"><span>Read More</span></a>
						</div>
					</div>
				{/if}
				{if $acl->isAllowed('save_searches')}
					<div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
						<div class="icon-container default-bg">
							<i class="fa fa-save"></i>
						</div>
						<div class="body">
							<h3>
								[[Saved Searches]]
							</h3>
							<a href="{$GLOBALS.site_url}/saved-searches/"  class="link"><span>Read More</span></a>
						</div>
					</div>
				{elseif $acl->getPermissionParams('save_searches') == "message"}
					<div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
						<div class="icon-container default-bg">
							<i class="fa fa-save"></i>
						</div>
						<div class="body">
							<h3>
								[[Saved Searches]]
							</h3>
							<a href="{$GLOBALS.site_url}/saved-searches/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_searches', 300, '[[Saved searches]]'); return false;" class="link"><span>Read More</span></a>
						</div>
					</div>
				{/if}
			</div>
		</div>
		<!-- END LEFT COLUMN MY ACCOUNT -->

		<!-- RIGHT COLUMN MY ACCOUNT -->
		<div class="col-sm-6">
			<div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
				<div class="icon-container default-bg">
					<i class="fa fa-clock-o"></i>
				</div>
				<div class="body">
					<h3>
						[[My Applications]]
					</h3>
					<a href="{$GLOBALS.site_url}/system/applications/view/" class="link"><span>Read More</span></a>
				</div>
			</div>
			<div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
				<div class="icon-container default-bg">
					<i class="fa fa-exclamation"></i>
				</div>
				<div class="body">
					<h3>
						[[My Notifications]]
					</h3>
					<a href="{$GLOBALS.site_url}/user-notifications/" class="link"><span>Read More</span></a>
				</div>
			</div>
			<div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
				<div class="icon-container default-bg">
					<i class="fa fa-credit-card"></i>
				</div>
				<div class="body">
					<h3>
						[[My Invoices]]
					</h3>
					<a href="{$GLOBALS.site_url}/my-invoices/" class="link"><span>Read More</span></a>
				</div>
			</div>
			{if $acl->isAllowed('use_private_messages')}
				<div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
					<div class="icon-container default-bg">
						<i class="fa fa-envelope"></i>
					</div>
					<div class="body">
						<h3>
							[[Private messages]]
						</h3>
						<a href="{$GLOBALS.site_url}/private-messages/inbox/">[[Inbox]] ({$GLOBALS.current_user.new_messages})</a> &nbsp;
						<a href="{$GLOBALS.site_url}/private-messages/outbox/">[[Outbox]]</a>
					</div>
				</div>
			{elseif $acl->getPermissionParams('use_private_messages') == "message"}
				<div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
					<div class="icon-container default-bg">
						<i class="fa fa-envelope"></i>
					</div>
					<div class="body">
						<h3>
							[[Private messages]]
						</h3>
						<a href="{$GLOBALS.site_url}/private-messages/inbox/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 300, '[[Inbox]]'); return false;">[[Inbox]] ({$GLOBALS.current_user.new_messages})</a> &nbsp;
						<a href="{$GLOBALS.site_url}/private-messages/outbox/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 300, '[[Outbox]]'); return false;">[[Outbox]]</a>
					</div>
				</div>
			{/if}
		</div>
		<!-- END RIGHT COLUMN MY ACCOUNT -->
		<div class="MyAccountFoot"> </div>
	</div>
</div>

<div id="adSpaceAccount" class="col-sm-5 table-corect">
	{*<div class="clr"></div>*}
	{module name="classifieds" function="recently_viewed_listings" count_listing="10"}
	{module name="classifieds" function="suggested_jobs" count_listing="5"}
	{module name="static_content" function="show_static_content" pageid="AccountJsAdSpace"}
</div>