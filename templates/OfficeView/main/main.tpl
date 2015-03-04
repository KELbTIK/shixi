<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
  <head>
<meta name="keywords" content="[[$KEYWORDS]]" />
<meta name="description" content="[[$DESCRIPTION]]" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<title>{$GLOBALS.settings.site_title}{if $TITLE ne ""}: [[$TITLE]] {/if}</title>
<link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/templates/_system/main/images/css/form.css" />
<link rel="StyleSheet" type="text/css" href="{image src="design.css"}" />
<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.js"></script>
<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.autocomplete.pack.js"></script>
{if $GLOBALS.current_language_data.rightToLeft}<link rel="StyleSheet" type="text/css" href="{image src="designRight.css"}" />{/if}
{if $GLOBALS.current_language_data.id == 'de'}<link rel="StyleSheet" type="text/css" href="{image src="design-de.css"}" />{/if}
{if $GLOBALS.current_language_data.id == 'fr'}<link rel="StyleSheet" type="text/css" href="{image src="design-fr.css"}" />{/if}
{if $GLOBALS.current_language_data.id == 'pt'}<link rel="StyleSheet" type="text/css" href="{image src="design-pt.css"}" />{/if}
{if $GLOBALS.current_language_data.id == 'sr'}<link rel="StyleSheet" type="text/css" href="{image src="design-sr.css"}" />{/if}
{if $GLOBALS.current_language_data.id == 'hi'}<link rel="StyleSheet" type="text/css" href="{image src="design-hi.css"}" />{/if}
<link rel="alternate" type="application/rss+xml" title="RSS2.0" href="{$GLOBALS.site_url}/rss/" />
<link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/system/ext/jquery/css/jquery.autocomplete.css"  />
<link rel="Stylesheet" type="text/css" href="{$GLOBALS.site_url}/system/ext/jquery/css/jquery.multiselect.css" />

<!-- AJAX EDIT TEMPLATE SECTION -->
<link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/system/ext/jquery/css/jquery-ui.css"  />
<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery-ui.js"></script>
<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.form.js"></script>
<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/multilist/jquery.multiselect.min.js"></script>
<script language="JavaScript" type="text/javascript" src="{common_js}/multilist_functions.js"></script>
<script language="JavaScript" type="text/javascript" src="{common_js}/autoupload_functions.js"></script>
<script language="JavaScript" type="text/javascript" src="{common_js}/jquery.poshytip.min.js"></script>
<script language="JavaScript" type="text/javascript" src="{common_js}/main.js"></script>
<!--[if IE 8]>
<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/pie-ie.js"></script>
<script language="JavaScript" type="text/javascript">
  $(function() {
	  if (window.PIE) {
		  $('input').each(function() {
			  PIE.attach(this);
		  });
	  }
  });
</script>
<![endif]-->
[[$HEAD]]

{if $highlight_templates}
<script language="javascript" type="text/javascript">
$(document).ready(function(){
	$("html").addClass("highlight-body");
});
$(function() {
	
	$("div.inner_div").bind("mouseenter", function(){
		var width	= $(this).parent().css('width');
		var height	= $(this).parent().css('height');
		var offset	= $(this).parent().offset();

		// inner_block css-class z-index = 11
		// set highlight z-index = 10
		$("#highlighterBlock").css({
			'display':'block',
			'position':'absolute',
			'top':offset.top,
			'left':offset.left,
			'width':width,
			'height':height,
			'z-index': 10
		});
	});
	$("div.inner_div").bind("mouseleave", function(){
		$("#highlighterBlock").css({
			'display':'none'
		});
	});

	// lets catch clicks on 'edit template' links
	$("a.editTemplateLink").click(function() {
		//alert( $(this).attr('title'));
		var templateName	= $(this).attr('title');
		var link			= $(this).attr('href');
		editTemplateMenu(templateName, link);
		return false;
	});

	$("a.editTemplateMenu").live('click', function() {
		var url = $(this).attr('href');
		popUpWindow(url, 700, 'Edit Template', true);
		return false;
	});

	function editTemplateMenu(templateName, url) {
		var title = 'Template';
		$("#messageBox").dialog( 'destroy' ).html('<b>Template Name:</b><br />' + templateName + '<br /><br /><a class="editTemplateMenu" style="font-weight: bold; color: #00f;" href="'+url+'" target="_blank">Edit this template</a>');
		$("#messageBox").dialog({
			width: 300,
			height: 150,
			modal: true,
			title: title
		}).dialog( 'open' );

		return false;
	}

});
</script>
<!-- END OF AJAX EDIT TEMPLATE SECTION -->
{/if}
		{literal}
		<script language="javascript" type="text/javascript">
		$.ui.dialog.prototype.options.bgiframe = true;

		function popUpWindow(url, widthWin, title, parentReload, userLoggedIn, callbackFunction) {
			reloadPage = false;
			$("#loading").show();
			$("#messageBox").dialog( 'destroy' ).html({/literal}'{capture name="displayJobProgressBar"}<img style="vertical-align: middle;" src="{$GLOBALS.site_url}/system/ext/jquery/progbar.gif" alt="[[Please wait ...]]" /> [[Please wait ...]]{/capture}{$smarty.capture.displayJobProgressBar|escape:'quotes'}'{literal});
			$("#messageBox").dialog({
				autoOpen: false,
				width: widthWin,
				height: 'auto',
				modal: true,
				title: title,
				position: ['top', 'center'],
				close: function(event, ui) {
					if (callbackFunction) {
						callbackFunction();
					}
					if (parentReload == true && !userLoggedIn && reloadPage == true) {
						parent.document.location.reload();
					}
				}
			}).hide();

			$.get(url, function(data){
				$("#messageBox").html(data).dialog("open").show();
				$("#loading").hide();
			});

			return false;
		}
		</script>
		{/literal}
	</head>
<body>
	{module name="users" function="cookie_preferences"}
	<div id="loading"></div>
	<div id="messageBox"></div>
	<div id="site_url" style="display:none">{$GLOBALS.site_url}</div>
	<div id="pleaseWait" style="display:none">[[Please wait ...]]</div>
	{include file="../menu/header.tpl"}
		<div id="container">
			<div id="inner">
			
				<div id="leftColumn">
					{if $GLOBALS.current_user.logged_in}
						<div id="loginFormMini">
							<p>[[You are currently logged in as]] {if $GLOBALS.current_user.subuser}<span class="longtext-25">{$GLOBALS.current_user.subuser.username}</span>{else}<span class="longtext-25">{$GLOBALS.current_user.username}</span>{/if}</p>
							<p><a href="{$GLOBALS.site_url}/logout/">[[Logout]]</a></p>
						</div>
					{else}
						<div id="blockBg">
							<div id="blockTop"></div>
							<div id="blockInner">{module name="users" function="login" template="login.tpl" internal="true"}</div>
						</div>
					{/if}
					
					<h3>[[Featured Companies]]</h3>
					{module name="users" function="featured_profiles" items_count="4"}
					
					{if $GLOBALS.settings.show_polls_on_main_page}
						{module name="polls" function="polls"}
					{/if}
					{module name="miscellaneous" function="mailchimp"}
				</div>
				
				<div id="rightColumn">
					<br/>{module name="banners" function="show_banners" group="Side Banners"}<br/>
					
					{if $GLOBALS.settings.show_news_on_main_page}
						<h3>[[News]]</h3>
						{module name="news" function="show_news"}
						<div class="clr"><br/></div>
					{/if}
					
					<h3>[[Jobs by Category]]</h3>
					{module name="classifieds" function="browse" browseUrl="/browse-by-category/" browse_template="browse_by_category.tpl"}
					<div class="clr"><br /></div>

					<h3>[[Jobs by City]]</h3>
					{module name="classifieds" function="browse" browseUrl="/browse-by-city/" browse_template="browse_by_city.tpl"}
					<div class="clr"><br /></div>
				</div>
				
				<div id="middleColumn">
					{$MAIN_CONTENT}
	
					<div id="groupsBlocks">
						<div id="jobSeker">
							<h2>[[Job Seekers]]</h2>
							<ul>
								<li><a href="{$GLOBALS.site_url}/registration/?user_group_id=JobSeeker">[[Register]]</a></li>
								<li><a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Resume">[[Post resumes]]</a></li>
								<li><a href="{$GLOBALS.site_url}/find-jobs/">[[Find jobs]]</a></li>
								<li><a href="{$GLOBALS.site_url}/job-alerts/?action=new">[[Get Jobs by Email]]</a></li>
							</ul>
						</div>

						<div id="employer">
							<h2>[[Employers]]</h2>
							<ul>
								<li><a href="{$GLOBALS.site_url}/registration/?user_group_id=Employer">[[Register]]</a></li>
								<li><a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Job">[[Post jobs]]</a></li>
								<li><a href="{$GLOBALS.site_url}/search-resumes/">[[Search resumes]]</a></li>
								<li><a href="{$GLOBALS.site_url}/resume-alerts/?action=new">[[Get Resumes by Email]]</a></li>
							</ul>
						</div>
					</div>
					
					<div id="featurdJobsBlock">
						<h2>[[Featured Jobs]]</h2>
						{module name="classifieds" function="featured_listings" items_count="4" listing_type="Job"}
					</div>

					<div id="latestJobsBlock">
						<h2>[[Latest Jobs]]</h2>
						{module name="classifieds" function="latest_listings" items_count="4" listing_type="Job"}
					</div>

					{if isset($GLOBALS.plugins.WordPressBridgePlugin) && $GLOBALS.plugins.WordPressBridgePlugin.active && $GLOBALS.settings.display_blog_on_homepage}
						<div id="blogBlock">
							<h2>[[Blog Posts]]</h2>
							{module name="miscellaneous" function="blog_page"}
						</div>
					{/if}
					<div class="clr"><br/></div>
				</div>
				<div class="clr"></div>
			</div>
		</div>
	<div id="grayBgBanner">{module name="banners" function="show_banners" group="Bottom Banners"}</div>
	{include file="../menu/footer.tpl"}
	{module name="miscellaneous" function="profiler"}
	{if $highlight_templates}
		<div id="highlighterBlock"></div>
	{/if}
</body>
</html>