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
											<li {if $GLOBALS.current_user.logged_in}id="dropDown"{/if}><a href="{$GLOBALS.site_url}/my-account/">[[My Account]]</a>
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

	<!-- slideshow start -->
	<!-- ================ -->
	<div class="slideshow">

		<!-- slider revolution start -->
		<!-- ================ -->
		<div class="slider-banner-container">
			<div class="slider-banner">
				<ul>
					<!-- slide 1 start -->
					<li data-transition="random" data-slotamount="7" data-masterspeed="500" data-saveperformance="on" data-title="Premium HTML5 template">

						<!-- main image -->
						<img src="{$GLOBALS.site_url}/images/slider-1-slide-1.jpg"  alt="slidebg1" data-bgposition="center top" data-bgfit="cover" data-bgrepeat="no-repeat">

						<!-- LAYER NR. 1 -->
						<div class="tp-caption default_bg large sfr tp-resizeme"
							 data-x="0"
							 data-y="70"
							 data-speed="600"
							 data-start="1200"
							 data-end="9400"
							 data-endspeed="600">Premium HTML5 template
						</div>

						<!-- LAYER NR. 2 -->
						<div class="tp-caption dark_gray_bg sfl medium tp-resizeme"
							 data-x="0"
							 data-y="170"
							 data-speed="600"
							 data-start="1600"
							 data-end="9400"
							 data-endspeed="600"><i class="icon-check"></i>
						</div>

						<!-- LAYER NR. 3 -->
						<div class="tp-caption light_gray_bg sfb medium tp-resizeme"
							 data-x="50"
							 data-y="170"
							 data-speed="600"
							 data-start="1600"
							 data-end="9400"
							 data-endspeed="600">100% Responsive
						</div>

						<!-- LAYER NR. 4 -->
						<div class="tp-caption dark_gray_bg sfl medium tp-resizeme"
							 data-x="0"
							 data-y="220"
							 data-speed="600"
							 data-start="1800"
							 data-end="9400"
							 data-endspeed="600"><i class="icon-check"></i>
						</div>

						<!-- LAYER NR. 5 -->
						<div class="tp-caption light_gray_bg sfb medium tp-resizeme"
							 data-x="50"
							 data-y="220"
							 data-speed="600"
							 data-start="1800"
							 data-end="9400"
							 data-endspeed="600">Bootstrap Based
						</div>

						<!-- LAYER NR. 6 -->
						<div class="tp-caption dark_gray_bg sfl medium tp-resizeme"
							 data-x="0"
							 data-y="270"
							 data-speed="600"
							 data-start="2000"
							 data-end="9400"
							 data-endspeed="600"><i class="icon-check"></i>
						</div>

						<!-- LAYER NR. 7 -->
						<div class="tp-caption light_gray_bg sfb medium tp-resizeme"
							 data-x="50"
							 data-y="270"
							 data-speed="600"
							 data-start="2000"
							 data-end="9400"
							 data-endspeed="600">Packed Full of Features
						</div>

						<!-- LAYER NR. 8 -->
						<div class="tp-caption dark_gray_bg sfl medium tp-resizeme"
							 data-x="0"
							 data-y="320"
							 data-speed="600"
							 data-start="2200"
							 data-end="9400"
							 data-endspeed="600"><i class="icon-check"></i>
						</div>

						<!-- LAYER NR. 9 -->
						<div class="tp-caption light_gray_bg sfb medium tp-resizeme"
							 data-x="50"
							 data-y="320"
							 data-speed="600"
							 data-start="2200"
							 data-end="9400"
							 data-endspeed="600">Very Easy to Customize
						</div>

						<!-- LAYER NR. 10 -->
						<div class="tp-caption dark_gray_bg sfb medium tp-resizeme"
							 data-x="0"
							 data-y="370"
							 data-speed="600"
							 data-start="2400"
							 data-end="9400"
							 data-endspeed="600">And Much More...
						</div>

						<!-- LAYER NR. 11 -->
						<div class="tp-caption sfr tp-resizeme"
							 data-x="right"
							 data-y="center"
							 data-speed="600"
							 data-start="2700"
							 data-end="9400"
							 data-endspeed="600"><img src="{$GLOBALS.site_url}/images/slider-1-layer-1.png" alt="">
						</div>

					</li>
					<!-- slide 1 end -->

					<!-- slide 2 start -->
					<li data-transition="random" data-slotamount="7" data-masterspeed="500" data-saveperformance="on" data-title="Powerful Bootstrap Theme">

						<!-- main image -->
						<img src="{$GLOBALS.site_url}/images/slider-1-slide-2.jpg"  alt="slidebg1" data-bgposition="center top" data-bgfit="cover" data-bgrepeat="no-repeat">

						<!-- LAYER NR. 1 -->
						<div class="tp-caption white_bg large sfr tp-resizeme"
							 data-x="0"
							 data-y="70"
							 data-speed="600"
							 data-start="1200"
							 data-end="9400"
							 data-endspeed="600">Powerful Bootstrap Theme
						</div>

						<!-- LAYER NR. 2 -->
						<div class="tp-caption default_bg sfl medium tp-resizeme"
							 data-x="0"
							 data-y="170"
							 data-speed="600"
							 data-start="1600"
							 data-end="9400"
							 data-endspeed="600"><i class="icon-check"></i>
						</div>

						<!-- LAYER NR. 3 -->
						<div class="tp-caption white_bg sfb medium tp-resizeme"
							 data-x="50"
							 data-y="170"
							 data-speed="600"
							 data-start="1600"
							 data-end="9400"
							 data-endspeed="600">W3C Validated
						</div>

						<!-- LAYER NR. 4 -->
						<div class="tp-caption default_bg sfl medium tp-resizeme"
							 data-x="0"
							 data-y="220"
							 data-speed="600"
							 data-start="1800"
							 data-end="9400"
							 data-endspeed="600"><i class="icon-check"></i>
						</div>

						<!-- LAYER NR. 5 -->
						<div class="tp-caption white_bg sfb medium tp-resizeme"
							 data-x="50"
							 data-y="220"
							 data-speed="600"
							 data-start="1800"
							 data-end="9400"
							 data-endspeed="600">Unlimited layout variations
						</div>

						<!-- LAYER NR. 6 -->
						<div class="tp-caption default_bg sfl medium tp-resizeme"
							 data-x="0"
							 data-y="270"
							 data-speed="600"
							 data-start="2000"
							 data-end="9400"
							 data-endspeed="600"><i class="icon-check"></i>
						</div>

						<!-- LAYER NR. 7 -->
						<div class="tp-caption white_bg sfb medium tp-resizeme"
							 data-x="50"
							 data-y="270"
							 data-speed="600"
							 data-start="2000"
							 data-end="9400"
							 data-endspeed="600">Google Maps
						</div>

						<!-- LAYER NR. 8 -->
						<div class="tp-caption default_bg sfl medium tp-resizeme"
							 data-x="0"
							 data-y="320"
							 data-speed="600"
							 data-start="2200"
							 data-end="9400"
							 data-endspeed="600"><i class="icon-check"></i>
						</div>

						<!-- LAYER NR. 9 -->
						<div class="tp-caption white_bg sfb medium tp-resizeme"
							 data-x="50"
							 data-y="320"
							 data-speed="600"
							 data-start="2200"
							 data-end="9400"
							 data-endspeed="600">Very Flexible
						</div>

						<!-- LAYER NR. 10 -->
						<div class="tp-caption default_bg sfb medium tp-resizeme"
							 data-x="0"
							 data-y="370"
							 data-speed="600"
							 data-start="2400"
							 data-end="9400"
							 data-endspeed="600">And Much More...
						</div>

						<!-- LAYER NR. 11 -->
						<div class="tp-caption sfr tp-resizeme"
							 data-x="right"
							 data-y="center"
							 data-speed="600"
							 data-start="2700"
							 data-end="9400"
							 data-endspeed="600"><img src="{$GLOBALS.site_url}/images/slider-1-layer-2.png" alt="">
						</div>

					</li>
					<!-- slide 2 end -->

					<!-- slide 3 start -->
					<li data-transition="random" data-slotamount="7" data-masterspeed="500" data-saveperformance="on" data-title="Powerful Bootstrap Theme">

						<!-- main image -->
						<img src="{$GLOBALS.site_url}/images/slider-1-slide-3.jpg"  alt="kenburns"  data-bgposition="left center" data-kenburns="on" data-duration="10000" data-ease="Linear.easeNone" data-bgfit="100" data-bgfitend="115" data-bgpositionend="right center">

						<!-- LAYER NR. 1 -->
						<div class="tp-caption white_bg large sfr tp-resizeme"
							 data-x="0"
							 data-y="70"
							 data-speed="600"
							 data-start="1200"
							 data-end="9400"
							 data-endspeed="600">Clean &amp; Unique Design
						</div>

						<!-- LAYER NR. 2 -->
						<div class="tp-caption dark_gray_bg sfl medium tp-resizeme"
							 data-x="0"
							 data-y="170"
							 data-speed="600"
							 data-start="1600"
							 data-end="9400"
							 data-endspeed="600"><i class="icon-check"></i>
						</div>

						<!-- LAYER NR. 3 -->
						<div class="tp-caption white_bg sfb medium tp-resizeme"
							 data-x="50"
							 data-y="170"
							 data-speed="600"
							 data-start="1600"
							 data-end="9400"
							 data-endspeed="600">After Sale Support
						</div>

						<!-- LAYER NR. 4 -->
						<div class="tp-caption dark_gray_bg sfl medium tp-resizeme"
							 data-x="0"
							 data-y="220"
							 data-speed="600"
							 data-start="1800"
							 data-end="9400"
							 data-endspeed="600"><i class="icon-check"></i>
						</div>

						<!-- LAYER NR. 5 -->
						<div class="tp-caption white_bg sfb medium tp-resizeme"
							 data-x="50"
							 data-y="220"
							 data-speed="600"
							 data-start="1800"
							 data-end="9400"
							 data-endspeed="600">Crystal Clean Code
						</div>

						<!-- LAYER NR. 6 -->
						<div class="tp-caption dark_gray_bg sfl medium tp-resizeme"
							 data-x="0"
							 data-y="270"
							 data-speed="600"
							 data-start="2000"
							 data-end="9400"
							 data-endspeed="600"><i class="icon-check"></i>
						</div>

						<!-- LAYER NR. 7 -->
						<div class="tp-caption white_bg sfb medium tp-resizeme"
							 data-x="50"
							 data-y="270"
							 data-speed="600"
							 data-start="2000"
							 data-end="9400"
							 data-endspeed="600">Crossbrowser Compatible
						</div>

						<!-- LAYER NR. 8 -->
						<div class="tp-caption dark_gray_bg sfl medium tp-resizeme"
							 data-x="0"
							 data-y="320"
							 data-speed="600"
							 data-start="2200"
							 data-end="9400"
							 data-endspeed="600"><i class="icon-check"></i>
						</div>

						<!-- LAYER NR. 9 -->
						<div class="tp-caption white_bg sfb medium tp-resizeme"
							 data-x="50"
							 data-y="320"
							 data-speed="600"
							 data-start="2200"
							 data-end="9400"
							 data-endspeed="600">Latest Technologies Used
						</div>

						<!-- LAYER NR. 10 -->
						<div class="tp-caption dark_gray_bg sfb medium tp-resizeme"
							 data-x="0"
							 data-y="370"
							 data-speed="600"
							 data-start="2400"
							 data-end="9400"
							 data-endspeed="600">Don't miss out!
						</div>

						<!-- LAYER NR. 11 -->
						<div class="tp-caption sfr"
							 data-x="right" data-hoffset="-660"
							 data-y="center"
							 data-speed="600"
							 data-start="2700"
							 data-endspeed="600"
							 data-autoplay="false"
							 data-autoplayonlyfirsttime="false"
							 data-nextslideatend="true">
							<div class="embed-responsive embed-responsive-16by9">
								<iframe class="embed-responsive-item" src='https://www.youtube.com/embed/v1uyQZNg2vE?enablejsapi=1&amp;html5=1&amp;hd=1&amp;wmode=opaque&amp;controls=1&amp;showinfo=0;rel=0;' width='640' height='360' style='width:640px;height:360px;'></iframe>
							</div>
						</div>

					</li>
					<!-- slide 3 end -->

				</ul>
				<div class="tp-bannertimer tp-bottom"></div>
			</div>
		</div>
		<!-- slider revolution end -->

	</div>
	<!-- slideshow end -->

</div>

