<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
	<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="keywords" content="[[$KEYWORDS]]" />
	<meta name="description" content="[[$DESCRIPTION]]" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>{if !$GLOBALS.page_not_found}{$GLOBALS.settings.site_title}{/if}{if $TITLE ne ""}{if !$GLOBALS.page_not_found}:{/if} [[$TITLE]]{/if}</title>
	<link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/templates/_system/main/images/css/form.css" />
	<link rel="StyleSheet" type="text/css" href="{image src="design.css"}" />
	{if $GLOBALS.current_language_data.rightToLeft}<link rel="StyleSheet" type="text/css" href="{image src="designRight.css"}" />{/if}
	{if $GLOBALS.current_language_data.id == 'pt'}<link rel="StyleSheet" type="text/css" href="{image src="design-pt.css"}" />{/if}
	{if $GLOBALS.current_language_data.id == 'sr'}<link rel="StyleSheet" type="text/css" href="{image src="design-sr.css"}" />{/if}
	<link rel="alternate" type="application/rss+xml" title="RSS2.0" href="{$GLOBALS.site_url}/rss/" />
	<link rel="stylesheet" href="{$GLOBALS.site_url}/system/lib/rating/style.css" type="text/css" />
	<link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/system/ext/jquery/css/jquery-ui.css"  />
	<link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/system/ext/jquery/css/jquery.autocomplete.css" />
	<link rel="Stylesheet" type="text/css" href="{$GLOBALS.site_url}/system/ext/jquery/css/jquery.multiselect.css" />

		<link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/bootstrap/css/bootstrap.css"  />
		<link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/css/animate.css"  />
		<link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/css/skins/red.css"  />
		<link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/css/normalize.css"  />
		<link href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700,300&amp;subset=latin,latin-ext" rel="stylesheet" type="text/css">
		<link href="http://fonts.googleapis.com/css?family=PT+Serif" rel="stylesheet" type="text/css">
		<link href="{$GLOBALS.site_url}/fonts/font-awesome/css/font-awesome.css" rel="stylesheet">
		<link href="{$GLOBALS.site_url}/fonts/fontello/css/fontello.css" rel="stylesheet">
		<link href="{$GLOBALS.site_url}/plugins/rs-plugin/css/settings.css" media="screen" rel="stylesheet">
		<link href="{$GLOBALS.site_url}/plugins/rs-plugin/css/extralayers.css" media="screen" rel="stylesheet">
		<link href="{$GLOBALS.site_url}/plugins/magnific-popup/magnific-popup.css" rel="stylesheet">
		<link href="{$GLOBALS.site_url}/css/animations.css" rel="stylesheet">
		<link href="{$GLOBALS.site_url}/plugins/owl-carousel/owl.carousel.css" rel="stylesheet">
		<link href="{$GLOBALS.site_url}/css/custom.css" rel="stylesheet">
		<link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/bootstrap/style.css"  />


		<script type="text/javascript" src="{$GLOBALS.site_url}/js/jquery-1.11.2.min.js"></script>
		<script type="text/javascript" src="{$GLOBALS.site_url}/bootstrap/js/bootstrap.js"></script>
		<script type="text/javascript" src="{$GLOBALS.site_url}/plugins/modernizr.js"></script>
		<script type="text/javascript" src="{$GLOBALS.site_url}/plugins/rs-plugin/js/jquery.themepunch.tools.min.js"></script>
		<script type="text/javascript" src="{$GLOBALS.site_url}/plugins/rs-plugin/js/jquery.themepunch.revolution.min.js"></script>
		<script type="text/javascript" src="{$GLOBALS.site_url}/plugins/isotope/isotope.pkgd.min.js"></script>
		<script type="text/javascript" src="{$GLOBALS.site_url}/plugins/owl-carousel/owl.carousel.js"></script>
		<script type="text/javascript" src="{$GLOBALS.site_url}/plugins/magnific-popup/jquery.magnific-popup.min.js"></script>
		<script type="text/javascript" src="{$GLOBALS.site_url}/plugins/jquery.appear.js"></script>
		<script type="text/javascript" src="{$GLOBALS.site_url}/plugins/jquery.countTo.js"></script>
		<script src="{$GLOBALS.site_url}/plugins/jquery.parallax-1.1.3.js"></script>
		<script src="{$GLOBALS.site_url}/plugins/jquery.validate.js"></script>
		<script type="text/javascript" src="{$GLOBALS.site_url}/js/template.js"></script>
		<script type="text/javascript" src="{$GLOBALS.site_url}/js/custom.js"></script>


	<script language="JavaScript" type="text/javascript" src="{common_js}/main.js"></script>
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.js"></script>
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery-ui.js"></script>
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.form.js"></script>
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.validate.min.js"></script>
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.autocomplete.pack.js"></script>
	<script language="JavaScript" type="text/javascript" src="{common_js}/autoupload_functions.js"></script>
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.highlight.js"></script>
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/imagesize.js"></script>
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/jquery.bgiframe.js"></script>
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/multilist/jquery.multiselect.min.js"></script>
	<script language="JavaScript" type="text/javascript" src="{common_js}/multilist_functions.js"></script>
	<script language="JavaScript" type="text/javascript" src="{common_js}/jquery.poshytip.min.js"></script>
	<script language="JavaScript" type="text/javascript" src="{common_js}/floatnumbers_functions.js"></script>
	<!--[if IE 8]>
		<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/pie-ie.js"></script>
		<script language="JavaScript" type="text/javascript">
			$(function() {
				if (window.PIE) {
					$('input, .products, .productLinks, a.button, a.standart-button, #reports-navigation-in, #reports-navigation-in-border').each(function() {
						PIE.attach(this);
					});
				}
			});
		</script>
	<![endif]-->
	[[$HEAD]]

	{if isset( $GLOBALS.available_datepicker_localizations[$GLOBALS.current_language] )}
		<script type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/datepicker/i18n/jquery.ui.datepicker-{$GLOBALS.current_language}.js" ></script>
	{/if}

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

		$(".editTemplateMenu").on('click', function() {
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

	// Set global javascript value for page
	window.SJB_GlobalSiteUrl = '{/literal}{$GLOBALS.site_url}{literal}';
	window.SJB_UserSiteUrl   = '{/literal}{$GLOBALS.user_site_url}{literal}';

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
		{* load scripts for used indeed or simplyhired *}
		{if $GLOBALS.user_page_uri == '/search-results-jobs/'}
			{if $GLOBALS.plugins.IndeedPlugin.active == 1}
				<script type="text/javascript" src="https://gdc.indeed.com/ads/apiresults.js"></script>
			{/if}
			{if $GLOBALS.plugins.SimplyHiredPlugin.active == 1}
				<script type="text/javascript" src="https://api.simplyhired.com/c/jobs-api/js/xml-v2.js"></script>
			{/if}
		{/if}
	</head>
<body>
	{module name="users" function="cookie_preferences"}
	<div id="loading"></div>
	<div id="messageBox"></div>
	{include file="../menu/header.tpl"}
	<div class="indexDiv">
		{module name="breadcrumbs" function="show_breadcrumbs"}
		{module name='flash_messages' function='display'}
		{$MAIN_CONTENT}
		<div class="clearfix"></div>
	</div>
	{if $GLOBALS.plugins.ShareThisPlugin.active == 1 && $GLOBALS.settings.display_for_all_pages == 1}
		{if $GLOBALS.user_page_uri != '/news/' && $GLOBALS.user_page_uri != '/display-job/' && $GLOBALS.user_page_uri != '/display-resume/'}
			<div id="shareThis">{$GLOBALS.settings.header_code}{$GLOBALS.settings.code}</div>
		{/if}
	{/if}
	<div id="grayBgBanner">{module name="banners" function="show_banners" group="Bottom Banners"}</div>
	{include file="../menu/footer.tpl"}
	{module name="miscellaneous" function="profiler"}
	{if $highlight_templates}
		<div id="highlighterBlock"></div>
	{/if}
</body>
</html>