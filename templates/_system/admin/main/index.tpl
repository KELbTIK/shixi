<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>SmartJobBoard [[Admin Panel]] {if $TITLE ne ""} :: [[{$TITLE}]] {/if}</title>

	<link rel="StyleSheet" type="text/css" href="{image src="design.css"}" />
	{if $GLOBALS.current_language_data.rightToLeft}<link rel="StyleSheet" type="text/css" href="{image src="designRight.css"}" />{/if}
    <link type="text/css" href="{$GLOBALS.user_site_url}/system/ext/jquery/themes/green/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="{image src="jquery-ui-1.8.custom.css"}" />
	<link type="text/css" href="{$GLOBALS.user_site_url}/system/ext/jquery/css/jquery-ui.css" rel="stylesheet" />
	<link rel="stylesheet" type="text/css" href="{$GLOBALS.user_site_url}/system/ext/jquery/css/jquery.multiselect.css" />
	<script language="JavaScript" type="text/javascript" src="{common_js}/main.js"></script>
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/jquery.js"></script>
    <script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/jquery-ui.js"></script>
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/jquery.form.js"></script>
	<script language="JavaScript" type="text/javascript" src="{common_js}/autoupload_functions.js"></script>
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/jquery.bgiframe.js"></script>
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/multilist/jquery.multiselect.min.js"></script>
	<script language="JavaScript" type="text/javascript" src="{common_js}/multilist_functions.js"></script>
	<script language="JavaScript" type="text/javascript" src="{common_js}/floatnumbers_functions.js"></script>
	{capture name="displayProgressBar"}<img style="vertical-align: middle;" src="{$GLOBALS.user_site_url}/system/ext/jquery/progbar.gif" alt="[[Please wait ...]]" /> [[Please wait ...]]{/capture}
    <script language="JavaScript" type="text/javascript">

		// Set global javascript value for page
		window.SJB_GlobalSiteUrl = '{$GLOBALS.site_url}';
		window.SJB_AdminSiteUrl  = '{$GLOBALS.admin_site_url}';
		window.SJB_UserSiteUrl   = '{$GLOBALS.user_site_url}';

		currentSjbVersion = {
			major: "{$GLOBALS.version.major}",
			minor: "{$GLOBALS.version.minor}",
			build: "{$GLOBALS.version.build}"
		};

		$(document).ready(function() {
			// create special service block on page, if not exists
			var n = $("#updateInfoBlockDashboard").length;
			if (n == 0) {
				$("body").append("<div id=\"updateInfoBlockDashboard\" style=\"display:none;\"></div>");
			}

			// check for availabled SJB updates
			$.getJSON(window.SJB_AdminSiteUrl + "/system/miscellaneous/update_check/", function(data) {
				$('#updateProgress').hide();
				if (data.updateStatus == 'available' && !data.closed_by_user) {
					$("#updateInfoBlock").show("slide", { direction: "up"}, 500);
				}

				switch (data.updateStatus) {
					case 'available':
						var updateVersion = data.availableVersion.major + "." + data.availableVersion.minor + " build " + data.availableVersion.build;
						$("#updateInfoBlockDashboard").html(updateVersion);
						$("#updateButtonBlockDashboard").html(
							'<form style="text-align: center;" name="update_to_version_form" method="post" action="{$GLOBALS.site_url}/update-to-new-version/">' +
								'<input type="submit" name="go_to_update_page" value="[[Update to version]] ' + updateVersion + '" />' +
							'</form>'
						);
						break;

					case 'deprecated':
						$('#updateDeprecated').show();
						break;

					case 'none':
						$('#updateNone').show();
						break;

					default:
						$('#updateError').show();
						break;
				}
			});

			$("#closeUpdateInfoBlock").click(function() {
				$("#updateInfoBlock").hide("slide", { direction: "up"}, 500);
				$.post(window.SJB_AdminSiteUrl + "/system/miscellaneous/update_check/", { action: "mark_as_closed"});
			});
		});

		$.extend($.ui.dialog.prototype.options, {
			modal: true
		});

		function popUpWindow(url, widthWin, heightWin, title, iframe, callbackFunction) {
			$("#messageBox").dialog('destroy').html('{$smarty.capture.displayProgressBar|escape:'javascript'}');
			$("#messageBox").dialog({
				width: widthWin,
				height: heightWin,
				modal: true,
				title: title,
				close: function(event, ui) {
					if (callbackFunction) {
						callbackFunction();
					}
				}
			}).dialog('open');
			if (iframe) {
				$("#messageBox").html('<iframe border="0" runat="server" width="100%" height="100%" frameborder="0" src="'+url+'"></iframe>');
			} else {
				$.get(url, function(data) {
					$("#messageBox").html(data);
				});
			}
			return false;
		}

		function popUpMessageWindow(widthWin, heightWin, title, message) {
			$("#messageBox").dialog("destroy" ).html(message);
			$("#messageBox").dialog({
				width: widthWin,
				height: heightWin,
				title: title
			}).dialog("open");
			return false;
		}
	</script>
</head><body>
	{capture name=url_for_wiki}
	{* Dashboard *}
		{if $url === '/system/dashboard/view/' || $url === '/'}
			http://wiki.smartjobboard.com/display/sjb42/Dashboard
		{elseif $url === '/upload-logo'}
			http://wiki.smartjobboard.com/display/sjb42/Upload+Your+Logo
		{elseif $url === '/edit-css/' && $params}
			{if $params === 'action=edit&amp;file=../templates/IntelligentView/main/images/design.css'}
				http://wiki.smartjobboard.com/display/sjb42/Edit+CSS+File
			{elseif $params === 'action=edit&amp;file=../templates/_system/main/images/css/form.css'}
				http://wiki.smartjobboard.com/display/sjb42/Edit+Forms+CSS+file
			{/if}
	{* Listing Configuration *}
		{elseif $url === '/listing-fields/'}
			http://wiki.smartjobboard.com/display/sjb42/Common+Fields
		{elseif $url === '/add-listing-field/'}
			http://wiki.smartjobboard.com/display/sjb42/Common+Fields#CommonFields-AddingaNewListingField
		{elseif strpos($url, '/edit-listing-field/') !== false}
			http://wiki.smartjobboard.com/display/sjb42/Common+Fields
		{elseif $url === '/listing-types/'}
			http://wiki.smartjobboard.com/display/sjb42/Listing+Types
		{elseif $url === '/add-listing-type/'}
			http://wiki.smartjobboard.com/display/sjb42/Listing+Types#ListingTypes-AddinaNewListingType
		{elseif $url === '/edit-listing-type/'}
			http://wiki.smartjobboard.com/display/sjb42/Listing+Types#ListingTypes-EditingaListingType
		{elseif $url === '/add-listing-type-field/'}
			http://wiki.smartjobboard.com/display/sjb42/Listing+Types#ListingTypes-AddingaNewListingField
		{elseif $url === '/edit-listing-type-field/'}	
			http://wiki.smartjobboard.com/display/sjb42/Listing+Types#ListingTypes-EditingListingFields
		{elseif strpos($url, '/posting-pages/job/new') !== false || strpos($url, '/posting-pages/resume/new') !== false}
			http://wiki.smartjobboard.com/display/sjb42/Posting+Pages#PostingPages-AddingaNewPostingPage
		{elseif strpos($url, '/posting-pages/') !== false}
			http://wiki.smartjobboard.com/display/sjb42/Posting+Pages
	{* Listing Management *}
		{elseif $url == '/manage-resumes/' || $GLOBALS.wikiExtraParam == 'Resume'}
			http://wiki.smartjobboard.com/display/sjb42/Manage+Resumes
		{elseif $url == '/manage-jobs/' || $GLOBALS.wikiExtraParam == 'Job'}
			http://wiki.smartjobboard.com/display/sjb42/Manage+Jobs
		{elseif $url == '/add-listing/' && (strpos($params, 'listing_type_id=resume') !== false || strpos($params, 'listing_type_id=Resume') !== false)}
			http://wiki.smartjobboard.com/display/sjb42/Adding+New+Resumes
		{elseif $url == '/add-listing/' && (strpos($params, 'listing_type_id=job') !== false || strpos($params, 'listing_type_id=Job') !== false)}
			http://wiki.smartjobboard.com/display/sjb42/Adding+New+Jobs
		{elseif $url === '/import_listings/'}
			http://wiki.smartjobboard.com/display/sjb42/Import+Listings
		{elseif $url === '/export-listings/'}
			http://wiki.smartjobboard.com/display/sjb42/Export+Listings
		{elseif $url === '/listing-feeds/'}
			http://wiki.smartjobboard.com/display/sjb42/XML+Feeds
		{elseif $url === '/show-import/'}
			http://wiki.smartjobboard.com/display/sjb42/XML+Import
		{elseif $url === '/edit-import/'}
			http://wiki.smartjobboard.com/display/sjb42/XML+Import
		{elseif $url === '/flagged-listings/'}
			http://wiki.smartjobboard.com/display/sjb42/Flagged+listings
		{elseif $url === '/import-listings/'}
			http://wiki.smartjobboard.com/display/sjb42/Import+Listings
		{elseif $url === '/add-import/'}
			http://wiki.smartjobboard.com/display/sjb42/XML+Import
	{* Users *}
		{elseif $url === '/user-groups/'}
			http://wiki.smartjobboard.com/display/sjb42/User+Groups
		{elseif $url === '/add-user-group/'}
			http://wiki.smartjobboard.com/display/sjb42/User+Groups#UserGroups-AddingaNewUserGroup
		{elseif $url === '/edit-user-group/'}
			http://wiki.smartjobboard.com/display/sjb42/User+Groups#UserGroups-EditingaUserGroup
		{elseif $url === '/edit-user-profile/'}
			http://wiki.smartjobboard.com/display/sjb42/User+Groups
		{elseif $url === '/system/users/acl/' && $params}
			http://wiki.smartjobboard.com/display/sjb42/User+Groups
		{elseif $url === '/manage-users/jobseeker/' || $url === '/add-user/jobseeker/' || $GLOBALS.wikiExtraParam == 'JobSeeker'}
			http://wiki.smartjobboard.com/display/sjb42/Manage+Job+Seekers
		{elseif $url === '/manage-users/employers/' || $url === '/add-user/employer/' || $GLOBALS.wikiExtraParam == 'Employer'}
			http://wiki.smartjobboard.com/display/sjb42/Manage+Employers
		{elseif $url === '/mailing/'}
			http://wiki.smartjobboard.com/display/sjb42/Mass+Mailing
		{elseif $url === '/banned-ips/'}
			http://wiki.smartjobboard.com/display/sjb42/Banned+IPs
		{elseif $url === '/add-user-profile-field/'}
			http://wiki.smartjobboard.com/display/sjb42/User+Groups#UserGroups-EditUserGroupProfileFields
		{elseif $url === '/export-users/'}
			http://wiki.smartjobboard.com/display/sjb42/Export+Users
		{elseif $url === '/guest-alerts/'}
			http://wiki.smartjobboard.com/display/sjb42/Guest+Email+Alerts
	{* Layout and Content *}
		{elseif $url === '/edit-templates/'}
			http://wiki.smartjobboard.com/display/sjb42/System+Templates
		{elseif $url === '/edit_themes/'}
			http://wiki.smartjobboard.com/display/sjb42/Themes
		{elseif $url === '/user-pages/'}
			{if strpos($params, 'action=new_page') !== false }
				http://wiki.smartjobboard.com/display/sjb42/Site+Pages#SitePages-AddingaNewUserPage
			{elseif strpos($params, 'action=edit_page') !== false }
				http://wiki.smartjobboard.com/display/sjb42/Site+Pages#SitePages-EditingUserPages
			{else}
				http://wiki.smartjobboard.com/display/sjb42/Site+Pages
			{/if}
		{elseif $url === '/stat-pages/'}
			{if strpos($params, 'action=edit') !== false}
				http://wiki.smartjobboard.com/display/sjb42/Static+Content#StaticContent-EditingStaticContent
			{else}
				http://wiki.smartjobboard.com/display/sjb42/Static+Content
			{/if}
		{elseif $url === '/manage-banner-groups/'}
			http://wiki.smartjobboard.com/display/sjb42/Banners
		{elseif $url === '/add-banner-group/'}
			http://wiki.smartjobboard.com/display/sjb42/Banners#Banners-AddingaNewBannerGroup
		{elseif $url === '/edit-banner-group/'}
			http://wiki.smartjobboard.com/display/sjb42/Banners#Banners-EditingBannersGroup
		{elseif $url === '/edit-banner/'}
			http://wiki.smartjobboard.com/display/sjb42/Banners#Banners-EditingaBanner
		{elseif $url === '/form-builders/'}
			http://wiki.smartjobboard.com/display/sjb42/Form+Builder
		{elseif $url === '/edit-themes/'}
			http://wiki.smartjobboard.com/display/sjb42/Themes
		{elseif $url === '/add-banner/'}
			http://wiki.smartjobboard.com/display/sjb42/Banners#Banners-AddingaNewBannertoaGroup
		{elseif $url === '/news-categories/' && !$params}
			http://wiki.smartjobboard.com/display/sjb42/News
		{elseif $url === '/news-categories/'}
			{if strpos($params, 'category_sid=1') !== false}
				http://wiki.smartjobboard.com/display/sjb42/News#News-Archive
			{elseif strpos($params, 'action=edit') !== false}
				http://wiki.smartjobboard.com/display/sjb42/News#News-NewsCategories
			{/if}
		{elseif $url === '/manage-news/'}
			{if strpos($params, 'action=add') !== false}
				http://wiki.smartjobboard.com/display/sjb42/News#News-AddingaNews
			{elseif strpos($params, 'action=edit') !== false}
				http://wiki.smartjobboard.com/display/sjb42/News#News-EditingNews
			{/if}
		{elseif $url === '/manage-polls/'}
			{if strpos($params, 'action=new') !== false}
				http://wiki.smartjobboard.com/display/sjb42/Polls#Polls-AddingaNewPoll
			{elseif strpos($params, 'action=edit') !== false}
				http://wiki.smartjobboard.com/display/sjb42/Polls#Polls-EditingPolls
			{elseif strpos($params, 'edit_answer') !== false}
				http://wiki.smartjobboard.com/display/sjb42/Polls#Polls-EditAnswers
			{else}
				http://wiki.smartjobboard.com/display/sjb42/Polls
			{/if}
	{* Billing *}
		{elseif $url === '/system/payment/gateways/'}
			http://wiki.smartjobboard.com/display/sjb42/Payment+Gateways
		{elseif $url === '/configure-gateway/' && $params}
			{if $params === 'gateway=2checkout'}
				http://wiki.smartjobboard.com/display/sjb42/2checkout
			{elseif $params === 'gateway=authnet_sim'}
				http://wiki.smartjobboard.com/display/sjb42/Authorize.Net+SIM
			{elseif $params === 'gateway=cash_gateway'}
				http://wiki.smartjobboard.com/display/sjb42/Cash+Payment
			{elseif $params === 'gateway=paypal_standard'}
				http://wiki.smartjobboard.com/display/sjb42/Paypal+Standard
			{elseif $params === 'gateway=wire_transfer'}
				http://wiki.smartjobboard.com/display/sjb42/Wire+Transfer
			{elseif $params === 'gateway=paypal_pro'}
				http://wiki.smartjobboard.com/display/sjb42/PayPal+Pro
			{/if}
		{elseif $url === '/payments/'}
			http://wiki.smartjobboard.com/display/sjb42/Transaction+History
		{elseif $url === '/payment-log/'}
			http://wiki.smartjobboard.com/display/sjb42/Payment+Log
		{elseif $url === '/manage-taxes/' || $url === '/add-tax/' || $url === '/edit-tax/'}
			http://wiki.smartjobboard.com/display/sjb42/Tax+Rules?moved=true
		{elseif $url === '/manage-invoices/' || $url === '/add-invoice/' || $url === '/edit-invoice/'}
			http://wiki.smartjobboard.com/display/sjb42/Invoices
	{* Reports *}
		{elseif $url === '/statistics/guest-alerts/'}
			http://wiki.smartjobboard.com/display/sjb42/Guest+Alerts
	{* System Configuration *}
		{elseif $url === '/adminpswd/'}
			http://wiki.smartjobboard.com/display/sjb42/Admin+Password
		{elseif $url === '/settings/'}
			http://wiki.smartjobboard.com/display/sjb42/System+Settings
		{elseif $url === '/alphabet-letters/'}
			{if strpos($params, 'action=edit') !== false}
				http://wiki.smartjobboard.com/pages/viewpage.action?pageId=327723#AlphabetLettersforSearchbyCompany-EditingAlphabet
			{elseif strpos($params, 'action=new') !== false}
				http://wiki.smartjobboard.com/pages/viewpage.action?pageId=327723#AlphabetLettersforSearchbyCompany-AddingaNewAlphabet
			{else}
				http://wiki.smartjobboard.com/pages/viewpage.action?pageId=327723
			{/if}
		{elseif $url == '/social-media/'}
			{if strpos($params, 'soc_network=facebook') !== false}
				http://wiki.smartjobboard.com/display/sjb42/Facebook#Facebook-Addingnewfeed
			{elseif strpos($params, 'soc_network=linkedin') !== false}
				http://wiki.smartjobboard.com/display/sjb42/Linkedin#Linkedin-Addingnewfeed
			{elseif strpos($params, 'soc_network=twitter') !== false}
				http://wiki.smartjobboard.com/display/sjb42/Twitter#Twitter-Addingnewfeed
			{else}
				http://wiki.smartjobboard.com/display/sjb42/Social+Media
			{/if}
		{elseif strpos($url, '/social-media/facebook') !== false}
			http://wiki.smartjobboard.com/display/sjb42/Facebook
		{elseif strpos($url, '/social-media/linkedin') !== false}
			http://wiki.smartjobboard.com/display/sjb42/Linkedin
		{elseif strpos($url, '/social-media/twitter') !== false}
			http://wiki.smartjobboard.com/display/sjb42/Twitter
		{elseif strpos($url, '/social-media/googleplus') !== false}
			http://wiki.smartjobboard.com/display/sjb42/Google+Plus
		{elseif strpos($url, '/social-media/bitly') !== false}
			http://wiki.smartjobboard.com/display/sjb42/Bitly
		{elseif $url === '/geographic-data/'}
			http://wiki.smartjobboard.com/display/sjb42/ZipCode+Database
		{elseif $url === '/manage-breadcrumbs/'}
			http://wiki.smartjobboard.com/display/sjb42/Breadcrumbs+Config
		{elseif $url === '/filters/'}
			http://wiki.smartjobboard.com/display/sjb42/HTML+filters
		{elseif $url === '/currency-list/'}
			{if strpos($params, 'action=add') !== false}
				http://wiki.smartjobboard.com/display/sjb42/Manage+Currencies#ManageCurrencies-AddingaNewCurrency
			{elseif strpos($params, 'action=edit') !== false}
				http://wiki.smartjobboard.com/display/sjb42/Manage+Currencies#ManageCurrencies-EditingCurrencies
			{else}
				http://wiki.smartjobboard.com/display/sjb42/Manage+Currencies
			{/if}
		{elseif $url === '/task-scheduler-settings/'}
			http://wiki.smartjobboard.com/display/sjb42/Task+Scheduler
		{elseif $url === '/system/miscellaneous/plugins/' && !$params}
			http://wiki.smartjobboard.com/display/sjb42/Plugins
		{elseif $url === '/system/miscellaneous/plugins/' && $params}
			{if $params === 'action=settings&amp;plugin=PhpBBBridgePlugin'}
				http://wiki.smartjobboard.com/display/sjb42/PhpBB+forum+integration+plugin
			{elseif strpos($params, 'WordPressBridgePlugin') !== false}
				http://wiki.smartjobboard.com/display/sjb42/Wordpress+integration+plugin
			{elseif strpos($params, 'TwitterIntegrationPlugin') !== false}
				{if strpos($params, 'action=edit_feed') !== false}
					http://wiki.smartjobboard.com/display/sjb42/Twitter+Integration+Plugin
				{elseif strpos($params, 'action=add_feed') !== false}
					http://wiki.smartjobboard.com/display/sjb42/Twitter+Integration+Plugin#TwitterIntegrationPlugin-CreatingaNewFeed
				{/if}
			{elseif strpos($params, 'GooglePlusSocialPlugin') !== false}
				http://wiki.smartjobboard.com/display/sjb42/Google+Plus
			{elseif strpos($params, 'FacebookSocialPlugin') !== false}
				http://wiki.smartjobboard.com/display/sjb42/Facebook+Plugin
			{elseif strpos($params, 'LinkedinSocialPlugin') !== false}
				http://wiki.smartjobboard.com/display/sjb42/LinkedIn+Plugin
			{elseif strpos($params, 'ShareThisPlugin') !== false}
				http://wiki.smartjobboard.com/display/sjb42/ShareThis+Plugin
			{elseif strpos($params, 'ShareThisPlugin') !== false}
				http://wiki.smartjobboard.com/display/sjb42/ShareThis+Plugin
			{elseif strpos($params, 'CaptchaPlugin') !== false}
				http://wiki.smartjobboard.com/display/sjb42/CAPTCHA+Plugin
			{elseif strpos($params, 'BeyondPlugin') !== false}
				http://wiki.smartjobboard.com/display/sjb42/Beyond+Plugin
			{elseif strpos($params, 'IndeedPlugin') !== false}
				http://wiki.smartjobboard.com/display/sjb42/Indeed+Plugin
			{elseif strpos($params, 'SimplyHiredPlugin') !== false}
				http://wiki.smartjobboard.com/display/sjb42/SimplyHired+Plugin
			{elseif strpos($params, 'MailChimpPlugin') !== false}
				http://wiki.smartjobboard.com/display/sjb42/MailChimp+plug-in
			{elseif strpos($params, 'GoogleAnalyticsPlugin') !== false}
				http://wiki.smartjobboard.com/display/sjb42/Google+Analytics+Plugin
			{/if}
		{elseif $url === '/refine-search-settings/'}
			http://wiki.smartjobboard.com/display/sjb42/Refine+Search+Settings
		{elseif $url === '/backup/'}
			http://wiki.smartjobboard.com/display/sjb42/Backup-Restore
		{elseif $url === '/flag-listing-settings/'}
			http://wiki.smartjobboard.com/display/sjb42/Flag+Listing+Settings
		{elseif $url === '/manage-subadmins/'}
			http://wiki.smartjobboard.com/display/sjb42/Admin+Sub+Accounts
		{elseif $url === '/add-subadmin/'}
			http://wiki.smartjobboard.com/display/sjb42/Admin+Sub+Accounts#AdminSubAccounts-CreatingaNewSub-Account
		{elseif $url === '/edit-subadmin/'}
			http://wiki.smartjobboard.com/display/sjb42/Admin+Sub+Accounts
		{elseif $url === '/countries/' || $url === '/add-country/' || $url === '/edit-country/' || $url === '/import-countries/'}
			http://wiki.smartjobboard.com/display/sjb42/Countries 
		{elseif $url === '/states/' || $url === '/add-state/' || $url === '/edit-state/' || $url === '/import-states/'}
			http://wiki.smartjobboard.com/pages/viewpage.action?pageId=6193229
	{* Language Management *}
		{elseif $url === '/manage-languages/'}
			http://wiki.smartjobboard.com/display/sjb42/Manage+Languages
		{elseif $url === '/add-language/'}
			http://wiki.smartjobboard.com/display/sjb42/Manage+Languages#ManageLanguages-AddingaNewLanguage
		{elseif $url === '/edit-language/'}
			http://wiki.smartjobboard.com/display/sjb42/Manage+Languages#ManageLanguages-EditingLanguages
		{elseif $url === '/manage-phrases/'}
			http://wiki.smartjobboard.com/display/sjb42/Translate+Phrases
		{elseif $url === '/import-language/'}
			http://wiki.smartjobboard.com/display/sjb42/Import+Language
		{elseif $url === '/export-language/'}
			http://wiki.smartjobboard.com/display/sjb42/Export+Language
		{elseif $url === '/add-phrase/'}
			http://wiki.smartjobboard.com/display/sjb42/Translate+Phrases#TranslatePhrases-AddingaNewPhrase
	{* Products *}
		{elseif $url === '/products/'}
			http://wiki.smartjobboard.com/display/sjb42/Products
		{elseif $url === '/add-product/'}
			http://wiki.smartjobboard.com/display/sjb42/Products#Products-AddingaNewProduct
		{elseif $url === '/edit-product/' && $params}
			{if $params}
				http://wiki.smartjobboard.com/display/sjb42/Products#Products-EditingaProduct
			{/if}
		{elseif $url === '/promotions/'}
			http://wiki.smartjobboard.com/display/sjb42/Promotions
		{elseif $url === '/add-promotion-code/'}
			http://wiki.smartjobboard.com/display/sjb42/Promotions#Promotions-Addinganewpromotioncode
		{elseif strpos($url, '/promotions/log/') !== false}
			http://wiki.smartjobboard.com/display/sjb42/Promotions
		{elseif $url == '/statistics/promotions/'}
			http://wiki.smartjobboard.com/display/sjb42/Promotions+Usage
		{elseif strpos($url, '/edit-promotion-code/') !== false}
			http://wiki.smartjobboard.com/display/sjb42/Promotions
	{* Reports *}
		{elseif $url === '/general-statistics/'}
			http://wiki.smartjobboard.com/display/sjb42/General+Statistics
		{elseif $url === '/listings-statistics/'}
			http://wiki.smartjobboard.com/display/sjb42/Listings
		{elseif $url === '/applications-and-views/'}
			http://wiki.smartjobboard.com/display/sjb42/Applications+and+Views
		{elseif $url === '/sales/'}
			http://wiki.smartjobboard.com/display/sjb42/Sales
	{* email *}
		{elseif $url === '/email-log/'}
			http://wiki.smartjobboard.com/display/sjb42/Email+Log
		{elseif $url === '/edit-email-templates/'}
			http://wiki.smartjobboard.com/display/sjb42/Email+Templates
		{elseif $url === '/update-to-new-version/'}
			http://wiki.smartjobboard.com/display/sjb42/Updates
		{else}
			http://wiki.smartjobboard.com/display/sjb42/
		{/if}
	{/capture}
	<table border="0" cellpadding="0" cellspacing="0" width="100%" id="structure"  height="100%">
		<tr>
			<td id="left" valign="top" height="100%">
				<div id="leftHeader" style="text-align:right">
					<a href="{$GLOBALS.user_site_url}" id="logoLink"></a>
                    <span class="packageVersion">[[version]] {$GLOBALS.version.major}.{$GLOBALS.version.minor} [[build]] {$GLOBALS.version.build}</span>
				</div>
				<div class="clr"><br/></div>
				{if $GLOBALS.subAdminSID > 0}
					{module name="menu" function="show_subadmin_menu"}
				{/if}
				{module name="menu" function="show_left_menu"}
				<div class="clr"><br/></div>
			</td>
			<td valign="top" height="100%">
				<div class="manual">
					<a href="{$smarty.capture.url_for_wiki}" target="_blank">[[Help for this page]] <img src="{image}question.png" border="0" class="helpicon" /> </a>
					<form id="langSwitcherForm" method="get" action="">
						<select name="lang" onchange="location.href='{$GLOBALS.admin_site_url}{$url}?lang='+this.value+'&amp;{$params}'" style="margin-top:5px; width: 200px;">
						{foreach from=$GLOBALS.languages item=language}
							<option value="{$language.id}"{if $language.id == $GLOBALS.current_language} selected="selected"{/if}>{$language.caption}</option>
						{/foreach}
						</select>
					</form>
				</div>
				<div id="messageBox"></div>

				<div id="topGray">
					<div id="updateInfoBlock">
						<a href="{$GLOBALS.site_url}/update-to-new-version/">[[New update available]]</a>
						<span id="closeUpdateInfoBlock">X</span>
					</div>
					<div id="breadCrumbs">
						{if $GLOBALS.user_page_uri !== "/"}<a href="{$GLOBALS.site_url}/">[[Dashboard]]</a> &#187;{/if} [[{$ADMIN_BREADCRUMBS}]]
					</div>
					<div id="topRight">
						[[Welcome back]], {$smarty.session.username}<br/>
						<a href="{$GLOBALS.site_url}/">[[Dashboard]]</a> | <a href="{$GLOBALS.site_url}/system/users/logout/">[[Log out]]</a>
					</div>
					<div id="suggest-feature">
						<a href="http://ideas.smartjobboard.com" target="_blank">[[Suggest a Feature]]</a>
					</div>
				</div>
				
				<div class="InContent">
					<div style="margin: 0 215px 0 0">
						{module name='flash_messages' function='display'}
					</div>
					{$MAIN_CONTENT}
					<div class="clr"><br/></div>
				</div>
			
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div id="footer">
					[[Copyright]] &copy; 2013 [[Powered by]] <a href="http://www.smartjobboard.com/">SmartJobBoard</a> &#124; <a href="http://wiki.smartjobboard.com/display/sjb42/SmartJobBoard+v4.2+User+Manual">[[User Manual]]</a>
				</div>
			</td>
		</tr>
	</table>
	{module name="miscellaneous" function="profiler"}
</body>
</html>