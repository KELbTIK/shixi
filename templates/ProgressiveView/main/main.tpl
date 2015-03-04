<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="keywords" content="[[$KEYWORDS]]" />
	<meta name="description" content="[[$DESCRIPTION]]" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>{$GLOBALS.settings.site_title}{if $TITLE ne ""}: [[$TITLE]] {/if}</title>
	<link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/templates/_system/main/images/css/form.css" />
	<link rel="StyleSheet" type="text/css" href="{image src="design.css"}" />
	<link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/system/ext/jquery/css/jquery.autocomplete.css"  />
	{if $GLOBALS.current_language_data.rightToLeft}<link rel="StyleSheet" type="text/css" href="{image src="designRight.css"}" />{/if}
	{if $GLOBALS.current_language_data.id == 'de'}<link rel="StyleSheet" type="text/css" href="{image src="design-de.css"}" />{/if}
	{if $GLOBALS.current_language_data.id == 'pl'}<link rel="StyleSheet" type="text/css" href="{image src="design-pl.css"}" />{/if}
	{if $GLOBALS.current_language_data.id == 'fr'}<link rel="StyleSheet" type="text/css" href="{image src="design-fr.css"}" />{/if}
	{if $GLOBALS.current_language_data.id == 'hi'}<link rel="StyleSheet" type="text/css" href="{image src="design-hi.css"}" />{/if}
	{if $GLOBALS.current_language_data.id == 'pt'}<link rel="StyleSheet" type="text/css" href="{image src="design-pt.css"}" />{/if}
	{if $GLOBALS.current_language_data.id == 'ro'}<link rel="StyleSheet" type="text/css" href="{image src="design-ro.css"}" />{/if}
	{if $GLOBALS.current_language_data.id == 'ru'}<link rel="StyleSheet" type="text/css" href="{image src="design-ru.css"}" />{/if}
	{if $GLOBALS.current_language_data.id == 'sr'}<link rel="StyleSheet" type="text/css" href="{image src="design-sr.css"}" />{/if}
	{if $GLOBALS.current_language_data.id == 'zh'}<link rel="StyleSheet" type="text/css" href="{image src="design-zh.css"}" />{/if}
	<link rel="alternate" type="application/rss+xml" title="RSS2.0" href="{$GLOBALS.site_url}/rss/" />
	<link rel="Stylesheet" type="text/css" href="{$GLOBALS.site_url}/system/ext/jquery/css/jquery.multiselect.css" />
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.js"></script>
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.autocomplete.pack.js"></script>
	<link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/system/ext/jquery/css/jquery-ui.css"  />
	<script language="JavaScript" type="text/javascript" src="{common_js}/main.js"></script>
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery-ui.js"></script>
	<script language="JavaScript" type="text/javascript" src="{image src="js/jquery.selectbox-0.2.min.js"}"></script>
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.form.js"></script>
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/multilist/jquery.multiselect.min.js"></script>
	<script language="JavaScript" type="text/javascript" src="{common_js}/multilist_functions.js"></script>
	<script language="JavaScript" type="text/javascript" src="{common_js}/autoupload_functions.js"></script>
	<script language="JavaScript" type="text/javascript" src="{common_js}/jquery.poshytip.min.js"></script>
	<!--[if IE 8]>
		<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/pie-ie.js"></script>
		<script language="javascript" type="text/javascript">
			$(function() {
				$("#employers").addClass("ie-employers");
				if (window.PIE) {
					$('input, .white-block, #quick-search, .ie-employers, .find-button-zoom, .label, a.button, a.standart-button, .products, .productLinks').each(function() {
						PIE.attach(this);
					});
				}
			});
		</script>
	<![endif]-->
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

	<script language="javascript" type="text/javascript">
		$.ui.dialog.prototype.options.bgiframe = true;

		function popUpWindow(url, widthWin, title, parentReload, userLoggedIn, callbackFunction) {
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

</head>
<body>
	<div class="main-wrapper">
		{module name="users" function="cookie_preferences"}
		<div id="loading"></div>
		<div id="messageBox"></div>
		<div id="header-bg">
			<div id="header-bg-in"></div>
		</div>
		<div class="main-div">
			{include file="../menu/header.tpl"}
			<div class="clr"></div>
			{$MAIN_CONTENT}
			<div class="clr"><br/></div>
			<div id="left-column">
				<script type="text/javascript">
					{literal}
					$(function(){
						$("#jTabs").tabs();
					});
					{/literal}
				</script>
				<div>
					<div class="css-tabs skin1" id="jTabs">
						<ul>
							<li><a href="#tabs-1">[[Jobs by Category]]</a></li>
							<li><a href="#tabs-2">[[Jobs by City]]</a></li>
							<li><a href="#tabs-3">[[Jobs by State]]</a></li>
						</ul>
						<div class="css-panes skin2">
							<div id="tabs-1">{module name="classifieds" function="browse" browseUrl="/browse-by-category/" browse_template="browse_by_category.tpl"}</div>
							<div id="tabs-2">{module name="classifieds" function="browse" browseUrl="/browse-by-city/" browse_template="browse_by_city.tpl"}</div>
							<div id="tabs-3">{module name="classifieds" function="browse" browseUrl="/browse-by-state/" browse_template="browse_by_state.tpl"}</div>
						</div>
					</div>
				</div>
				<div class="clr"><br/></div>
				<div id="featured-listings">
					<div id="featured-head">[[Featured Jobs]]</div>
					<div id="featured">{module name="classifieds" function="featured_listings" items_count="4" listing_type="Job"}</div>
				</div>
				<div id="latest-listings">
					<div id="latest-head">[[Latest Jobs]]</div>
					<div id="latest">{module name="classifieds" function="latest_listings" items_count="4" listing_type="Job"}</div>
				</div>
				<div class="clr"><br/></div>
				<div class="job-seeker-tools">
					<div class="header">[[Job Seeker Tools]]</div>
					<ul>
						<li><a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Resume"><img src="{image}post-resume.png" alt="Post Resume" border="0" /><br/>[[Post resumes]]</a></li>
						<li><a href="{$GLOBALS.site_url}/find-jobs/"><img src="{image}find-zoom.png" alt="Find Jobs" border="0" /><br/>[[Find Jobs]]</a></li>
						<li><a href="{$GLOBALS.site_url}/job-alerts/?action=new"><img src="{image}get-by-mail.png" alt="Get Jobs by Email" border="0" /><br/>[[Get Jobs by Email]]</a></li>
					</ul>
				</div>
				<div class="clr"><br/></div>

				{if isset($GLOBALS.plugins.WordPressBridgePlugin) && $GLOBALS.plugins.WordPressBridgePlugin.active && $GLOBALS.settings.display_blog_on_homepage}
				<div class="job-seeker-tools blog-posts">
					<div class="header">[[Blog Posts]]</div>
					{module name="miscellaneous" function="blog_page"}
				</div>
				{/if}
				<div class="clr"><br/></div>
				{module name="banners" function="show_banners" group="Bottom Banners"}
			</div>
			<div id="right-column">
				<div class="white-block featured-companies">
					<h2>[[Featured Companies]]</h2>
					<span class="sep-line">&nbsp;</span>
					{module name="users" function="featured_profiles" items_count="4" number_of_cols="2"}
					<div class="clr"><br/></div>
					<div class="view-all">
						<a href="{$GLOBALS.site_url}/browse-by-company/">[[View All Companies]]</a>
					</div>
				</div>

				{if $GLOBALS.settings.show_news_on_main_page}
					<div class="white-block latest-news">
						<h2>[[News]]</h2>
						<span class="sep-line">&nbsp;</span>
						{module name="news" function="show_news"}
					</div>
				{/if}


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
		<div id="highlighterBlock"></div>
	{/if}
</body>
</html>