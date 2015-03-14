{if $account_activated}
	<div class="message alert alert-info">
		[[Your account was successfully activated. Thank you.]]
    </div>
{/if}
<div class="MyAccount col-sm-8">
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
                {capture assign="myListingsBlock"}
                    {foreach from=$listingTypesInfo item="listingTypeInfo"}
                        {if ($acl->isAllowed('post_'|cat:$listingTypeInfo.id)) || $listingTypeInfo.id == 'Job'}
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
                    {/if}
                {else}
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
                {/if}
                {if $acl->isAllowed('save_resume')}
                        <div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
                            <div class="icon-container default-bg">
                                <i class="fa fa-floppy-o"></i>
                            </div>
                            <div class="body">
                                <h3>
                                    [[Saved Resumes]]
                                </h3>
                                <a href="{$GLOBALS.site_url}/saved-resumes/" class="link"><span>Read More</span></a>
                            </div>
                        </div>
                {elseif $acl->getPermissionParams('save_resume') == "message"}
                        <div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
                            <div class="icon-container default-bg">
                                <i class="fa fa-floppy-o"></i>
                            </div>
                            <div class="body">
                                <h3>
                                    [[Saved Resumes]]
                                </h3>
                                <a onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_resume', 400, '[[Saved Resumes]]', true, false); return false;"
                                   href="{$GLOBALS.site_url}/access-denied/?permission=save_resume" class="link"><span>Read More</span></a>
                            </div>
                        </div>
                {/if}
                {if $acl->isAllowed('use_resume_alerts')}
                        <div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
                            <div class="icon-container default-bg">
                                <i class="fa fa-tasks"></i>
                            </div>
                            <div class="body">
                                <h3>
                                    [[Resume Alerts]]
                                </h3>
                                <a href="{$GLOBALS.site_url}/resume-alerts/" class="link"><span>Read More</span></a>
                            </div>
                        </div>
                {elseif $acl->getPermissionParams('use_resume_alerts') == "message"}
                        <div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
                            <div class="icon-container default-bg">
                                <i class="fa fa-tasks"></i>
                            </div>
                            <div class="body">
                                <h3>
                                    [[Resume Alerts]]
                                </h3>
                                <a href="{$GLOBALS.site_url}/resume-alerts/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_resume_alerts', 300, '[[Resume Alerts]]'); return false;" class="link"><span>Read More</span></a>
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
                                <a href="{$GLOBALS.site_url}/saved-searches/" class="link"><span>Read More</span></a>
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
            {if $GLOBALS.current_user.subuser}
                {if $acl->isAllowed('subuser_add_listings', $GLOBALS.current_user.subuser.sid) || $acl->isAllowed('subuser_manage_listings', $GLOBALS.current_user.subuser.sid)}
                    <div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
                        <div class="icon-container default-bg">
                            <i class="fa fa-clock-o"></i>
                        </div>
                        <div class="body">
                            <h3>
                                [[Application Tracking]]
                            </h3>
                            <a href="{$GLOBALS.site_url}/system/applications/view/" class="link"><span>Read More</span></a>
                        </div>
                    </div>
                {/if}
            {else}
                <div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
                    <div class="icon-container default-bg">
                        <i class="fa fa-clock-o"></i>
                    </div>
                    <div class="body">
                        <h3>
                            [[Application Tracking]]
                        </h3>
                        <a href="{$GLOBALS.site_url}/system/applications/view/" class="link"><span>Read More</span></a>
                    </div>
                </div>
            {/if}
            {if $GLOBALS.current_user.subuser}
                {if $acl->isAllowed('use_screening_questionnaires', $GLOBALS.current_user.subuser.sid)}
                    <div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
                        <div class="icon-container default-bg">
                            <i class="fa fa-question-circle"></i>
                        </div>
                        <div class="body">
                            <h3>
                                [[Screening Questionnaires]]
                            </h3>
                            <a href="{$GLOBALS.site_url}/screening-questionnaires/" class="link"><span>Read More</span></a>
                        </div>
                    </div>
                {elseif $acl->getPermissionParams('use_screening_questionnaires') == "message"}
                    <div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
                        <div class="icon-container default-bg">
                            <i class="fa fa-question-circle"></i>
                        </div>
                        <div class="body">
                            <h3>
                                [[Screening Questionnaires]]
                            </h3>
                            <a href="{$GLOBALS.site_url}/screening-questionnaires/"  onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_screening_questionnaires', 300, '[[Screening Questionnaires]]'); return false;" class="link"><span>Read More</span></a>
                        </div>
                    </div>
                {/if}
            {else}
                {if $acl->isAllowed('use_screening_questionnaires')}
                    <div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
                        <div class="icon-container default-bg">
                            <i class="fa fa-question-circle"></i>
                        </div>
                        <div class="body">
                            <h3>
                                [[Screening Questionnaires]]
                            </h3>
                            <a href="{$GLOBALS.site_url}/screening-questionnaires/" class="link"><span>Read More</span></a>
                        </div>
                    </div>
                {elseif $acl->getPermissionParams('use_screening_questionnaires') == "message"}
                    <div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
                        <div class="icon-container default-bg">
                            <i class="fa fa-question-circle"></i>
                        </div>
                        <div class="body">
                            <h3>
                                [[Screening Questionnaires]]
                            </h3>
                            <a href="{$GLOBALS.site_url}/screening-questionnaires/"  onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_screening_questionnaires', 300, '[[Screening Questionnaires]]'); return false;" class="link"><span>Read More</span></a>
                        </div>
                    </div>
                {/if}
            {/if}
            {if $acl->isAllowed('create_sub_accounts') && !$GLOBALS.current_user.subuser}
                <div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
                    <div class="icon-container default-bg">
                        <i class="fa fa-user-plus"></i>
                    </div>
                    <div class="body">
                        <h3>
                            [[Sub Accounts]]
                        </h3>
                        <a href="{$GLOBALS.site_url}/sub-accounts/" class="link"><span>Read More</span></a>
                    </div>
                </div>
            {elseif $acl->getPermissionParams('create_sub_accounts') == "message"}
                <div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
                    <div class="icon-container default-bg">
                        <i class="fa fa-user-plus"></i>
                    </div>
                    <div class="body">
                        <h3>
                            [[Sub Accounts]]
                        </h3>
                        <a href="{$GLOBALS.site_url}/sub-accounts/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=create_sub_accounts', 300, '[[Sub Accounts]]'); return false;" class="link"><span>Read More</span></a>
                    </div>
                </div>
            {/if}
            {if !$GLOBALS.current_user.subuser}
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
            {/if}
            <div class="box-style-2 object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
                <div class="icon-container default-bg">
                    <i class="fa fa-file-text-o"></i>
                </div>
                <div class="body">
                    <h3>
                        [[My Reports]]
                    </h3>
                    <a href="{$GLOBALS.site_url}/my-reports/" class="link"><span>Read More</span></a>
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
<div id="adSpaceAccount" class="col-sm-4">
	<div id="my-account-stats">{module name="statistics" function="my_reports"}</div>
	{module name="static_content" function="show_static_content" pageid="AccountEmpAdSpace"}
</div>