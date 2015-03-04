<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns=http://www.w3.org/1999/xhtml xml:lang=en-US lang="en-US">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="keywords" content="[[$KEYWORDS]]">
		<meta name="description" content="[[$DESCRIPTION]]">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>{$GLOBALS.settings.site_title}{if $TITLE ne ""}:&nbsp;&nbsp;[[$TITLE]] {/if}</title>
		<link rel="StyleSheet" type="text/css" href="{image src="design.css"}">
		<link rel="alternate" type="application/rss+xml" title="RSS2.0" href="{$GLOBALS.site_url}/rss/">
		<link rel="Stylesheet" type="text/css" href="{$GLOBALS.site_url}/system/ext/jquery/css/jquery.multiselect.css" />
		<script language="JavaScript" type="text/javascript" src="{common_js}/main.js"></script>
		<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/multilist/jquery.multiselect.min.js"></script>
		<script language="JavaScript" type="text/javascript" src="{common_js}/multilist_functions.js"></script>
		[[$HEAD]]
	</head>
	<body />
		{module name="users" function="cookie_preferences"}
		<div class="headerpage">
		{include file="../menu/header.tpl"}	
		</div><div class="main">	
			{module name="menu" function="top_menu"}
			<div class="content">
				<div class="rightPanel">
					<div class="rightPanelTitle">&nbsp;&nbsp;[[Jobs by Category]]</div>
					{module name="classifieds" function="browse" browseUrl="/browse-by-category/" browse_template="browse_by_category.tpl"}
					<div class="rightPanelTitle">&nbsp;&nbsp;[[Jobs by City]]</div>
					{module name="classifieds" function="browse" browseUrl="/browse-by-city/" browse_template="browse_by_city.tpl"}
				</div>
				<div class="centerBlock">
						<div class="quickSearchKeep">{$MAIN_CONTENT}</div>
						<br />
						<div class="searchForm" style="border-left-style: none; border-top-style:solid;">
							<div class="headerText">[[Featured Companies]]
							</div>
							{module name="users" function="featured_profiles" number_of_cols="4" items_count="4"}
						</div>
						<br />
						<div class="searchForm" style="border-left-style: none; border-top-style:solid;">
							<div class="headerText">[[Featured Jobs]]
							</div>
							{module name="classifieds" function="featured_listings" number_of_cols="4" items_count="4" listing_type="Job"}
						</div>
						<br/>
						<!-- BLOCK -->
						<div class="bbb" style="float: left;">
									
							<img src="{image}employers_img.png" style="float: left;"/>
							<div style="width: 200px; float:left; margin-left: 20px;">
								<div style="font-size: 14pt; padding:0; margin-top:2px; padding-bottom:10px;">[[Employers]]</div>
								<a href="{$GLOBALS.site_url}/registration/?user_group_id=Employer">[[Register]]</a><br/>
								<a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Job">[[Post jobs]]</a><br/>
								<a href="{$GLOBALS.site_url}/search-resumes/">[[Search resumes]]</a><br/>
								<a href="{$GLOBALS.site_url}/resume-alerts/?action=new">[[Get Resumes by Email]]</a>
							</div>
						</div>
						<div class="bbb">
							<img src="{image}jobseekers_img.png" / style="float: left;">
							<div style="width: 200px; float:left; margin-left: 20px;">
								<div style="font-size: 14pt; padding:0; margin-top:2px; padding-bottom:10px;">[[Job Seekers]]</div>	
								<a href="{$GLOBALS.site_url}/registration/?user_group_id=JobSeeker">[[Register]]</a><br/>
								<a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Resume">[[Post resumes]]</a><br/>
								<a href="{$GLOBALS.site_url}/find-jobs/">[[Find jobs]]</a><br/>
								<a href="{$GLOBALS.site_url}/job-alerts/?action=new">[[Get Jobs by Email]]</a>
							</div>
						</div>
						<!-- END -->
						<br/>
						<div class="searchForm" style="border-left-style: none; border-top-style:solid; float: left; margin-top:15px;">
							<div class="RSSBlock">
								<p style="padiing:0px; margin:0px; float: left; width: 90px;">
									<a href="{$GLOBALS.site_url}/rss/">[[RSS Feed]]</a>
								</p>
								<img src="{image}rss_icon.png" style="float:right; margin-right:5px;"/>
							</div>
							<div class="headerText">[[Latest Jobs]]
							</div>
							{module name="classifieds" function="latest_listings" number_of_cols="4" items_count="4" listing_type="Job"}
						</div>
				</div>
			</div>
			<div class="footerBlock" style="clear:both;">
				<div style="width: 5px; height: 38px; float:left; background-image:url('{image}footer_left_angle.png')"></div>
				<div style="width: 5px; height: 38px; float:right; background-image:url('{image}footer_right_angle.png')"></div>
				<div class="copyright">
					<br />
					[[Powered by]] <a target="_blank" href="http://www.smartjobboard.com" title="Job Board Software, Script">SmartJobBoard Job Board Software</a>
				</div>
			</div>
		</div>
  	{module name="miscellaneous" function="profiler"}
	</body>
</html>