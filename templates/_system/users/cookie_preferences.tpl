{if $action == 'view'}
	<link rel="StyleSheet" type="text/css" href="{$GLOBALS.site_url}/templates/_system/main/images/css/CookiePreferences.css"  />

	{if $mobileVersion}
		{if $showCookiePreferences}
			<div id="cookiePreferencesMobile_front-end">
				<div id="cookiePreferencesInfBlock">
					[[This website uses cookies. You can either]] <a href="#">[[Agree]]</a> [[or change your]] <a href="{$GLOBALS.site_url}/cookie-preferences/">[[Cookie Preferences]]</a>.
				</div>
			</div>
		{/if}
	{else}
		{literal}
		<script language="javascript" type="text/javascript">
			function cookiePreferencesPopupOpen()
			{
				var url = "{/literal}{$GLOBALS.site_url}/cookie-preferences/{literal}";
				popUpWindow(url, 700, "{/literal}[[Cookie Preferences]]{literal}");
				return false;
			}

			function cookiePreferencesSave(value)
			{
				if (value === undefined) {
					if ($("#cookiePreferencesSlider").slider("value") != 0) {
						var answer = confirm(cookiePreferences_confirm);
						if (!answer) {
							return;
						}
					}

					value = cookiePreferencesSteps[cookiePreferencesValue];
				}


				var ajaxUrl    = "{/literal}{$GLOBALS.site_url}/ajax/{literal}";
				var ajaxParams = {
					"action" : "cookiePreferences",
					"cookiePreferencesValue" : value
				};

				$.get(ajaxUrl, ajaxParams, function(data) {
					if (data) {
						location.reload();
					}
				});
			}
		</script>
		{/literal}

		{if $showCookiePreferences}
			<div id="cookiePreferences_front-end">
				<div id="cookiePreferencesInfBlock">
					{if $smarty.cookies.cookiePreferences == "Advertising"}
						<p>
							[[We use cookies to make your experience of using our website better. To comply with the e-Privacy Directive we need to ask your consent to set these cookies.]]
						</p>
						<input type="button" id="closeCookiePreferences_front-end"  value="[[AGREE AND PROCEED]]" />
						<input type="button" onClick="cookiePreferencesPopupOpen();"  value="[[VIEW COOKIE PREFERENCES]]" />
					{else}
						<p class="error">
							[[You have opted out from usage of cookies on the website. This makes your journey on our website not so comfortable. If you wish you can enable cookies now.]]
						</p>
						<input type="button" onClick="cookiePreferencesSave('Advertising');" value="[[Enable]]" />
						<input type="button" id="closeCookiePreferences_front-end" value="[[No thanks]]" />
					{/if}
				</div>
			</div>

			{literal}
			<script language="JavaScript" type="text/javascript">
				$(document).ready(function() {
					$("#cookiePreferences_front-end").show("slide", {direction: "up"}, 1500)
													 .css("display", "block");
					$("#closeCookiePreferences_front-end").click(function() {
						$("#cookiePreferences_front-end").hide("slide", {direction: "up"}, 500);
					});
				});
			</script>
			{/literal}
		{/if}
	{/if}
{elseif $action == 'preferences'}
	{literal}
	<script language="javascript" type="text/javascript">
		var cookiePreferencesSteps = ["Advertising", "Functional", "System"];
		var cookiePreferencesValue = 0;
		for (var i = 0; i < cookiePreferencesSteps.length; i++) {
			if (cookiePreferencesSteps[i] == "{/literal}{$smarty.cookies.cookiePreferences}{literal}") {
				cookiePreferencesValue = i;
				break;
			}
		}
	</script>
	{/literal}

	{if $mobileVersion}
		<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.js"></script>
		{literal}
		<script language="javascript" type="text/javascript">
			function cookieCheckboxClick(checkBox)
			{
				if (checkBox.name == "Functional") {
					if (!checkBox.checked) {
						$('input[name=Advertising]').removeAttr("checked");
						cookiePreferencesGlowText(2);
					} else {
						cookiePreferencesGlowText(1);
					}

					return true;
				}

				if (checkBox.name == "Advertising") {
					if (!checkBox.checked) {
						cookiePreferencesGlowText(1);
					} else {
						$('input[name=Functional]').attr("checked", "checked");
						cookiePreferencesGlowText(0);
					}

					return true;
				}
			}
		</script>
		{/literal}
		<form action="" method="post">
		<table>
			<tr>
				<td colspan="2">
					[[Our website uses cookies. For more information on cookies visit www.allaboutcookies.org. Below you can check what cookies we use and opt out of them if needed:]]
				</td>
			</tr>
			<tr>
				<td class="cookiePreferencesDescription">
					<h1>
						[[System Cookies]]
					</h1>
					<span>
						[[Required to enable the core site functionality. These cookies cannot be disabled.]]
					</span>
				</td>
				<td>

				</td>
			</tr>
			<tr>
				<td class="cookiePreferencesDescription">
					<h1>
						[[Functional Cookies]]
					</h1>
					<span>
						[[Required to enable functionality of third-party applications.]]
					</span>
				</td>
				<td>
					<input type="checkbox" name="Functional" {if $smarty.cookies.cookiePreferences != "System"}checked="checked"{/if} onClick="return cookieCheckboxClick(this);">
				</td>
			</tr>
			<tr>
				<td class="cookiePreferencesDescription">
					<h1>
						[[Advertising Cookies]]
					</h1>
					<span>
						[[Used by advertising companies to serve ads relevant to your interests.]]
					</span>
				</td>
				<td>
					<input type="checkbox" name="Advertising" {if $smarty.cookies.cookiePreferences == "Advertising"}checked="checked"{/if} onClick="return cookieCheckboxClick(this);">
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<a href="{$smarty.session.cookiePreferencesMobileReferer}"><input type="button" onClick="" value="[[Cancel]]"></a>
					<input type="submit" name="cookiePreferencesSave"  value="[[Save]]">
				</td>
			</tr>
		</table>
		</form>
	{else}
		{literal}
		<script language="javascript" type="text/javascript">
			var cookiePreferences_confirm = "{/literal}[[If you disable cookies the website performance can become unstable and many functions will be turned off.]]{literal}";

			$("#cookiePreferencesSlider").slider({
				min: 0,
				max: 2,
				value: cookiePreferencesValue,
				slide: function( event, ui ) {
					cookiePreferencesGlowText(ui.value);
				},
				orientation: "vertical"
			});
		</script>
		{/literal}

		<p>
			[[Our website uses cookies. A cookie is a small file of letters and numbers that we put on your computer if you agree. These cookies allow us to distinguish you from other users of the website which helps us to provide you with a good experience when you browse our website and also allows us to improve our site. For more information on cookies visit www.allaboutcookies.org. Cookies are not dangerous for your computer. We don't store any personally identifiable information about you on any of our cookies. Below you can check what cookies we use and opt out of them using the slider if needed:]]
		</p>
		<div id="cookiePreferencesSlider"></div>
		<div class="cookiePreferencesDescription">
			<h1>
				[[System Cookies]]
			</h1>
			<span>
				[[Required to enable the core site functionality. They may be used for different purposes such as security, billing, navigation. These cookies expire after a browser session so would not be stored longer term. These cookies cannot be disabled.]]
			</span>
		</div>
		<div class="cookiePreferencesDescription">
			<h1>
				[[Functional Cookies]]
			</h1>
			<span>
				[[Required to enable functionality of third-party applications. These cookies are stored on a users' computer in between browser sessions which allows the preferences or actions of the user across a site to be remembered.]]
			</span>
		</div>
		<div class="cookiePreferencesDescription">
			<h1>
				[[Advertising Cookies]]
			</h1>
			<span>
				[[These cookies are used by advertising companies to serve ads that are relevant to your interests.]]
			</span>
		</div>
		<div id="cookiePreferencesPopupButtons">
			<input type="button" onClick="$('#messageBox').dialog('close');" value="[[Cancel]]">
			<input type="button" onClick="cookiePreferencesSave();" value="[[Save]]">
		</div>
	{/if}

	{literal}
	<script language="javascript" type="text/javascript">
		var sliderHeightDone = false;
		function cookiePreferencesGlowText(value)
		{
			cookiePreferencesValue = value;

			var cookieDescriptionHeight = 0;
			var counter                 = cookiePreferencesSteps.length - 1;
			$( ".cookiePreferencesDescription" ).each(function() {
				if (counter-- < cookiePreferencesValue) {
					$(this).children("h1").css("color", "#990000");
					$(this).children("span").css("color", "#999999");
				} else {
					$(this).children("h1").css("color", "#009900");
					$(this).children("span").css("color", "#000000");
				}

				if (!sliderHeightDone) {
					cookieDescriptionHeight += parseInt($(this).css("height"));
				}
			});

			if (!sliderHeightDone) {
				$("#cookiePreferencesSlider").height((cookieDescriptionHeight - 48) + "px");
				sliderHeightDone = true;
			}
		}

		cookiePreferencesGlowText(cookiePreferencesValue);
	</script>
	{/literal}
{/if}
