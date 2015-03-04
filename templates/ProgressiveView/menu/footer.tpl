<div id="footer">
	<div class="footer-wrapper">
		<ul>
			<li><a href="{$GLOBALS.site_url}/">[[Home]]</a> / </li>
			{if $GLOBALS.current_user.logged_in}
				<li><a href="{$GLOBALS.site_url}/my-account/">[[My Account]]</a> / </li>
			{/if}
			<li>
				{if $GLOBALS.current_user.group.id == "Employer"}
					<a href="{$GLOBALS.site_url}/employer-products/">[[Products]]</a>
				{elseif !$GLOBALS.current_user.logged_in && $GLOBALS.user_page_uri == "/employers/"}
					<a href="{$GLOBALS.site_url}/employer-products/">[[Products]]</a>
				{else}
					<a href="{$GLOBALS.site_url}/jobseeker-products/">[[Products]]</a>
				{/if} /
			</li>
			{if $GLOBALS.current_user.logged_in}
				{if $GLOBALS.current_user.group.id != "Employer"}
					{if ($acl->isAllowed('open_job_search_form')) || $GLOBALS.current_user.group.id == "JobSeeker"}
						<li><a href="{$GLOBALS.site_url}/find-jobs/">[[Find Jobs]]</a> / </li>
					{/if}
				{/if}
				{if ($acl->isAllowed('open_resume_search_form')) || $GLOBALS.current_user.group.id == "Employer"}
					<li><a href="{$GLOBALS.site_url}/search-resumes/">[[Search Resumes]]</a> / </li>
				{/if}
				{foreach from=$listingTypesInfo item="listingTypeInfo"}
					{if ($acl->isAllowed('post_'|cat:$listingTypeInfo.id))
						|| $GLOBALS.current_user.group.id == "Employer" && $listingTypeInfo.id == "Job"
						|| $GLOBALS.current_user.group.id == "JobSeeker" && $listingTypeInfo.id == "Resume"}
							<li>
								<a href="{$GLOBALS.site_url}/add-listing/?listing_type_id={$listingTypeInfo.id}" >
									{if in_array($listingTypeInfo.id, array('Job', 'Resume'))}[[Post {$listingTypeInfo.id}s]]{else}[[Post {$listingTypeInfo.id} Listings]]{/if}
								</a> /
							</li>
					{/if}
				{/foreach}
			{else}
				{if $GLOBALS.user_page_uri == "/"}
					<li><a href="{$GLOBALS.site_url}/find-jobs/">[[Find Jobs]]</a> / </li>
					<li><a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Resume">[[Post Resumes]]</a> / </li>
				{elseif $GLOBALS.user_page_uri == "/employers/"}
					<li><a href="{$GLOBALS.site_url}/search-resumes/" >[[Search Resumes]]</a> / </li>
					<li><a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Job" >[[Post Jobs]]</a> / </li>
				{else}
					<li><a href="{$GLOBALS.site_url}/find-jobs/" >[[Find Jobs]]</a> / </li>
					<li><a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Resume" >[[Post Resumes]]</a> / </li>
					<li><a href="{$GLOBALS.site_url}/search-resumes/" >[[Search Resumes]]</a> / </li>
					<li><a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Job" >[[Post Jobs]]</a> / </li>
				{/if}
			{/if}
			<li><a href="{$GLOBALS.site_url}/contact/">[[Contact]]</a> / </li>
			<li><a href="{$GLOBALS.site_url}/about/">[[About Us]]</a> / </li>
			<li><a href="{$GLOBALS.site_url}/site-map/">[[Site Map]]</a></li>
			{if isset($GLOBALS.mobileUrl)}
				<li>
					/ <a href="{$GLOBALS.mobileUrl}{if $GLOBALS.SessionId}?authId={$GLOBALS.SessionId}{/if}">[[Mobile Version]]</a>
				</li>
			{/if}
			{if $GLOBALS.settings.cookieLaw}
				<li>
					 / <a href="#" onClick="return cookiePreferencesPopupOpen();">[[Cookie Preferences]]</a>
				</li>
			{/if}
		</ul>
		<div id="copy">&copy; 2008-{$smarty.now|date_format:"%Y"} [[Powered by]] <a target="_blank" href="http://www.smartjobboard.com" title="Job Board Software, Script">SmartJobBoard Job Board Software</a></div>
	</div>
	<div id="footer-bottom"></div>
</div>

