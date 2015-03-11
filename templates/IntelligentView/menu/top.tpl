<header class="header fixed clearfix">
	<div class="container">
		<div class="row">
			<div class="col-md-2">

				<!-- header-left start -->
				<!-- ================ -->
				<div class="header-left clearfix">

					<!-- logo -->
					<div class="logo">
						<div class="png"></div>
						<a href="{$GLOBALS.site_url}/"><img src="{image}logo.png" border="0" alt="[[{$GLOBALS.settings.logoAlternativeText}]]" title="[[{$GLOBALS.settings.logoAlternativeText}]]" /></a>
					</div>
				</div>
				<!-- header-left end -->

			</div>

			<div class="col-md-10">

				<!-- header-right start -->
				<!-- ================ -->
				<div class="header-right clearfix">

					<!-- main-navigation start -->
					<!-- ================ -->
					<div class="main-navigation animated">

						<!-- navbar start -->
						<!-- ================ -->
						<nav class="navbar navbar-default" role="navigation">
							<div class="container-fluid">

								<!-- Toggle get grouped for better mobile display -->
								<div class="navbar-header">
									<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
										<span class="sr-only">Toggle navigation</span>
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
									</button>
								</div>

								<!-- Collect the nav links, forms, and other content for toggling -->
								<div class="collapse navbar-collapse" id="navbar-collapse-1">
									<ul class="nav navbar-nav navbar-right">


										<li><a href="{$GLOBALS.site_url}/">[[Home]]</a></li>
										{if $GLOBALS.current_user.logged_in}
											<li class="dropdown">
                                                <a class="dropdown-toggle" data-toggle="dropdown" href="{$GLOBALS.site_url}/my-account/">[[My Account]]</a>
												{if $GLOBALS.current_user.group.id == "Employer"}
													{include file="drop_down_menu_employer.tpl"}
												{elseif $GLOBALS.current_user.group.id == "JobSeeker"}
													{include file="drop_down_menu_jobseeker.tpl"}
												{/if}
											</li>
										{/if}
										<li  {if !$GLOBALS.current_user.logged_in}class="dropdown"{/if}>
											{if $GLOBALS.current_user.group.id == "JobSeeker"}
												<a href="{$GLOBALS.site_url}/jobseeker-products/">[[Products]]</a>
											{elseif $GLOBALS.current_user.group.id == "Employer"}
												<a href="{$GLOBALS.site_url}/employer-products/">[[Products]]</a>
											{elseif !$GLOBALS.current_user.group.id}
												<a  class="dropdown-toggle" data-toggle="dropdown" href="#">[[Products]]</a>
												{include file="drop_down_menu_products.tpl"}
											{else}
												<a href="{$GLOBALS.site_url}/{$GLOBALS.current_user.group.id|lower}-products/">[[Products]]</a>
											{/if}
										</li>
										{if $GLOBALS.current_user.logged_in}
											{if ($acl->isAllowed('open_job_search_form')) || $GLOBALS.current_user.group.id == "JobSeeker"}
												<li><a href="{$GLOBALS.site_url}/find-jobs/" >[[Find Jobs]]</a></li>
											{/if}
											{if ($acl->isAllowed('open_resume_search_form')) || $GLOBALS.current_user.group.id == "Employer"}
												<li><a href="{$GLOBALS.site_url}/search-resumes/" >[[Search Resumes]]</a></li>

											{/if}
											{foreach from=$listingTypesInfo item="listingTypeInfo"}
												{if ($acl->isAllowed('post_'|cat:$listingTypeInfo.id))
												|| $GLOBALS.current_user.group.id == "Employer" && $listingTypeInfo.id == "Job"
												|| $GLOBALS.current_user.group.id == "JobSeeker" && $listingTypeInfo.id == "Resume"}
													<li>
														<a href="{$GLOBALS.site_url}/add-listing/?listing_type_id={$listingTypeInfo.id}" >
															{if in_array($listingTypeInfo.id, array('Job', 'Resume'))}[[Post {$listingTypeInfo.name}s]]{else}[[Post {$listingTypeInfo.name} Listings]]{/if}
														</a>
													</li>
												{/if}
											{/foreach}
										{else}
											{if $GLOBALS.current_user.group.id != "Employer"}
												<li><a href="{$GLOBALS.site_url}/find-jobs/" >[[Find Jobs]]</a></li>
												<li><a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Resume" >[[Post Resumes]]</a></li>
											{/if}
											{if $GLOBALS.current_user.group.id != "JobSeeker"}
												<li><a href="{$GLOBALS.site_url}/search-resumes/" >[[Search Resumes]]</a></li>
												<li><a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Job" >[[Post Jobs]]</a></li>
											{/if}
										{/if}
										<li><a href="{$GLOBALS.site_url}/contact/" >[[Contact]]</a></li>
									</ul>
								</div>

							</div>
						</nav>
						<!-- navbar end -->

					</div>
					<!-- main-navigation end -->

				</div>
				<!-- header-right end -->

			</div>
		</div>
	</div>
</header>

<div class="banner">




</div>

