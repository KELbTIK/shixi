<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
  <head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="keywords" content="[[$KEYWORDS]]" />
<meta name="description" content="[[$DESCRIPTION]]" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>{$GLOBALS.settings.site_title}{if $TITLE ne ""}: [[$TITLE]]{/if}</title>
<link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/templates/_system/main/images/css/form.css" />
{if $GLOBALS.current_language_data.rightToLeft}<link rel="StyleSheet" type="text/css" href="{image src="designRight.css"}" />{/if}
<link rel="alternate" type="application/rss+xml" title="RSS2.0" href="{$GLOBALS.site_url}/rss/" />
<link rel="stylesheet" href="{$GLOBALS.site_url}/system/lib/rating/style.css" type="text/css" />
<link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/system/ext/jquery/css/jquery-ui.css"  />

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
	  <link href="{$GLOBALS.site_url}/css/style-akord.css" rel="stylesheet">

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

<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.js"></script>
<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery-ui.js"></script>
<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.validate.min.js"></script>
<script language="JavaScript" type="text/javascript" src="{common_js}/autoupload_functions.js"></script>
<script language="JavaScript" type="text/javascript" src="{common_js}/jquery.poshytip.min.js"></script>
[[$HEAD]]
{literal}
	<script language="javascript" type="text/javascript">
		function popUpWindow(url, widthWin, title, parentReload, userLoggedIn){
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
{if $highlight_templates}
<!-- AJAX EDIT TEMPLATE SECTION -->
<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.form.js"></script>
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
{module name="users" function="cookie_preferences"}
<div id="loading"></div>
<div id="messageBox"></div>
{include file="../menu/header.tpl"}
<div id="siteMap">
	<h1>{$TITLE}</h1>
	{module name='flash_messages' function='display'}
	{$MAIN_CONTENT}
</div>
{include file="../menu/footer.tpl"}
{module name="miscellaneous" function="profiler"}
</body>
</html>
{if $highlight_templates}
<div id="highlighterBlock" style="display:none;background-color: #ccc; opacity: 0.5;"></div>
{/if}