    <footer id="footer">

        <!-- .footer start -->
        <!-- ================ -->
        <div class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="footer-content">
                            <div class="logo-footer">
                                <div class="png"></div>
                                <a href="{$GLOBALS.site_url}/"><img src="{image}logo.png" border="0" alt="[[{$GLOBALS.settings.logoAlternativeText}]]" title="[[{$GLOBALS.settings.logoAlternativeText}]]" /></a>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <p>Lorem ipsum dolor sit amet, consect tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim ven.</p>
                                    <ul class="social-links circle">
                                        <li class="facebook"><a target="_blank" href="http://www.facebook.com"><i class="fa fa-facebook"></i></a></li>
                                        <li class="twitter"><a target="_blank" href="http://www.twitter.com"><i class="fa fa-twitter"></i></a></li>
                                        <li class="googleplus"><a target="_blank" href="http://plus.google.com"><i class="fa fa-google-plus"></i></a></li>
                                        <li class="skype"><a target="_blank" href="http://www.skype.com"><i class="fa fa-skype"></i></a></li>
                                        <li class="linkedin"><a target="_blank" href="http://www.linkedin.com"><i class="fa fa-linkedin"></i></a></li>
                                    </ul>
                                </div>
                                <div class="col-sm-6">
                                    <ul class="list-icons">
                                        <li><i class="fa fa-map-marker pr-10"></i> One infinity loop, 54100</li>
                                        <li><i class="fa fa-phone pr-10"></i> +00 1234567890</li>
                                        <li><i class="fa fa-fax pr-10"></i> +00 1234567891 </li>
                                        <li><i class="fa fa-envelope-o pr-10"></i> info@idea.com</li>
                                    </ul>
                                </div>
                            </div>
                            <a href="page-about.html" class="link"><span>Read More</span></a>
                        </div>
                    </div>
                    <div class="space-bottom hidden-lg hidden-xs"></div>
                    <div class="col-md-6">
                        <div class="footer-content">
                            <h2>Links</h2>
                            <nav>
                                <ul class="nav nav-pills nav-stacked col-sm-6">
                                    <li>
                                        <a href="{$GLOBALS.site_url}/">[[Home]]</a>
                                    </li>
                                    <li>
                                        <a href="{$GLOBALS.site_url}/my-account/">[[My Account]]</a>
                                    </li>
                                    <li>
                                        <a href="{$GLOBALS.site_url}/contact/" >[[Contact]]</a>
                                    </li>
                                    <li>
                                        <a href="{$GLOBALS.site_url}/about/">[[About Us]]</a>
                                    </li>
                                    <li>
                                       <a href="{$GLOBALS.site_url}/site-map/">[[Sitemap]]</a>
                                    </li>


                                </ul>
                                <ul class="nav nav-pills nav-stacked col-sm-6 ">
                                    {if isset($GLOBALS.mobileUrl)}
                                        <li>
                                           <a href="{$GLOBALS.mobileUrl}{if $GLOBALS.SessionId}?authId={$GLOBALS.SessionId}{/if}">[[Mobile Version]]</a>
                                        </li>
                                    {/if}
                                    {if $GLOBALS.settings.cookieLaw}
                                        <li>
                                            <a href="#" onClick="return cookiePreferencesPopupOpen();">[[Cookie Preferences]]</a>
                                        </li>
                                    {/if}
                                    {if $GLOBALS.current_user.logged_in}
                                        {if ($acl->isAllowed('open_job_search_form')) || $GLOBALS.current_user.group.id == "JobSeeker"}
                                            <li>
                                               <a href="{$GLOBALS.site_url}/find-jobs/" >[[Find Jobs]]</a>
                                            </li>
                                        {/if}
                                        {if ($acl->isAllowed('open_resume_search_form')) || $GLOBALS.current_user.group.id == "Employer"}
                                            <li>
                                                <a href="{$GLOBALS.site_url}/search-resumes/" >[[Search Resumes]]</a>
                                            </li>
                                        {/if}
                                        {foreach from=$listingTypesInfo item="listingTypeInfo"}
                                            {if ($acl->isAllowed('post_'|cat:$listingTypeInfo.id))
                                            || $GLOBALS.current_user.group.id == "Employer" && $listingTypeInfo.id == "Job"
                                            || $GLOBALS.current_user.group.id == "JobSeeker" && $listingTypeInfo.id == "Resume"}
                                                <li>
                                                    <img src="{image}sepDot.png" border="0" alt="" />
                                                    <a href="{$GLOBALS.site_url}/add-listing/?listing_type_id={$listingTypeInfo.id}" >
                                                        {if in_array($listingTypeInfo.id, array('Job', 'Resume'))}[[Post {$listingTypeInfo.id}s]]{else}[[Post {$listingTypeInfo.id} Listings]]{/if}
                                                    </a>
                                                </li>
                                            {/if}
                                        {/foreach}
                                    {else}
                                        {if $GLOBALS.current_user.group.id != "Employer"}
                                            <li>
                                               <a href="{$GLOBALS.site_url}/find-jobs/" >[[Find Jobs]]</a>
                                            </li>
                                            <li>
                                               <a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Resume" >[[Post Resumes]]</a>
                                            </li>
                                        {/if}
                                        {if $GLOBALS.current_user.group.id != "JobSeeker"}
                                            <li>
                                                <a href="{$GLOBALS.site_url}/search-resumes/" >[[Search Resumes]]</a>
                                            </li>
                                            <li>
                                                <a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Job" >[[Post Jobs]]</a>
                                            </li>
                                        {/if}
                                    {/if}
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="space-bottom hidden-lg hidden-xs"></div>
            </div>
        </div>
        <!-- .footer end -->

        <!-- .subfooter start -->
        <!-- ================ -->
        <div class="subfooter">
            <div class="container">
                &copy; Shixi.com {$smarty.now|date_format:"%Y"}
            </div>
        </div>
        <!-- .subfooter end -->

    </footer>