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
{*<link rel="StyleSheet" type="text/css" href="{image src="design.css"}" />*}
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
	  <link href="{$GLOBALS.site_url}/css/style-akord.css" rel="stylesheet">




{*{if $GLOBALS.current_language_data.rightToLeft}<link rel="StyleSheet" type="text/css" href="{image src="designRight.css"}" />{/if}*}
{*{if $GLOBALS.current_language_data.id == 'de'}<link rel="StyleSheet" type="text/css" href="{image src="design-de.css"}" />{/if}*}
{*{if $GLOBALS.current_language_data.id == 'pl'}<link rel="StyleSheet" type="text/css" href="{image src="desing-pl.css"}" />{/if}*}
{*{if $GLOBALS.current_language_data.id == 'ro'}<link rel="StyleSheet" type="text/css" href="{image src="design-ro.css"}" />{/if}*}
{*{if $GLOBALS.current_language_data.id == 'fr'}<link rel="StyleSheet" type="text/css" href="{image src="design-fr.css"}" />{/if}*}
{*{if $GLOBALS.current_language_data.id == 'pt'}<link rel="StyleSheet" type="text/css" href="{image src="design-pt.css"}" />{/if}*}
{*{if $GLOBALS.current_language_data.id == 'sr'}<link rel="StyleSheet" type="text/css" href="{image src="design-sr.css"}" />{/if}*}
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
        <div class="mainColumn container">
            <div class="page-top">


            {$MAIN_CONTENT}

            <div class="">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="active"><a href="#vtab1" role="tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-magic pr-10"></i>[[Job Seekers]]</a></li>
                    <li class=""><a href="#vtab2" role="tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-life-saver pr-10"></i>[[Employers]]</a></li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane fade active in" id="vtab1">
                        <ul class="list-icons">
                            <li><i class="icon-check pr-10"></i> <a href="{$GLOBALS.site_url}/registration/?user_group_id=JobSeeker">[[Register]]</a></li>
                            <li><i class="icon-check pr-10"></i> <a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Resume">[[Post resumes]]</a></li>
                            <li><i class="icon-check pr-10"></i> <a href="{$GLOBALS.site_url}/find-jobs/">[[Find jobs]]</a></li>
                            <li><i class="icon-check pr-10"></i> <a href="{$GLOBALS.site_url}/job-alerts/?action=new">[[Get Jobs by Email]]</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="vtab2">
                        <ul class="list-icons">
                            <li><i class="icon-check pr-10"></i> <a href="{$GLOBALS.site_url}/registration/?user_group_id=Employer">[[Register]]</a></li>
                            <li><i class="icon-check pr-10"></i> <a href="{$GLOBALS.site_url}/add-listing/?listing_type_id=Job">[[Post jobs]]</a></li>
                            <li><i class="icon-check pr-10"></i> <a href="{$GLOBALS.site_url}/search-resumes/">[[Search resumes]]</a></li>
                            <li><i class="icon-check pr-10"></i> <a href="{$GLOBALS.site_url}/resume-alerts/?action=new">[[Get Resumes by Email]]</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="plan stripped" style="margin-bottom:0 !important;">
                <div class="header">
                    <h3>[[Featured Jobs]]</h3>
                </div>
            </div>
            <div class="featuredJobs" style="width: 100%;">{module name="classifieds" function="featured_listings" items_count="4" listing_type="Job"}</div>
            <a href="{$GLOBALS.site_url}/listing-feeds/?feedId=10" id="mainRss" class="pull-right"><i class="fa fa-rss-square"></i> RSS</a>
            <div class="clearfix"></div>
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
                    {module name="miscellaneous" function="blog_page"}
                </div>
            {/if}
                <div class="rightColumn">
                    {if $GLOBALS.settings.show_news_on_main_page}
                        <div class="plan stripped Category" style="margin-bottom:0 !important;">
                            <div class="header">
                                <h3>[[News]]</h3>
                            </div>
                        </div>
                        {module name="news" function="show_news"}
                    {/if}
                    <div class="clearfix"></div>
                    <!-- accordion start -->
                    <div class="panel-group panel-dark" id="accordion-2">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion-2" href="#collapseOne-2">
                                        <i class="fa fa-leaf"></i>[[Jobs by Category]]
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseOne-2" class="panel-collapse collapse in">
                                <div class="panel-body">
                                    {module name="classifieds" function="browse" browseUrl="/browse-by-category/" browse_template="browse_by_category.tpl"}
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion-2" href="#collapseTwo-2" class="collapsed">
                                        <i class="fa fa-home"></i>[[Jobs by City]]
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseTwo-2" class="panel-collapse collapse">
                                <div class="panel-body">
                                    {module name="classifieds" function="browse" browseUrl="/browse-by-city/" browse_template="browse_by_city.tpl"}
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- accordion end -->
                </div>
                <div class="clearfix"></div>
                <div class="leftColumn">
                    <h1 class="text-center">[[Featured Companies]]</h1>
                    <div class="separator"></div>
                    {module name="users" function="featured_profiles" items_count="4"}


                    <div class="col-md-6">

                        {if $GLOBALS.settings.show_polls_on_main_page}

                            {module name="polls" function="polls"}
                        {/if}
                    </div>

                    <div class="col-md-6">

                        {module name="miscellaneous" function="mailchimp"}
                    </div>
                </div>
                <div class="clearfix"></div>

            </div>
        </div>




		<div class="container">
		{*{module name="banners" function="show_banners" group="Bottom Banners"}*}
		</div>
		{include file="../menu/footer.tpl"}
		{module name="miscellaneous" function="profiler"}
		{if $highlight_templates}
			<div id="highlighterBlock"></div>
		{/if}
</body>
</html>