	<div class="clr"><br/></div>
	<div class="bottomMenu">
		<a href="{$GLOBALS.site_url}/">[[Home]]</a>
		<img src="{image}sepDot.png" border="0" alt="" /><a href="{$GLOBALS.site_url}/my-account/">[[My Account]]</a>
		{if $GLOBALS.current_user.logged_in}
			{if ($acl->isAllowed('open_job_search_form')) || $GLOBALS.current_user.group.id == "JobSeeker"}
				<img src="{image}sepDot.png" border="0" alt="" /><a href="{$GLOBALS.site_url}/find-jobs/" >[[Find Jobs]]</a>
			{/if}
			{if ($acl->isAllowed('open_resume_search_form')) || $GLOBALS.current_user.group.id == "Employer"}
				<img src="{image}sepDot.png" border="0" alt="" /><a href="{$GLOBALS.site_url}/search-resumes/" >[[Search Resumes]]</a>
			{/if}
			{foreach from=$listingTypesInfo item="listingTypeInfo"}
				{if ($acl->isAllowed('post_'|cat:$listingTypeInfo.id))
					|| $GLOBALS.current_user.group.id == "Employer" && $listingTypeInfo.id == "Job"
					|| $GLOBALS.current_user.group.id == "JobSeeker" && $listingTypeInfo.id == "Resume"}
						<img src="{image}sepDot.png" border="0" alt="" />
						<a href="{$GLOBALS.site_url}/add-listing/?listing_type_id={$listingTypeInfo.id}" >
							{if in_array($listingTypeInfo.id, array('Job', 'Resume'))}[[Post {$listingTypeInfo.id}s]]{else}[[Post {$listingTypeInfo.id} Listings]]{/if}
						</a>
				{/if}
			{/foreach}
		{else}
			{if $GLOBALS.current_user.group.id != "Employer"}
				<img src="{image}sepDot.png" border="0" alt="" /><a href="{$GLOBALS.site_url}/find-jobs/" >[[Find Jobs]]</a>
				<img src="{image}sepDot.png" border="0" alt="" /><a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Resume" >[[Post Resumes]]</a>
			{/if}
			{if $GLOBALS.current_user.group.id != "JobSeeker"}
				<img src="{image}sepDot.png" border="0" alt="" /><a href="{$GLOBALS.site_url}/search-resumes/" >[[Search Resumes]]</a>
				<img src="{image}sepDot.png" border="0" alt="" /><a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Job" >[[Post Jobs]]</a>
			{/if}
		{/if}
		<img src="{image}sepDot.png" border="0" alt="" /><a href="{$GLOBALS.site_url}/contact/" >[[Contact]]</a>
		<img src="{image}sepDot.png" border="0" alt="" /><a href="{$GLOBALS.site_url}/about/">[[About Us]]</a>
		<img src="{image}sepDot.png" border="0" alt="" /><a href="{$GLOBALS.site_url}/site-map/">[[Sitemap]]</a>
		{if isset($GLOBALS.mobileUrl)}
			<img src="{image}sepDot.png" border="0" alt="" /><a href="{$GLOBALS.mobileUrl}{if $GLOBALS.SessionId}?authId={$GLOBALS.SessionId}{/if}">[[Mobile Version]]</a>
		{/if}
		{if $GLOBALS.settings.cookieLaw}
			<img src="{image}sepDot.png" border="0" alt="" /><a href="#" onClick="return cookiePreferencesPopupOpen();">[[Cookie Preferences]]</a>
		{/if}
	</div>
</div>
<div class="Footer">
	&copy; Shixi.com {$smarty.now|date_format:"%Y"}
</div>