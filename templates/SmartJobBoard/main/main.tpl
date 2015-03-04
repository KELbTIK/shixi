<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="keywords" content="[[$KEYWORDS]]" />
<meta name="description" content="[[$DESCRIPTION]]" />  	
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>{$GLOBALS.settings.site_title}{if $TITLE ne ""}: [[$TITLE]] {/if}</title>
<link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/templates/_system/main/images/css/form.css" />
<link rel="StyleSheet" type="text/css" href="{image src="design.css"}" />
{if $GLOBALS.current_language_data.rightToLeft}<link rel="StyleSheet" type="text/css" href="{image src="designRight.css"}" />{/if}
{if $GLOBALS.current_language_data.id == 'sr'}<link rel="StyleSheet" type="text/css" href="{image src="design-sr.css"}" />{/if}
{if $GLOBALS.current_language_data.id == 'pt'}<link rel="StyleSheet" type="text/css" href="{image src="design-pt.css"}" />{/if}
<link rel="alternate" type="application/rss+xml" title="RSS2.0" href="{$GLOBALS.site_url}/rss/" />
<link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/system/ext/jquery/css/jquery-ui.css"  />
<link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/system/ext/jquery/css/jquery.autocomplete.css"  />
<link rel="Stylesheet" type="text/css" href="{$GLOBALS.site_url}/system/ext/jquery/css/jquery.multiselect.css" />
<script language="JavaScript" type="text/javascript" src="{common_js}/main.js"></script>
<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.js"></script>
<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.autocomplete.pack.js"></script>
<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery-ui.js"></script>
<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.form.js"></script>
<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/multilist/jquery.multiselect.min.js"></script>
<script language="JavaScript" type="text/javascript" src="{common_js}/multilist_functions.js"></script>
<script language="JavaScript" type="text/javascript" src="{common_js}/autoupload_functions.js"></script>
<script language="JavaScript" type="text/javascript" src="{common_js}/jquery.poshytip.min.js"></script>
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
	{include file="../menu/header.tpl"}
	<div class="InnerDiv">
		<div class="leftColumn">
			{$MAIN_CONTENT}
			<div class="GrayBlock">
				<h1>[[Featured Companies]]</h1>
				{module name="users" function="featured_profiles" number_of_cols="4" items_count="4"}
			</div>
			<div class="clr"><br/></div>
			<div class="GrayBlock">
				<h1>[[Featured Jobs]]</h1>
				{module name="classifieds" function="featured_listings" number_of_cols="4" items_count="4" listing_type="Job"}
			</div>
			<div class="clr"><br/></div>
			<div class="employerBlock">
				<h1>[[Employers]]</h1>
				<a href="{$GLOBALS.site_url}/registration/?user_group_id=Employer">[[Register]]</a><br/>
				<a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Job">[[Post jobs]]</a><br/>
				<a href="{$GLOBALS.site_url}/search-resumes/">[[Search resumes]]</a><br/>
				<a href="{$GLOBALS.site_url}/resume-alerts/?action=new">[[Get Resumes by Email]]</a>
			</div>
			<div class="jobseekerBlock">
				<h1>[[Job Seekers]]</h1>
				<a href="{$GLOBALS.site_url}/registration/?user_group_id=JobSeeker">[[Register]]</a><br/>
				<a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Resume">[[Post resumes]]</a><br/>
				<a href="{$GLOBALS.site_url}/find-jobs/">[[Find jobs]]</a><br/>
				<a href="{$GLOBALS.site_url}/job-alerts/?action=new">[[Get Jobs by Email]]</a>
			</div>
			<div class="clr"><br/></div>
			<div class="GrayBlock">
				<h1>[[Latest Jobs]]
					<span class="RSSBlock">
						<img src="{image}rss_icon.png" alt="rss" /> <a href="{$GLOBALS.site_url}/listing-feeds/?feedId=10">[[RSS Feed]]</a>
					</span>
				</h1>
				{module name="classifieds" function="latest_listings" number_of_cols="4" items_count="4" listing_type="Job"}
			</div>
			{if isset($GLOBALS.plugins.WordPressBridgePlugin) && $GLOBALS.plugins.WordPressBridgePlugin.active && $GLOBALS.settings.display_blog_on_homepage}
			<div class="clr"><br/></div>
			<div class="GrayBlock">
				<h1>[[Blog Posts]]</h1>
				{module name="miscellaneous" function="blog_page"}
			</div>
			{/if}
		</div>
		<div class="rightColumn">
			{module name="banners" function="show_banners" group="Side Banners"}
			{if $GLOBALS.settings.show_news_on_main_page}
				<div class="rightPanelTitle">&nbsp;&nbsp;[[News]]</div>
				{module name="news" function="show_news"}<br/>
				{/if}
				
				<div class="rightPanelTitle">&nbsp;&nbsp;[[Jobs by Category]]</div>
				{module name="classifieds" function="browse" browseUrl="/browse-by-category/" browse_template="browse_by_category.tpl"}
				
				<div class="rightPanelTitle">&nbsp;&nbsp;[[Jobs by City]]</div>
				{module name="classifieds" function="browse" browseUrl="/browse-by-city/" browse_template="browse_by_city.tpl"}
				
				{if $GLOBALS.settings.show_polls_on_main_page}
					{module name="polls" function="polls"}
			{/if}
			{module name="miscellaneous" function="mailchimp"}
		</div>
		<div class="clr"><br/></div>
		{module name="banners" function="show_banners" group="Bottom Banners"}
		<div class="clr"><br/></div>
	</div>
	{include file="../menu/footer.tpl"}
	{module name="miscellaneous" function="profiler"}
	{if $highlight_templates}
		<div id="highlighterBlock"></div>
	{/if}
</body>
</html>