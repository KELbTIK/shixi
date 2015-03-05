    <footer id="footer">

        <!-- .footer start -->
        <!-- ================ -->
        <div class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="footer-content">
                            <div class="logo-footer"><img id="logo-footer" src="images/logo_red_footer.png" alt=""></div>
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
                    <div class="col-sm-6 col-md-2">
                        <div class="footer-content">
                            <h2>Links</h2>
                            <nav>
                                <ul class="nav nav-pills nav-stacked">
                                    <li>
                                        <a href="{$GLOBALS.site_url}/">[[Home]]</a>
                                    </li>
                                    <li>
                                        <img src="{image}sepDot.png" border="0" alt="" /><a href="{$GLOBALS.site_url}/my-account/">[[My Account]]</a>
                                    </li>
                                {if $GLOBALS.current_user.logged_in}
                                    {if ($acl->isAllowed('open_job_search_form')) || $GLOBALS.current_user.group.id == "JobSeeker"}
                                        <li>
                                            <img src="{image}sepDot.png" border="0" alt="" /><a href="{$GLOBALS.site_url}/find-jobs/" >[[Find Jobs]]</a>
                                        </li>
                                    {/if}
                                    {if ($acl->isAllowed('open_resume_search_form')) || $GLOBALS.current_user.group.id == "Employer"}
                                        <li>
                                            <img src="{image}sepDot.png" border="0" alt="" /><a href="{$GLOBALS.site_url}/search-resumes/" >[[Search Resumes]]</a>
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
                                            <img src="{image}sepDot.png" border="0" alt="" /><a href="{$GLOBALS.site_url}/find-jobs/" >[[Find Jobs]]</a>
                                        </li>
                                        <li>
                                            <img src="{image}sepDot.png" border="0" alt="" /><a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Resume" >[[Post Resumes]]</a>
                                        </li>
                                    {/if}
                                    {if $GLOBALS.current_user.group.id != "JobSeeker"}
                                        <li>
                                            <img src="{image}sepDot.png" border="0" alt="" /><a href="{$GLOBALS.site_url}/search-resumes/" >[[Search Resumes]]</a>
                                        </li>
                                        <li>
                                            <img src="{image}sepDot.png" border="0" alt="" /><a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Job" >[[Post Jobs]]</a>
                                        </li>
                                    {/if}
                                {/if}
                                    <li>
                                        <img src="{image}sepDot.png" border="0" alt="" /><a href="{$GLOBALS.site_url}/contact/" >[[Contact]]</a>
                                    </li>
                                    <li>
                                        <img src="{image}sepDot.png" border="0" alt="" /><a href="{$GLOBALS.site_url}/about/">[[About Us]]</a>
                                    </li>
                                    <li>
                                        <img src="{image}sepDot.png" border="0" alt="" /><a href="{$GLOBALS.site_url}/site-map/">[[Sitemap]]</a>
                                    </li>
                                {if isset($GLOBALS.mobileUrl)}
                                    <li>
                                        <img src="{image}sepDot.png" border="0" alt="" /><a href="{$GLOBALS.mobileUrl}{if $GLOBALS.SessionId}?authId={$GLOBALS.SessionId}{/if}">[[Mobile Version]]</a>
                                    </li>
                                {/if}
                                {if $GLOBALS.settings.cookieLaw}
                                    <li>
                                        <img src="{image}sepDot.png" border="0" alt="" /><a href="#" onClick="return cookiePreferencesPopupOpen();">[[Cookie Preferences]]</a>
                                    </li>
                                {/if}
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 col-md-offset-1">
                        <div class="footer-content">
                            <h2>Latest Projects</h2>
                            <div class="gallery row">
                                <div class="gallery-item col-xs-4">
                                    <div class="overlay-container">
                                        <img src="images/gallery-1.jpg" alt="">
                                        <a href="portfolio-item.html" class="overlay small">
                                            <i class="fa fa-link"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="gallery-item col-xs-4">
                                    <div class="overlay-container">
                                        <img src="images/gallery-2.jpg" alt="">
                                        <a href="portfolio-item.html" class="overlay small">
                                            <i class="fa fa-link"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="gallery-item col-xs-4">
                                    <div class="overlay-container">
                                        <img src="images/gallery-3.jpg" alt="">
                                        <a href="portfolio-item.html" class="overlay small">
                                            <i class="fa fa-link"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="gallery-item col-xs-4">
                                    <div class="overlay-container">
                                        <img src="images/gallery-4.jpg" alt="">
                                        <a href="portfolio-item.html" class="overlay small">
                                            <i class="fa fa-link"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="gallery-item col-xs-4">
                                    <div class="overlay-container">
                                        <img src="images/gallery-5.jpg" alt="">
                                        <a href="portfolio-item.html" class="overlay small">
                                            <i class="fa fa-link"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="gallery-item col-xs-4">
                                    <div class="overlay-container">
                                        <img src="images/gallery-6.jpg" alt="">
                                        <a href="portfolio-item.html" class="overlay small">
                                            <i class="fa fa-link"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="gallery-item col-xs-4">
                                    <div class="overlay-container">
                                        <img src="images/gallery-7.jpg" alt="">
                                        <a href="portfolio-item.html" class="overlay small">
                                            <i class="fa fa-link"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="gallery-item col-xs-4">
                                    <div class="overlay-container">
                                        <img src="images/gallery-8.jpg" alt="">
                                        <a href="portfolio-item.html" class="overlay small">
                                            <i class="fa fa-link"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="gallery-item col-xs-4">
                                    <div class="overlay-container">
                                        <img src="images/gallery-9.jpg" alt="">
                                        <a href="portfolio-item.html" class="overlay small">
                                            <i class="fa fa-link"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
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
                <div class="row">
                    <div class="col-md-6">
                        &copy; Shixi.com {$smarty.now|date_format:"%Y"}
                    </div>
                    <div class="col-md-6">
                        <nav class="navbar navbar-default" role="navigation">
                            <!-- Toggle get grouped for better mobile display -->
                            <div class="navbar-header">
                                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-2">
                                    <span class="sr-only">Toggle navigation</span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                </button>
                            </div>
                            <div class="collapse navbar-collapse" id="navbar-collapse-2">
                                <ul class="nav navbar-nav">
                                    <li><a href="index.html">Home</a></li>
                                    <li><a href="page-about.html">About</a></li>
                                    <li><a href="blog-right-sidebar.html">Blog</a></li>
                                    <li><a href="portfolio-3col.html">Portfolio</a></li>
                                    <li><a href="page-contact.html">Contact</a></li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- .subfooter end -->

    </footer>