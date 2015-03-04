<div id="footer">
	<a href="{$GLOBALS.site_url}" >[[Home]]</a> &nbsp; <img src="{image}menuSep.png" border="0" alt=""/> &nbsp;
	<a href="{$GLOBALS.site_url}/my-account/" >[[My Account]]</a> &nbsp; <img src="{image}menuSep.png" border="0" alt=""/> &nbsp;
	{if $GLOBALS.current_user.logged_in}
		{if ($acl->isAllowed('open_job_search_form')) || $GLOBALS.current_user.group.id == "JobSeeker"}
			<a href="{$GLOBALS.site_url}/find-jobs/" >[[Find Jobs]]</a> &nbsp; <img src="{image}menuSep.png" border="0" alt=""/> &nbsp;
		{/if}
		{if ($acl->isAllowed('open_resume_search_form')) || $GLOBALS.current_user.group.id == "Employer"}
			<a href="{$GLOBALS.site_url}/search-resumes/" >[[Search Resumes]]</a> &nbsp; <img src="{image}menuSep.png" border="0" alt=""/> &nbsp;
		{/if}
		{foreach from=$listingTypesInfo item="listingTypeInfo"}
			{if ($acl->isAllowed('post_'|cat:$listingTypeInfo.id))
				|| $GLOBALS.current_user.group.id == "Employer" && $listingTypeInfo.id == "Job"
				|| $GLOBALS.current_user.group.id == "JobSeeker" && $listingTypeInfo.id == "Resume"}
					<a href="{$GLOBALS.site_url}/add-listing/?listing_type_id={$listingTypeInfo.id}" >
						{if in_array($listingTypeInfo.id, array('Job', 'Resume'))}[[Post {$listingTypeInfo.id}s]]{else}[[Post {$listingTypeInfo.id} Listings]]{/if}
					</a> &nbsp;
					<img src="{image}menuSep.png" border="0" alt=""/> &nbsp;
			{/if}
		{/foreach}
	{else}
		{if $GLOBALS.current_user.group.id != "Employer"}
			<a href="{$GLOBALS.site_url}/find-jobs/" >[[Find Jobs]]</a> &nbsp; <img src="{image}menuSep.png" border="0" alt=""/> &nbsp;
			<a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Resume" >[[Post Resumes]]</a> &nbsp; <img src="{image}menuSep.png" border="0" alt=""/> &nbsp;
		{/if}
		{if $GLOBALS.current_user.group.id != "JobSeeker"}
			<a href="{$GLOBALS.site_url}/search-resumes/" >[[Search Resumes]]</a> &nbsp; <img src="{image}menuSep.png" border="0" alt=""/> &nbsp;
			<a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Job" >[[Post Jobs]]</a> &nbsp; <img src="{image}menuSep.png" border="0" alt=""/> &nbsp;
		{/if}
	{/if}
	<a href="{$GLOBALS.site_url}/contact" >[[Contact]]</a> &nbsp; <img src="{image}menuSep.png" border="0" alt=""/> &nbsp;
	<a href="{$GLOBALS.site_url}/about/">[[About Us]]</a> &nbsp; <img src="{image}menuSep.png" border="0" alt=""/> &nbsp;
	<a href="{$GLOBALS.site_url}/site-map/">[[Sitemap]]</a>
	{if isset($GLOBALS.mobileUrl)}
		&nbsp; <img src="{image}menuSep.png" border="0" alt=""/> &nbsp;
		<a href="{$GLOBALS.mobileUrl}{if $GLOBALS.SessionId}?authId={$GLOBALS.SessionId}{/if}">[[Mobile Version]]</a>
	{/if}
	{if $GLOBALS.settings.cookieLaw}
		&nbsp; <img src="{image}menuSep.png" border="0" alt=""/> &nbsp;
		<a href="#" onClick="return cookiePreferencesPopupOpen();">[[Cookie Preferences]]</a>
	{/if}
	<br/><br/>[[Powered by]] <a target="_blank" href="http://www.smartjobboard.com" title="Job Board Software, Script">SmartJobBoard Job Board Software</a>
</div>
</div>