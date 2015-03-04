<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns=http://www.w3.org/1999/xhtml xml:lang=en-US lang="en-US">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="keywords" content="[[$KEYWORDS]]" />
	<meta name="description" content="[[$DESCRIPTION]]" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>{$GLOBALS.settings.site_title}{if $TITLE ne ""}:&nbsp;&nbsp;[[$TITLE]] {/if}</title>
	<link rel="StyleSheet" type="text/css" href="{image src="design.css"}" />
	<link rel="alternate" type="application/rss+xml" title="RSS2.0" href="{$GLOBALS.site_url}/rss/" />
	{if $GLOBALS.current_language_data.rightToLeft}<link rel="StyleSheet" type="text/css" href="{image src="designRight.css"}" />{/if}
	{if $GLOBALS.current_language_data.id == 'de'}<link rel="StyleSheet" type="text/css" href="{image src="design-de.css"}" />{/if}
	{if $GLOBALS.current_language_data.id == 'fr'}<link rel="StyleSheet" type="text/css" href="{image src="design-fr.css"}" />{/if}
	{if $GLOBALS.current_language_data.id == 'hi'}<link rel="StyleSheet" type="text/css" href="{image src="design-hi.css"}" />{/if}
	{if $GLOBALS.current_language_data.id == 'pl'}<link rel="StyleSheet" type="text/css" href="{image src="design-pl.css"}" />{/if}
	{if $GLOBALS.current_language_data.id == 'ro'}<link rel="StyleSheet" type="text/css" href="{image src="design-ro.css"}" />{/if}
	{if $GLOBALS.current_language_data.id == 'sr'}<link rel="StyleSheet" type="text/css" href="{image src="design-sr.css"}" />{/if}
	{if $GLOBALS.current_language_data.id == 'zh'}<link rel="StyleSheet" type="text/css" href="{image src="design-zh.css"}" />{/if}
	{if $GLOBALS.current_language_data.id == 'ru'}<link rel="StyleSheet" type="text/css" href="{image src="design-ru.css"}" />{/if}
	<link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/templates/_system/main/images/css/form.css" />
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.js"></script>
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.autocomplete.pack.js"></script>
	<link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/system/ext/jquery/css/jquery-ui.css"  />
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery-ui.js"></script>
	<script language="JavaScript" type="text/javascript" src="{image src="js/jquery.selectbox-0.2.min.js"}"></script>
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.form.js"></script>
	<script language="JavaScript" type="text/javascript" src="{common_js}/autoupload_functions.js"></script>
	<script language="JavaScript" type="text/javascript" src="{common_js}/jquery.poshytip.min.js"></script>
	<!--[if IE 8]>
		<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/pie-ie.js"></script>
		<script language="javascript" type="text/javascript">
			$(function() {
				if (window.PIE) {
					$("#employers").addClass("ie-employers");
					$('input, .get-started a, .ie-employers').each(function() {
						PIE.attach(this);
					});
				}
			});
		</script>
	<![endif]-->
	<script language="javascript" type="text/javascript">

		// Set global javascript value for page
		window.SJB_GlobalSiteUrl = '{$GLOBALS.site_url}';
		window.SJB_UserSiteUrl   = '{$GLOBALS.user_site_url}';

		$.ui.dialog.prototype.options.bgiframe = true;

		function popUpWindow(url, widthWin, title, parentReload, userLoggedIn){
			reloadPage = false;
			$("#loading").show();
			$("#messageBox").dialog( 'destroy' ).html('{capture name="displayJobProgressBar"}<img style="vertical-align: middle;" src="{$GLOBALS.site_url}/system/ext/jquery/progbar.gif" alt="[[Please wait ...]]" /> [[Please wait ...]]{/capture}{$smarty.capture.displayJobProgressBar|escape:'quotes'}');
			$("#messageBox").dialog({
				autoOpen: false,
				width: widthWin,
				height: 'auto',
				modal: true,
				title: title,
				close: function(event, ui) {
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
	[[$HEAD]]
	{if $highlight_templates}
		<!-- AJAX EDIT TEMPLATE SECTION -->
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

				$(".editTemplateMenu").live('click', function() {
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
</head>
<body>
	<div class="main-wrapper">
		{module name="users" function="cookie_preferences"}
		<div id="loading"></div>
		<div id="messageBox"></div>
		<div id="header-bg" class="emp-header">
			<div id="header-bg-in" class="emp-header-in"></div>
		</div>
		<div class="main-div">
			{include file="../menu/header.tpl"}
			<div class="clr"><br/></div>
			<div class="clr"><br/></div>
			<div id="employer-bg">
				<div class="left-side">
					<h1>[[Easy Job Posting]]:</h1>
					<ul>
						<li>[[Shop for products]]</li>
						<li>[[Pay for them]]</li>
						<li>[[Post your jobs]]</li>
					</ul>
					<div class="get-started"><a href="{$GLOBALS.site_url}/employer-products/">[[Get Started]]</a></div>
				</div>
				<div class="right-buts">
					<a class="post-job" href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Job"><span>[[Post Job]]</span></a>
					<a class="search-resume" href="{$GLOBALS.site_url}/search-resumes/"><span>[[Search Resumes]]</span></a>
					<a class="get-resume" href="{$GLOBALS.site_url}/resume-alerts/?action=new"><span>[[Get Resumes by Email]]</span></a>
				</div>
			</div>
			<div id="left-column">
				{if $GLOBALS.settings.show_news_on_main_page}
					<div class="employer-news-blog">
						<h3>[[News]]</h3>
						{module name="news" function="show_news"}
					</div>
				{/if}
				{if isset($GLOBALS.plugins.WordPressBridgePlugin) && $GLOBALS.plugins.WordPressBridgePlugin.active && $GLOBALS.settings.display_blog_on_homepage}
					<div class="employer-news-blog">
						<h2>[[Blog Posts]]</h2>
						{module name="miscellaneous" function="blog_page"}
					</div>
				{/if}
				{module name="banners" function="show_banners" group="Bottom Banners"}
			</div>
			<div id="right-column">

				{if $GLOBALS.settings.show_polls_on_main_page}
					<div class="white-block polls">
						<h2>[[Poll]]</h2>
						<span class="sep-line">&nbsp;</span>
						{module name="polls" function="polls"}
					</div>
				{/if}

				{if $GLOBALS.settings.show_news_on_main_page}
					<div class="white-block newsletter">
						<h2>[[Newsletter]]</h2>
						<span class="sep-line">&nbsp;</span>
						{module name="miscellaneous" function="mailchimp"}
					</div>
				{/if}
			</div>
		</div>
		<div class="clr"><br/></div>
		{include file="../menu/footer.tpl"}
	</div>

	{module name="miscellaneous" function="profiler"}

	{if $highlight_templates}
		<div id="highlighterBlock" style="display:none;background-color: #ccc; opacity: 0.5;"></div>
	{/if}
</body>
</html>

