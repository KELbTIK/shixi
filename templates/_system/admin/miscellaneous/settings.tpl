{breadcrumbs}[[System Settings]]{/breadcrumbs}
<h1><img src="{image}/icons/gear32.png" border="0" alt="" class="titleicon"/>[[System Settings]]</h1>
{foreach from=$errors item=error}
	<p class="error">[[{$error}]]</p>
{/foreach}

<form method="post" action="{$GLOBALS.site_url}/settings/" id="settingsPane">
	<input type="hidden" id="action" name="action" value="save_settings" />
    <input type="hidden" id="page" name="page" value="#generalTab"/>
	<div id="settingsPane">
		<ul class="ui-tabs-nav">
			<li class="ui-tabs-selected"><a href="#generalTab"><span>[[General]]</span></a></li>
            <li class="ui-tabs-unselect"><a href="#performanceTab"><span>[[Performance]]</span></a></li>
            <li class="ui-tabs-unselect"><a href="#notificationsTab"><span>[[Notifications]]</span></a></li>
			<li class="ui-tabs-unselect"><a href="#internationalizationTab"><span>[[Internationalization]]</span></a></li>
			<li class="ui-tabs-unselect"><a href="#currencyTab"><span>[[Billing]]</span></a></li>
			<li class="ui-tabs-unselect"><a href="#securityTab"><span>[[Security]]</span></a></li>
			<li class="ui-tabs-unselect"><a href="#mailTab"><span>[[Mail]]</span></a></li>
			<li class="ui-tabs-unselect"><a href="#errorControlTab"><span>[[Error Control]]</span></a></li>
		</ul>

		<div id="generalTab" class="ui-tabs-panel">
			<table width="100%">
				<thead>
					<tr>
						<th>[[Name]]</th>
						<th>[[Value]]</th>
					</tr>
				</thead>
				<tbody>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Site Title]]</td>
					<td><input type="text" name="site_title" value="{$settings.site_title}" /></td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Listing Picture Width]]</td>
					<td><input type="text" name="listing_picture_width" value="{$settings.listing_picture_width}" /></td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Listing Picture Height]]</td>
					<td><input type="text" name="listing_picture_height" value="{$settings.listing_picture_height}" /></td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Listing Thumbnail Width]]</td>
					<td><input type="text" name="listing_thumbnail_width" value="{$settings.listing_thumbnail_width}" /></td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Listing Thumbnail Height]]</td>
					<td><input type="text" name="listing_thumbnail_height" value="{$settings.listing_thumbnail_height}" /></td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Radius Search Unit]]</td>
					<td><select name="radius_search_unit"><option value="miles">[[Miles]]</option><option value="kilometers"{if $settings.radius_search_unit == 'kilometers'} selected{/if}>[[Kilometers]]</option></select></td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Upload file types]]</td>
					<td><input type="text" name="file_valid_types" size="50" value="{$settings.file_valid_types}" /></td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Turn Comments on]]</td>
					<td><input type="hidden" name="show_comments" value="0" /><input type="checkbox" name="show_comments" value="1"{if $settings.show_comments} checked="checked"{/if} /></td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Turn Ratings on]]</td>
					<td><input type="hidden" name="show_rates" value="0" /><input type="checkbox" name="show_rates" value="1"{if $settings.show_rates} checked="checked"{/if} /></td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Show "Terms of Use" check box on registration form]]</td>
					<td><input type="hidden" name="terms_of_use_check" value="0" /><input type="checkbox" name="terms_of_use_check" value="1"{if $settings.terms_of_use_check} checked="checked"{/if} /></td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Enable Maintenance Mode]] <a href="{$GLOBALS.site_url}/edit-templates/?module_name=miscellaneous&template_name=maintenance_mode.tpl" target="_blank" title="[[Edit maintenance_mode.tpl]]" class="edit-email-template"></a></td>
					<td><input type="hidden" name="maintenance_mode" value="0" /><input id="maintenance_mode_" type="checkbox" name="maintenance_mode" value="1"{if $settings.maintenance_mode} checked="checked"{/if} /><br/>
						[[enter IP or IP range to access the site]]<br/>
						<input type="text" value="{$settings.maintenance_mode_ip}" name="maintenance_mode_ip"/><br/>
						<sub>[[use * for replacing one or several digits<br />use comma (,) to specify two or more IPs]]</sub>
					</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Automatically Delete Expired Listings]]</td>
					<td><input type="hidden" name="automatically_delete_expired_listings" value="0" /><input type="checkbox" name="automatically_delete_expired_listings" value="1"{if $settings.automatically_delete_expired_listings} checked="checked"{/if} /> [[after]] <input type="text"  style="width:100px" name="period_delete_expired_listings" value="{$settings.period_delete_expired_listings}"/> [[days]]</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Use Autocomplete after]]</td>
					<td><input type="hidden" name="min_autocomplete_symbols_quantity" value="0" /> [[after]] <input type="text"  style="width:100px" name="min_autocomplete_symbols_quantity" value="{$settings.min_autocomplete_symbols_quantity}"/> [[symbols]]</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Enable autocomplete for 'Keywords' field on search forms]]</td>
					<td><input type="hidden" name="use_autocomplete_for_keywords" value="0" /><input type="checkbox" name="use_autocomplete_for_keywords" value="1"{if $settings.use_autocomplete_for_keywords} checked="checked"{/if} /></td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Enable Keywords Highlight for search results]]</td>
					<td><input type="hidden" name="use_highlight_for_keywords" value="0" /><input type="checkbox" name="use_highlight_for_keywords" value="1"{if $settings.use_highlight_for_keywords} checked="checked"{/if} /></td>
				</tr>

                <tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Enable Keywords Search in attached files (doc, docx, xls, xlsx, pdf)]]</td>
					<td><input type="hidden" name="get_keyword_from_file" value="0" /><input type="checkbox" name="get_keyword_from_file" value="1"{if $settings.get_keyword_from_file} checked="checked"{/if} /></td>
				</tr>

                <tr class="{cycle values = 'evenrow,oddrow'}">
                    <td>[[Display "Map View" button on search results page]]</td>
                    <td><input type="hidden" name="view_on_map" value="0" /><input type="checkbox" name="view_on_map" value="1"{if $settings.view_on_map} checked="checked"{/if} /></td>
                </tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Allow to post before checkout]]</td>
					<td><input type="hidden" name="allow_to_post_before_checkout" value="0" /><input type="checkbox" name="allow_to_post_before_checkout" value="1"{if $settings.allow_to_post_before_checkout} checked="checked"{/if} /></td>
				</tr>

				<tr id="clearTable">
					<td colspan="2" align="right"><div class="floatRight"><input type="submit" class="greenButton" value="[[Save]]" /></div></td>
				</tr>

				</tbody>
			</table>

		</div>

        <div id="performanceTab" class="ui-tabs-panel">

            <table class="basetable" width="100%">

                <tr class="headrow">
                    <td>[[Name]]</td>
                    <td>[[Value]]</td>
                </tr>

                <tr class="{cycle values = 'evenrow,oddrow'}">
                    <td>[[Enable Caching]]</td>
                    <td><input type="hidden" name="enableCache" value="0" /><input id="enableCache" type="checkbox" name="enableCache" value="1"{if $settings.enableCache} checked="checked"{/if} /></td>
                </tr>

                <tr class="{cycle values = 'evenrow,oddrow'}">
                    <td>[[Cache Lifetime]]</td>
                    <td>
                        <input type="text" style="width: 30px" name="cacheHours" value="{$settings.cacheHours}" /> [[h]]
                        <input type="text" name="cacheMinutes" style="width: 30px" value="{$settings.cacheMinutes}" /> [[m]]
                    </td>
                </tr>

                <tr class="{cycle values = 'evenrow,oddrow'}">
                    <td>[[Enable]] &quot;[[Browse by]]&quot; [[counter]]</td>
                    <td><input type="hidden" name="enableBrowseByCounter" value="0" /><input type="checkbox" name="enableBrowseByCounter" value="1"{if $settings.enableBrowseByCounter} checked="checked"{/if} /></td>
                </tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Enable gzip-compression for Site Pages]]</td>
					<td><input type="hidden" name="gzip_compression" value="0" /><input type="checkbox" name="gzip_compression" value="1"{if $settings.gzip_compression} checked="checked"{/if} /></td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Enable profiler]]</td>
					<td><input type="hidden" name="profiler" value="0" /><input type="checkbox" name="profiler" value="1"{if $settings.profiler} checked="checked"{/if} /></td>
				</tr>

                <tr id="clearTable">
                    <td colspan="2" align="right">
                        <div class="floatRight">
                            <input type="submit" value="[[Apply]]" class="grayButton" onclick="applySettings(this)"/>
                            <input type="submit" class="grayButton" value="[[Save]]" />
                        </div>
                    </td>
                </tr>

            </table>

        </div>

        <div id="notificationsTab" class="ui-tabs-panel ui-tabs-hide">

			<table class="basetable" width="100%">
				<tr class="headrow">
					<td>[[Name]]</td>
					<td>[[Value]]</td>
				</tr>
				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Admin Email to receive Notifications]]</td>
					<td><input type="text" name="notification_email" value="{$settings.notification_email}" /></td>
				</tr>
				<tr>
					<td colspan="2"><small>* [[System Notifications (marked below) will be sent to this email address]] </small></td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Test Email]]</td>
					<td><input type="text" name="test_email" value="{$settings.test_email}" /></td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Notify Admin on Listing Added]]</td>
					<td>
						<select name="notify_on_listing_added" class="left">
							<option value="">[[None]]</option>
							{foreach from=$listingEmailTemplates item="emailTemplate"}
							<option value="{$emailTemplate.sid}" {if $settings.notify_on_listing_added == $emailTemplate.sid}selected="selected"{/if}>{$emailTemplate.name}</option>
							{/foreach}
						</select>
						{if $settings.notify_on_listing_added}
							<a href="{$GLOBALS.site_url}/edit-email-templates/listing/{$settings.notify_on_listing_added}" target="_blank" title="[[Notify Admin on Listing Added]]" class="edit-email-template"></a>
						{/if}
					</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Notify Admin on User Registration]]</td>
					<td>
						<select name="notify_on_user_registration" class="left">
							<option value="">[[None]]</option>
							{foreach from=$userEmailTemplates item="emailTemplate"}
							<option value="{$emailTemplate.sid}" {if $settings.notify_on_user_registration == $emailTemplate.sid}selected="selected"{/if}>{$emailTemplate.name}</option>
							{/foreach}
						</select>
						{if $settings.notify_on_user_registration}
							<a href="{$GLOBALS.site_url}/edit-email-templates/user/{$settings.notify_on_user_registration}" target="_blank" title="[[Notify Admin on User Registration]]" class="edit-email-template"></a>
						{/if}
					</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Notify Admin on Listing Expiration]]</td>
					<td>
						<select name="notify_on_listing_expiration">
							<option value="">[[None]]</option>
							{foreach from=$listingEmailTemplates item="emailTemplate"}
							<option value="{$emailTemplate.sid}" {if $settings.notify_on_listing_expiration == $emailTemplate.sid}selected="selected"{/if}>{$emailTemplate.name}</option>
							{/foreach}
						</select>
						{if $settings.notify_on_listing_expiration}
							<a href="{$GLOBALS.site_url}/edit-email-templates/listing/{$settings.notify_on_listing_expiration}" target="_blank" title="[[Notify Admin on Listing Expiration]]" class="edit-email-template"></a>
						{/if}
					</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Notify Admin on User Product Expiration]]</td>
					<td>
						<select name="notify_on_user_contract_expiration">
							<option value="">[[None]]</option>
							{foreach from=$productEmailTemplates item="emailTemplate"}
							<option value="{$emailTemplate.sid}" {if $settings.notify_on_user_contract_expiration == $emailTemplate.sid}selected="selected"{/if}>{$emailTemplate.name}</option>
							{/foreach}
						</select>
						{if $settings.notify_on_user_contract_expiration}
							<a href="{$GLOBALS.site_url}/edit-email-templates/product/{$settings.notify_on_user_contract_expiration}" target="_blank" title="[[Notify Admin on User Product Expiration]]" class="edit-email-template"></a>
						{/if}
					</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Notify Admin on User Profile Deletion]]</td>
					<td>
						<select name="notify_admin_on_deleting_user_profile">
							<option value="">[[None]]</option>
							{foreach from=$userEmailTemplates item="emailTemplate"}
							<option value="{$emailTemplate.sid}" {if $settings.notify_admin_on_deleting_user_profile == $emailTemplate.sid}selected="selected"{/if}>{$emailTemplate.name}</option>
							{/foreach}
						</select>
						{if $settings.notify_admin_on_deleting_user_profile}
							<a href="{$GLOBALS.site_url}/edit-email-templates/user/{$settings.notify_admin_on_deleting_user_profile}" target="_blank" title="[[Notify Admin on User Profile Deletion]]" class="edit-email-template"></a>
						{/if}
					</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Notify Admin on Listing Flagged]]</td>
					<td>
						<select name="notify_admin_on_listing_flagged">
							<option value="">[[None]]</option>
							{foreach from=$listingEmailTemplates item="emailTemplate"}
							<option value="{$emailTemplate.sid}" {if $settings.notify_admin_on_listing_flagged == $emailTemplate.sid}selected="selected"{/if}>{$emailTemplate.name}</option>
							{/foreach}
						</select>
						{if $settings.notify_admin_on_listing_flagged}
							<a href="{$GLOBALS.site_url}/edit-email-templates/listing/{$settings.notify_admin_on_listing_flagged}" target="_blank" title="[[Notify Admin on Listing Flagged]]" class="edit-email-template"></a>
						{/if}
					</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Notify Admin on Sub-Admin registration]]</td>
					<td>
						<select name="notify_admin_on_subadmin_registration">
							<option value="">[[None]]</option>
							{foreach from=$otherEmailTemplates item="emailTemplate"}
							<option value="{$emailTemplate.sid}" {if $settings.notify_admin_on_subadmin_registration == $emailTemplate.sid}selected="selected"{/if}>{$emailTemplate.name}</option>
							{/foreach}
						</select>
						{if $settings.notify_admin_on_subadmin_registration}
							<a href="{$GLOBALS.site_url}/edit-email-templates/user/{$settings.notify_admin_on_subadmin_registration}" target="_blank" title="[[Notify Admin on Sub-Admin registration]]" class="edit-email-template"></a>
						{/if}
					</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Notify Admin on User Approval]]</td>
					<td>
						<select name="notify_admin_on_user_approval">
						<option value="">[[None]]</option>
						{foreach from=$userEmailTemplates item="emailTemplate"}
							<option value="{$emailTemplate.sid}" {if $settings.notify_admin_on_user_approval == $emailTemplate.sid}selected="selected"{/if}>{$emailTemplate.name}</option>
							{/foreach}
						</select>
						{if $settings.notify_admin_on_user_approval}
							<a href="{$GLOBALS.site_url}/edit-email-templates/user/{$settings.notify_admin_on_user_approval}" target="_blank" title="[[Notify Admin on User Approval]]" class="edit-email-template"></a>
						{/if}
					</td>
				</tr>
				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Notify Admin on User Rejection]]</td>
					<td>
						<select name="notify_admin_on_user_rejection">
						<option value="">[[None]]</option>
						{foreach from=$userEmailTemplates item="emailTemplate"}
							<option value="{$emailTemplate.sid}" {if $settings.notify_admin_on_user_rejection == $emailTemplate.sid}selected="selected"{/if}>{$emailTemplate.name}</option>
							{/foreach}
						</select>
						{if $settings.notify_admin_on_user_rejection}
							<a href="{$GLOBALS.site_url}/edit-email-templates/user/{$settings.notify_admin_on_user_rejection}" target="_blank" title="[[Notify Admin on User Rejection]]" class="edit-email-template"></a>
						{/if}
					</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Number of Listings sent in Email Alerts]]</td>
					<td>
						<input type="text" name="num_of_listings_sent_in_email_alerts" value="{$settings.num_of_listings_sent_in_email_alerts}"/>
					</td>
				</tr>

				<tr id="clearTable">
					<td colspan=2 align="right">
                        <div class="floatRight">
                            <input type="submit" value="[[Apply]]" class="grayButton" onclick="applySettings(this)"/>
                            <input type="submit" class="grayButton" value="[[Save]]" />
                        </div>
                    </td>
				</tr>

			</table>
		</div>

		<div id="internationalizationTab" class="ui-tabs-panel">

			<table class="basetable" width="100%">

				<tr class="headrow">
					<td>[[Name]]</td>
					<td>[[Value]]</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Default Country]]</td>
					<td>
					    <select name="default_country">
					    	<option value="">[[Select Country]]</option>
						    {foreach from=$countries item=country}
						    	<option value="{$country.id}" {if $settings.default_country == $country.id} selected="selected"{/if}>[[{$country.caption}]]</option>
						    {/foreach}
					    </select>
					</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Default Domain]]</td>
					<td>
					    <select name="i18n_default_domain">
						    {foreach from=$i18n_domains item=domain}
						    	<option value="{$domain}"{if $settings.i18n_default_domain == $domain} selected="selected"{/if}>[[{$domain}]]</option>
						    {/foreach}
					    </select>
					</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Default Language]]</td>
					<td>
					    <select name="i18n_default_language">
						    {foreach from=$i18n_languages item=language}
						    	<option value="{$language.id}"{if $settings.i18n_default_language == $language.id} selected="selected"{/if}>{$language.caption}</option>
						    {/foreach}
					    </select>
					</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Mark Phrases That Are Not Translated]]</td>
					<td>
					    <select name="i18n_display_mode_for_not_translated_phrases">
						    <option value="default">[[default]]</option>
						    <option value="highlight"{if $settings.i18n_display_mode_for_not_translated_phrases == 'highlight'} selected="selected"{/if}>[[highlight]]</option>
					    </select>
					</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Timezone]]</td>
					<td>
					    <select name="timezone">
						    {foreach from=$timezones item=timezone}
						    	<option value="{$timezone}" {if $settings.timezone == $timezone} selected="selected"{/if}>{$timezone}</option>
						    {/foreach}
					    </select>
					</td>
				</tr>

				<tr id="clearTable">
					<td colspan="2" align="right">
                        <div class="floatRight">
                            <input type="submit" value="[[Apply]]" class="grayButton" onclick="applySettings(this)"/>
                            <input type="submit" class="grayButton" value="[[Save]]" />
                        </div>
                    </td>
				</tr>

			</table>

		</div>

		<div id="currencyTab" class="ui-tabs-panel">

			<table class="basetable" width="100%">
				<tr class="headrow">
					<td>[[Name]]</td>
					<td>[[Value]]</td>
				</tr>
                <tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Enable Taxes]]</td>
					<td><input type="hidden" name="enable_taxes" value="0"/><input type="checkbox" name="enable_taxes" value="1" {if $settings.enable_taxes}checked = checked{/if}/></td>
				</tr>
				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Billing Currency Sign]]</td>
					<td><input type="text" size="3" name="transaction_currency" value="{$settings.transaction_currency}" /></td>
				</tr>
				<tr>
					<td colspan="2"><small>* [[This currency sign will be used for displaying your site services prices]]</small></td>
				</tr>
                <tr class="{cycle values = 'evenrow,oddrow'}">
                    <td>[[Send Payment To]]</td>
                    <td><textarea name="send_payment_to" cols="50" rows="6">{$settings.send_payment_to}</textarea></td>
                </tr>
                <tr>
                    <td colspan="2"><small>*&nbsp;[[This text will be displayed in invoices]]</small></td>
                </tr>
				<tr id="clearTable">
					<td colspan="2" align="right">
                        <div class="floatRight">
                            <input type="submit" value="[[Apply]]" class="grayButton" onclick="applySettings(this)"/>
                            <input type="submit" class="grayButton" value="[[Save]]" />
                        </div>
                    </td>
				</tr>
			</table>

		</div>

		<div id="securityTab" class="ui-tabs-panel">

			<table class="basetable" width="100%">
				<tr class="headrow">
					<td>[[Name]]</td>
					<td>[[Value]]</td>
				</tr>
				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Use CAPTCHA for Registration forms]]</td>
					<td><input type="hidden" name="registrationCaptcha" value="0" /><input type="checkbox" name="registrationCaptcha" value="1"{if $settings.registrationCaptcha} checked="checked"{/if} /></td>
				</tr>
				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Use CAPTCHA for Post Job/Resume forms]]</td>
					<td><input type="hidden" name="postJobCaptcha" value="0" /><input type="checkbox" name="postJobCaptcha" value="1"{if $settings.postJobCaptcha} checked="checked"{/if} /></td>
				</tr>
				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Use CAPTCHA for Contact us form]]</td>
					<td><input type="hidden" name="contactUsCaptcha" value="0" /><input type="checkbox" name="contactUsCaptcha" value="1"{if $settings.contactUsCaptcha} checked="checked"{/if} /></td>
				</tr>
				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Use CAPTCHA for Tell a friend form]]</td>
					<td><input type="hidden" name="tellFriendCaptcha" value="0" /><input type="checkbox" name="tellFriendCaptcha" value="1"{if $settings.tellFriendCaptcha} checked="checked"{/if} /></td>
				</tr>
				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Use CAPTCHA for Contact user/Application forms]]</td>
					<td><input type="hidden" name="contactUserCaptcha" value="0" /><input type="checkbox" name="contactUserCaptcha" value="1"{if $settings.contactUserCaptcha} checked="checked"{/if} /></td>
				</tr>
				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Use CAPTCHA for Flag Listing form]]</td>
					<td><input type="hidden" name="flagListingCaptcha" value="0" /><input type="checkbox" name="flagListingCaptcha" value="1"{if $settings.flagListingCaptcha} checked="checked"{/if} /></td>
				</tr>
				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Enable Cookie Law compatibility]]</td>
					<td><input type="hidden" name="cookieLaw" value="0" /><input type="checkbox" name="cookieLaw" value="1"{if $settings.cookieLaw} checked="checked"{/if} /></td>
				</tr>
				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Bind session to IP address]]</td>
					<td><input type="hidden" name="sessionBindIP" value="0" /><input type="checkbox" name="sessionBindIP" value="1"{if $settings.sessionBindIP} checked="checked"{/if} /></td>
				</tr>
				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Maximum allowed authentication attempts without captcha]]
						<div><small>* [[Set value 0 or leave it empty if you would like to disabe this setting]]</small></div>
					</td>
					<td><input type="text" size="3" name="captcha_max_allowed_auth_attempts" value="{$settings.captcha_max_allowed_auth_attempts}" /></td>
				</tr>
				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>
						[[Ban web crawlers]]
						<div><small>* [[This setting allows you to disable web crawlers to index pages on your site. This can significantly reduce the load of your site. To disable certain web crawler please enter "User Agent" used by this crawler into the text field on the right side. Use new line sign to separate several web crawlers. To turn off this setting just delete everything from that field.]]</small></div>
					</td>
					<td><textarea name="disable_bots" rows="10" cols="50">{$disable_bots}</textarea></td>
				</tr>
                <tr class="{cycle values = 'evenrow,oddrow'}">
                    <td>[[Behavior With Escape HTML Tags]]</td>
                    <td>
                        <select name="escape_html_tags">
                            <option value="">[[Raw output (unsafe, XSS possible)]]</option>
                            <option value="htmlentities"{if $settings.escape_html_tags == 'htmlentities'} selected="selected"{/if}>[[Convert escape chars to ASCII symbols (beta)]]</option>
                            <option value="htmlpurifier"{if $settings.escape_html_tags == 'htmlpurifier'} selected="selected"{/if}>[[Strip Tags]]</option>
                        </select>
                    </td>
                </tr>
                <tr class="{cycle values = 'evenrow,oddrow'}">
                    <td>[[Bad word filter]]</td>
                    <td><textarea name="bad_words">{$settings.bad_words}</textarea></td>
                </tr>
                <tr>
                    <td colspan="2"><small>* [[List undesirable words separated by space]].</small></td>
                </tr>
                <tr id="clearTable">
					<td colspan="2" align="right">
                        <div class="floatRight">
                            <input type="submit" value="[[Apply]]" class="grayButton" onclick="applySettings(this)"/>
                            <input type="submit" class="grayButton" value="[[Save]]" />
                        </div>
                    </td>
				</tr>
			</table>

		</div>

		<div id="mailTab" class="ui-tabs-panel">
			<table class="basetable" width="100%">
				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[From Name]]</td>
					<td><input type="text" name="FromName" value="{$settings.FromName}" /></td>
				</tr>
				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[System Email]]</td>
					<td><input type="text" name="system_email" value="{$settings.system_email}" /></td>
				</tr>
				<tr>
					<td colspan="2"><small>* [[Users will get notifications from this email address]]</small></td>
				</tr>
				<tr class="{cycle values = 'evenrow,oddrow'}">
				<td>[[Mail Signature]]</td>
				<td><textarea cols="80" rows="6" name="system_email_signature">{$settings.system_email_signature}</textarea></td>
				</tr>
			</table>
			<table class="basetable" width="100%" >
				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td class="strong"><input type="radio" name="smtp" value="1" {if $settings.smtp == 1}checked="checked"{/if} /> [[SMTP]]</td>
				</tr>
			</table>
			<div class="smtp">
				<table class="basetable" width="100%">
					<tr class="headrow">
						<td>[[Name]]</td>
						<td>[[Value]]</td>
					</tr>
					<tr class="{cycle values = 'evenrow,oddrow'}">
						<td>[[SMTP Sender Mail]]</td>
						<td><input type="text" name="smtp_sender" value="{$settings.smtp_sender}" /></td>
					</tr>
					<tr class="{cycle values = 'evenrow,oddrow'}">
						<td>[[SMTP Port]]</td>
						<td><input type="text" name="smtp_port" value="{$settings.smtp_port}" /></td>
					</tr>
					<tr class="{cycle values = 'evenrow,oddrow'}">
						<td>[[SMTP Host]]</td>
						<td><input type="text" name="smtp_host" value="{$settings.smtp_host}" /></td>
					</tr>
					<tr class="{cycle values = 'evenrow,oddrow'}">
						<td>[[SMTP Security]]</td>
						<td>
							<input type="radio" name="smtp_security" value="none" {if $settings.smtp_security != 'ssl' && $settings.smtp_security != 'tls'}checked="checked"{/if} />[[None]]&nbsp;&nbsp;
							<input type="radio" name="smtp_security" value="ssl" {if $settings.smtp_security == 'ssl'}checked="checked"{/if} />[[SSL]]&nbsp;&nbsp;
							<input type="radio" name="smtp_security" value="tls" {if $settings.smtp_security == 'tls'}checked="checked"{/if} />[[TLS]]

						</td>
					</tr>
					<tr>
						<td colspan="2"><small>* [[Look for your SMTP mail host requirements]]</small></td>
					</tr>
					<tr class="{cycle values = 'evenrow,oddrow'}">
						<td>[[Username]]</td>
						<td><input type="text" name="smtp_username" value="{$settings.smtp_username}" /></td>
					</tr>
					<tr class="{cycle values = 'evenrow,oddrow'}">
						<td>[[Password]]</td>
						<td><input type="password" name="smtp_password" value="{$settings.smtp_password}" /></td>
					</tr>
				</table>
			</div>
			<div class="sendmail">
				<table class="basetable" width="100%">
					<tr class="{cycle values = 'evenrow,oddrow'}">
						<td style="font-weight: bold;"><input type="radio" name="smtp" value="0"  {if $settings.smtp == 0}checked="checked"{/if} /> [[Sendmail]]</td>
					</tr>
				</table>
				<table class="basetable" width="100%">
					<tr class="headrow">
						<td>[[Name]]</td>
						<td>[[Value]]</td>
					</tr>
					<tr class="{cycle values = 'evenrow,oddrow'}">
						<td width="226px">[[Path to sendmail]]</td>
						<td><input type="text" name="sendmail_path" value="{$settings.sendmail_path}" /></td>
					</tr>
				</table>
			</div>
			<div class='sendmail'>
				<table class="basetable" width="100%">
					<tr class="{cycle values = 'evenrow,oddrow'}">
						<td class="strong"><input type="radio" name="smtp" value="3"  {if $settings.smtp == 3}checked="checked"{/if} /> [[PHP Mail Function]]</td>
					</tr>
				</table>
			</div>
			<div class="sendmail">
				<table class="basetable" width="100%">
					<tr class="{cycle values = 'evenrow,oddrow'}">
						<td width='226px'>[[Enable Email Scheduling]]</td>
						<td>
							<input type="hidden" name="email_scheduling" value="0"/>
							<input type="checkbox" id="email_scheduling" name='email_scheduling' value='1'  {if $settings.email_scheduling}checked='checked'{/if} /><br/>
							<strong>[[Number of emails to be sent per hour]]</strong><br/>
							<input type="text" id="number_emails" name="number_emails" value="{$settings.number_emails}"/><br/>
							<small>[[You need to make additional settings for CRON for this option to work. Use the following link]]: {$GLOBALS.user_site_url}/email-scheduling/</small>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<small>
								[[Here is an example of the full CRON script in Unix format to run the email scheduling every hour]]:<br/>
								<strong>0 */1 * * * wget --tries=1 --timeout=99999 -O email_log.txt {$GLOBALS.user_site_url}/email-scheduling/</strong>
							</small>
						</td>
					</tr>
					<tr class="{cycle values = 'evenrow,oddrow'}">
						<td width="226px">[[Check mail set up]]</td>
						<td><input id="checkMail" type="submit" value="[[Check]]" class="grayButton"/></td>
					</tr>
				</table>
			</div>
			<table class="basetable" width="100%">
				<tr id="clearTable">
					<td colspan="2" align="right">
                        <div class="floatRight">
                            <input type="submit" value="[[Apply]]" class="grayButton" onclick="applySettings(this)"/>
                            <input type="submit" class="grayButton" value="[[Save]]" />
                        </div>
                    </td>
				</tr>
			</table>
		</div>


		<div id="errorControlTab" class="ui-tabs-panel">

			<table class="basetable" width="100%">

				<tr class="headrow">
					<td>[[Name]]</td>
					<td>[[Value]]</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Error Control mode]]</td>
					<td><select name="error_control_mode">
							<option value="production" {if $settings.error_control_mode == 'production'} selected="selected"{/if}>[[Production]]</option>
							<option value="debug" {if $settings.error_control_mode == 'debug'} selected="selected"{/if}>[[Debug]]</option>
						</select>
					</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td></td>
					<td><small>* [[Production mode hide runtime errors from page]]</small></td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Log Errors]]</td>
					<td>
						<input name="error_logging" type="hidden" value="0" />
						<input name="error_logging" type="checkbox" value="1" {if $settings.error_logging == '1'} checked="checked"{/if} />
					</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Error Level]]</td>
					<td><select name="error_log_level">
							<option value="E_ALL" {if $settings.error_log_level == 'E_ALL'}selected="selected"{/if}>[[All]]</option>
							<option value="E_WARNING" {if $settings.error_log_level == 'E_WARNING'}selected="selected"{/if}>[[Errors And Warnings]]</option>
							<option value="E_ERROR" {if $settings.error_log_level == 'E_ERROR'}selected="selected"{/if}>[[Only Errors]]</option>
						</select>
					</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td>[[Log Lifetime]]</td>
					<td><select name="error_log_lifetime">
							<option value="1" {if $settings.error_log_lifetime == '1'}selected="selected"{/if}>1 [[day]]</option>
							<option value="3" {if $settings.error_log_lifetime == '3'}selected="selected"{/if}>3 [[days]]</option>
							<option value="7" {if $settings.error_log_lifetime == '7'}selected="selected"{/if}>7 [[days]]</option>
							<option value="14" {if $settings.error_log_lifetime == '14'}selected="selected"{/if}>14 [[days]]</option>
							<option value="30" {if $settings.error_log_lifetime == '30'}selected="selected"{/if}>30 [[days]]</option>
						</select>
					</td>
				</tr>

				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td colspan="2"><a href="{$GLOBALS.site_url}/view-error-log/">[[View Error Log]]</a></td>
				</tr>

				<tr id="clearTable">
					<td colspan="2" align="right">
                        <div class="floatRight">
                            <input type="submit" value="[[Apply]]" class="grayButton" onclick="applySettings(this)"/>
                            <input type="submit" class="grayButton" value="[[Save]]" />
                        </div>
                    </td>
				</tr>

			</table>

		</div>
</form>
</div>
<br />
<div><a href="{$GLOBALS.site_url}/alphabet-letters/">[[Alphabet Letters for "Search by Company" section]]</a></div>


<script type="text/javascript">
	$(document).ready(function() {
		$("#settingsPane").tabs();
        checkUncheckIPBlock();
        checkUncheckCacheLifetime();
		$("#maintenance_mode_").click(function() {
			checkUncheckIPBlock();
		});
        $("#enableCache").click(function() {
            checkUncheckCacheLifetime();
        });

		$("#checkMail").click(function () {

			var preloader = $(this).after(getPreloaderCodeForFieldId("checkMailLoader"));
			$("#checkMail").attr("disabled", "disabled");

			$.ajax({
				type:"POST",
				url:window.SJB_GlobalSiteUrl + "/system/miscellaneous/mail_check/",
				data:$("#settingsPane").serialize(),
				success:function (html) {
							$(preloader).next("span").remove();
							var result = JSON.parse(html);
							$(".message").remove();
							$(".error").remove();
							if (result["status"] == true) {
								$("#settingsPane").before('<p class="' + result["type"] + '">[[Your mail is set up correctly and functions fine.]]</p>');
							}
							if (result["status"] == false) {
								$("#settingsPane").before('<p class="' + result["type"] + '">[[Your mail is not functioning. Please check Admin Panel and server settings.]]</p>');
							}
							if (result["status"] == "fieldError") {
								var fieldCaption = {
									"smtp_host" : "[[SMTP Host]]",
									"smtp_port" : "[[SMTP Port]]",
									"smtp_sender" : "[[SMTP Sender Mail]]",
									"smtp_username" : "[[Username]]",
									"smtp_password" : "[[Password]]",
									"sendmail_path" : "[[Path to sendmail]]",
									"system_email" : "[[System email]]"
								};
								var messages = result["message"];
								$.each(messages, function(key) {
									if (key == "EMPTY_VALUE") {
										$.each(messages[key], function(key, value) {
											$("#settingsPane").before('<p class="' + result["type"] + '">"' + fieldCaption[value] + '" [[field is empty.]]</p>');
										});
									}

									if (key == "NOT_VALID") {
										$.each(messages[key], function(key, value) {
											$("#settingsPane").before('<p class="' + result["type"] + '">"' + fieldCaption[value] + '" [[field is not valid.]]</p>');
										});
									}
								});
							}
							$("#checkMail").attr("disabled", "");
							$(window).scrollTop(0);
						}
			});
			return false;
		});

	});

	$(".setting_button").click(function(){
		var butt = $(this);
		$(this).next(".setting_block").slideToggle("normal", function(){
            if ($(this).css("display") == "block") {
                butt.children(".setting_icon").html("[-]");
            } else {
                butt.children(".setting_icon").html("[+]");
            }
        });
	});

    function checkUncheckIPBlock() {
        if ($("#maintenance_mode_").attr("checked"))
            $("input[name='maintenance_mode_ip']").removeAttr("disabled");
        else
            $("input[name='maintenance_mode_ip']").attr("disabled", "disabled");
    }

    function checkUncheckCacheLifetime() {
        if ($("#enableCache").attr("checked")) {
            $("input[name='cacheHours']").removeAttr("disabled");
            $("input[name='cacheMinutes']").removeAttr("disabled");
        }
        else {
            $("input[name='cacheHours']").attr("disabled", "disabled");
            $("input[name='cacheMinutes']").attr("disabled", "disabled");
        }
    }

    function applySettings(id) {
        var div_id = $(id).parents('.ui-tabs-panel').attr('id');
        div_id = '#' + div_id;
        $('#page').attr('value', div_id);
        $('#action').attr('value', 'apply_settings');
    }

    $(function() {
        if ($('#email_scheduling').attr('checked')) {
            $('#number_emails').attr('disabled', '');
        } else {
            $('#number_emails').attr('disabled', 'disabled');
        }
    });

    $('#email_scheduling').click(function(){
        var checked = $('#email_scheduling').attr('checked');
        if (checked) {
            $('#number_emails').attr('disabled', '');
        } else {
            $('#number_emails').attr('disabled', 'disabled');
        }
    });

    var page = '{$page}';
    if (page) {
        $("#settingsPane ul li").each(function(){
            if ($('a', this).attr('href') == page) {
                var cl = $(this).attr('class') + ' ui-tabs-selected';
                $(this).attr('class', cl);
            } else {
                $(this).attr('class', 'ui-tabs-unselect');
            }
        });
    }
</script>

