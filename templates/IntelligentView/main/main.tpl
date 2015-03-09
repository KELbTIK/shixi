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
	  <link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/bootstrap/css/bootstrap.css"  />
	  <link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/css/animate.css"  />
	  <link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/css/skins/red.css"  />
	  <link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/css/normalize.css"  />




	  <link rel="Stylesheet" type="text/css" href="{$GLOBALS.site_url}/system/ext/jquery/css/jquery.multiselect.css" />
	  <link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/system/ext/jquery/css/jquery-ui.css"  />


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




{if $GLOBALS.current_language_data.rightToLeft}<link rel="StyleSheet" type="text/css" href="{image src="designRight.css"}" />{/if}
{if $GLOBALS.current_language_data.id == 'de'}<link rel="StyleSheet" type="text/css" href="{image src="design-de.css"}" />{/if}
{if $GLOBALS.current_language_data.id == 'pl'}<link rel="StyleSheet" type="text/css" href="{image src="desing-pl.css"}" />{/if}
{if $GLOBALS.current_language_data.id == 'ro'}<link rel="StyleSheet" type="text/css" href="{image src="design-ro.css"}" />{/if}
{if $GLOBALS.current_language_data.id == 'fr'}<link rel="StyleSheet" type="text/css" href="{image src="design-fr.css"}" />{/if}
{if $GLOBALS.current_language_data.id == 'pt'}<link rel="StyleSheet" type="text/css" href="{image src="design-pt.css"}" />{/if}
{if $GLOBALS.current_language_data.id == 'sr'}<link rel="StyleSheet" type="text/css" href="{image src="design-sr.css"}" />{/if}
<link rel="alternate" type="application/rss+xml" title="RSS2.0" href="{$GLOBALS.site_url}/rss/" />

	  <link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/bootstrap/style.css"  />

<script type="text/javascript" src="{$GLOBALS.site_url}/js/jquery-1.11.2.min.js"></script>
{*<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.autocomplete.pack.js"></script>*}

{*<script language="JavaScript" type="text/javascript" src="{common_js}/main.js"></script>*}
<script type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery-ui.js"></script>

<script  type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.form.js"></script>
<script  type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/multilist/jquery.multiselect.min.js"></script>
<script  type="text/javascript" src="{common_js}/multilist_functions.js"></script>
<script  type="text/javascript" src="{common_js}/autoupload_functions.js"></script>
<script type="text/javascript" src="{common_js}/jquery.poshytip.min.js"></script>





	  <script type="text/javascript" src="{$GLOBALS.site_url}/bootstrap/js/bootstrap.js"></script>

	  <!-- Modernizr javascript -->
	  <script type="text/javascript" src="{$GLOBALS.site_url}/plugins/modernizr.js"></script>

	  <!-- jQuery REVOLUTION Slider  -->
	  <script type="text/javascript" src="{$GLOBALS.site_url}/plugins/rs-plugin/js/jquery.themepunch.tools.min.js"></script>
	  <script type="text/javascript" src="{$GLOBALS.site_url}/plugins/rs-plugin/js/jquery.themepunch.revolution.min.js"></script>

	  <!-- Isotope javascript -->
	  <script type="text/javascript" src="{$GLOBALS.site_url}/plugins/isotope/isotope.pkgd.min.js"></script>

	  <!-- Owl carousel javascript -->
	  <script type="text/javascript" src="{$GLOBALS.site_url}/plugins/owl-carousel/owl.carousel.js"></script>

	  <!-- Magnific Popup javascript -->
	  <script type="text/javascript" src="{$GLOBALS.site_url}/plugins/magnific-popup/jquery.magnific-popup.min.js"></script>

	  <!-- Appear javascript -->
	  <script type="text/javascript" src="{$GLOBALS.site_url}/plugins/jquery.appear.js"></script>

	  <!-- Count To javascript -->
	  <script type="text/javascript" src="{$GLOBALS.site_url}/plugins/jquery.countTo.js"></script>

	  <!-- Parallax javascript -->
	  <script src="{$GLOBALS.site_url}/plugins/jquery.parallax-1.1.3.js"></script>

	  <!-- Contact form -->
	  <script src="{$GLOBALS.site_url}/plugins/jquery.validate.js"></script>

	  <!-- Initialization of Plugins -->
	  <script type="text/javascript" src="{$GLOBALS.site_url}/js/template.js"></script>

	  <!-- Custom Scripts -->
	  <script type="text/javascript" src="{$GLOBALS.site_url}/js/custom.js"></script>

	  <!--[if IE 8]>
	<script type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/pie-ie.js"></script>
	<script type="text/javascript">
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

	$("a.editTemplateMenu").on('click', function() {
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


		<div class="leftColumn">
			<div class="container">
				<div class="col-md-6">

					{if !$GLOBALS.current_user.logged_in}

							<h2 class="title" style="margin-top:20px;">Login</h2>
							{module name="users" function="login" template="login.tpl" internal="true"}

					{/if}
				</div>



				<div class="col-md-6">
					<h2 class="Companies">[[Featured Companies]]</h2>
					{module name="users" function="featured_profiles" items_count="4"}
				</div>


				<div class="col-md-6">

					{if $GLOBALS.settings.show_polls_on_main_page}

						{module name="polls" function="polls"}
					{/if}
				</div>

				<div class="col-md-6">

					{module name="miscellaneous" function="mailchimp"}
				</div>
			</div>
		</div>

		<div class="mainColumn container">
			{$MAIN_CONTENT}

			<div class="JobSeekerBlock col-md-6" style="padding-left:0 !important;">
				<div class="plan stripped" style="margin-bottom:0 !important;">
					<div class="header">
						<h3>[[Job Seekers]]</h3>
					</div>
				</div>

				<div class="JobSeekerBlockBg" style="width:100%;">
					<p><a href="{$GLOBALS.site_url}/registration/?user_group_id=JobSeeker">[[Register]]</a></p>
					<p><a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Resume">[[Post resumes]]</a></p>
					<p><a href="{$GLOBALS.site_url}/find-jobs/">[[Find jobs]]</a></p>
					<p><a href="{$GLOBALS.site_url}/job-alerts/?action=new">[[Get Jobs by Email]]</a></p>
					<br/>
				</div>
			</div>

			<div class="EmployerBlock col-md-6" style="padding-right:0 !important;">
				<div class="plan stripped" style="margin-bottom:0 !important;">
					<div class="header">
						<h3>[[Employers]]</h3>
					</div>
				</div>
				<div class="EmployerBlockBg" style="width:100%;">
					<p><a href="{$GLOBALS.site_url}/registration/?user_group_id=Employer">[[Register]]</a></p>
					<p><a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Job">[[Post jobs]]</a></p>
					<p><a href="{$GLOBALS.site_url}/search-resumes/">[[Search resumes]]</a></p>
					<p><a href="{$GLOBALS.site_url}/resume-alerts/?action=new">[[Get Resumes by Email]]</a></p>
					<br/>
				</div>
			</div>
			<div class="clr"><br/></div>
			<div class="plan stripped" style="margin-bottom:0 !important;">
				<div class="header">
					<h3>[[Featured Jobs]]</h3>
				</div>
			</div>
			<div class="featuredJobs" style="width: 100%;">{module name="classifieds" function="featured_listings" items_count="4" listing_type="Job"}</div>
			<a href="{$GLOBALS.site_url}/listing-feeds/?feedId=10" id="mainRss">RSS</a>
			<div class="clr"><br/></div>
			<div class="plan stripped" style="margin-bottom:0 !important;">
				<div class="header">
					<h3>[[Latest Jobs]]</h3>
				</div>
			</div>
			<div class="latestJobs"  style="width: 100%;">{module name="classifieds" function="latest_listings" items_count="4" listing_type="Job"}</div>

			{if isset($GLOBALS.plugins.WordPressBridgePlugin) && $GLOBALS.plugins.WordPressBridgePlugin.active && $GLOBALS.settings.display_blog_on_homepage}
				<div class="plan stripped" style="margin-bottom:0 !important;">
					<div class="header">
						<h3>[[Blog Posts]]</h3>
					</div>
				</div>
			<div class="featuredJobs"  style="width: 100%;">
				<br/>{module name="miscellaneous" function="blog_page"}<br/>
			</div>
			{/if}

		</div>
		<div class="rightColumn container">
			{module name="banners" function="show_banners" group="Side Banners"}
			<br>
			{if $GLOBALS.settings.show_news_on_main_page}
				<div class="plan stripped Category" style="margin-bottom:0 !important;">
					<div class="header">
						<h3>[[News]]</h3>
					</div>
				</div>
				{module name="news" function="show_news"}
			{/if}
			<div class="clr"><br/></div>
			<div class="col-md-6">
				<div class="plan stripped Category" style="margin-bottom:0 !important;">
					<div class="header">
						<h3>[[Jobs by Category]]</h3>
					</div>
				</div>
				{module name="classifieds" function="browse" browseUrl="/browse-by-category/" browse_template="browse_by_category.tpl"}
				<div class="clr"><br /></div>
			</div>
			<div class="col-md-6">
				<div class="plan stripped City" style="margin-bottom:0 !important;">
					<div class="header">
						<h3>[[Jobs by City]]</h3>
					</div>
				</div>
				{module name="classifieds" function="browse" browseUrl="/browse-by-city/" browse_template="browse_by_city.tpl"}
			</div>
		</div>

		<div class="clr"><br/></div>
		<div class="container">
		{module name="banners" function="show_banners" group="Bottom Banners"}
		</div>
		{include file="../menu/footer.tpl"}
		{module name="miscellaneous" function="profiler"}
		{if $highlight_templates}
			<div id="highlighterBlock"></div>
		{/if}
</body>
</html>