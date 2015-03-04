<script type="text/javascript">
	function applySettings() {
		$('input[name="submit"]').attr('value', 'apply');
	}
</script>

{breadcrumbs}<a href="{$GLOBALS.site_url}/system/miscellaneous/plugins/">[[Plugins]]</a> &#187; {$plugin.name} [[Settings]]{/breadcrumbs}
<h1><img src="{image}/icons/plug32.png" border="0" alt="" class="titleicon"/>{$plugin.name} [[Settings]]</h1>
{foreach from=$errors item='error'}
	<p class="error">[[{$error}]]</p>
{/foreach}
{foreach from=$messages item='message'}
	<p class="message">[[{$message}]]</p>
{/foreach}

{if $plugin.name == "CaptchaPlugin"}
	<form method="post">
		<input type="hidden" name="action" value="save_settings">
		<input type="hidden" name="plugin" value="{$plugin.name}">
        <input type="hidden" name="submit" value="save">
		<table>
			<thead>
				<tr>
					<th>[[Name]]</th>
					<th>[[View]]</th>
					<th>[[Settings]]</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$settings item=pluginSettings}
					{assign var=setting_name value=$pluginSettings.id}
					{if $pluginSettings.type == 'separator'}
					<tr>
						<td colspan="2">{if $pluginSettings.caption}<strong>[[{$pluginSettings.caption}]]</strong>{else}&nbsp;{/if}</td>
					</tr>
					{else}
						{foreach from=$pluginSettings.list_values item=list}
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td><input type="radio" name="{$pluginSettings.id}" value="{$list.id}" {if $savedSettings.$setting_name == $list.id}checked=checked{/if} />&nbsp;[[{$list.caption}]]</td>
							<td>
								{if $list.id == 'kCaptcha'}
									<img id="captchaImg" src="{$GLOBALS.site_url}/../system/miscellaneous/captcha/" alt="[[Captcha]]" /></td>
								{else}
									{$list.view}
								{/if}
							<td>{if $list.id!='kCaptcha'}<a href="{$GLOBALS.site_url}/system/miscellaneous/plugins/?action=editCaptcha&amp;type={$list.id}&amp;plugin={$plugin.name}" class="editbutton">[[Edit]]</a>{/if}</td>
						</tr>
						{/foreach}
					{/if}
				{/foreach}
				<tr id="clearTable">
					<td  colspan="3" align="right">
                        <div class="floatRight">
                            <input type="submit" id="apply" value="[[Apply]]" class="grayButton" />
                            <input type="submit" class="grayButton" value="[[Save]]" />
                        </div>
                    </td>
				</tr>
			</tbody>
		</table>
	</form>

{else}

	<form method="post">
		<input type="hidden" name="action" value="save_settings">
		<input type="hidden" name="plugin" value="{$plugin.name}">
        <input type="hidden" name="submit" value="save">
		<table>
			<thead>
				<tr>
					<th>[[Name]]</th>
					<th>[[Value]]</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$settings item=pluginSettings name=pluginSettings}
					{assign var=setting_name value=$pluginSettings.id}
					{if $pluginSettings.type == 'separator'}
						<tr>
							<td colspan="2">{if $pluginSettings.caption}<strong>[[{$pluginSettings.caption}]]</strong>{else}&nbsp;{/if}</td>
						</tr>
						{else}
						{if $setting_name == 'IndeedKeywords' || $setting_name == 'simplyHiredKeyword' || $setting_name == 'JujuKeywords'}
						<tr>
							<td colspan="2"><strong>[[Default Filtering Parameters]]:</strong></td>
						</tr>
						{/if}
						{if $pluginSettings.id != 'jobAMaticDomain'}
							<tr class="{cycle values = 'evenrow,oddrow'}">
								<td>[[{$pluginSettings.caption}]]</td>
								<td>{if $pluginSettings.is_required}<span class="required">*</span>{/if}{$pluginSetting.tabName.id}
									{if $pluginSettings.type == 'boolean'}
										{if $setting_name == 'display_for_all_pages'}
											<input type="hidden" name="{$setting_name}" value="0" /><input type="checkbox" name="{$setting_name}" value="1" {if $savedSettings.$setting_name}checked="checked" {/if} onclick="CheckAll(this)" />
										{else}
											<input type="hidden" name="{$setting_name}" value="0" /><input type="checkbox" id="checkbox_{$smarty.foreach.pluginSettings.iteration}" name="{$setting_name}" value="1" {if $savedSettings.$setting_name}checked="checked" {/if} />
										{/if}
									{elseif $pluginSettings.id == 'simplyHiredSiteUrl'}
										<select name="{$pluginSettings.id}">
											{foreach from=$pluginSettings.list_values item=list}
												<option value="{$list.id}" {if $savedSettings.$setting_name}{if $savedSettings.$setting_name == $list.id}selected="selected" {/if} {else} {if $list.id == 'simplyhired.com'} selected="selected"{/if} {/if}>[[{$list.caption}]]</option>
											{/foreach}
										</select>
										<p> </p>
										<p id="jobAMaticDomain" style="line-height: 23px; margin: 1px 0;">[[Job-a-matic domain]]:</br>
											<span class="required">*</span>
											<input type="text" name="jobAMaticDomain" value="{$savedSettings.jobAMaticDomain|escape:'html'}" /><br/>
											<small>[[A domain name you got during the registration of publisher's account on SimplyHired]]</small>
										</p>
									{elseif  $pluginSettings.type == 'string'}
										<input type="text" name="{$pluginSettings.id}" value="{$savedSettings.$setting_name|escape:'html'}" />
									{elseif  $pluginSettings.type == 'text'}
										<textarea name="{$pluginSettings.id}" style="width: 250px; height: 150px;">{$savedSettings.$setting_name|escape:'html'}</textarea>
									{elseif  $pluginSettings.type == 'integer'}
										<input type="text" class="inputInteger" value="{$savedSettings.$setting_name}" name="{$pluginSettings.id}" />
									{elseif  $pluginSettings.type == 'list'}
										<select name="{$pluginSettings.id}">
											<option value="">[[Please Select Item]]:</option>
										{foreach from=$pluginSettings.list_values item=list}
											<option value="{$list.id}" {if $savedSettings.$setting_name == $list.id}selected="selected" {/if}>[[{$list.caption}]]</option>
										{/foreach}
										</select>
									{elseif  $pluginSettings.type == 'multilist'}
										<select name="{$pluginSettings.id}[]" multiple="multiple">
											<option value="">[[Please Select Items]]:</option>
										{assign var=selectedItems value=$savedSettings.$setting_name}
										{foreach from=$pluginSettings.list_values item=list}
											<option value="{$list.id}" {if in_array($list.id, explode(',', $selectedItems))}selected{/if}>[[{$list.caption}]]</option>
										{/foreach}
										</select>
									{/if}
									{if $pluginSettings.comment}
									<br/><small>[[{$pluginSettings.comment}]]</small>
									{/if}
								</td>
							</tr>
						{/if}
					{/if}
				{/foreach}
				{if $plugin.name == "MailChimpPlugin"}
					<tr><td colspan="2">[[Important: On Mailchimp list settings please do not change the tag of the First Name field ("FNAME")]].</td></tr>
				{/if}
			</tbody>
			<tr id="clearTable">
				<td colspan="2" align="right">
                    <div class="floatRight">
                        <input type="submit" class="grayButton" id="apply" value="[[Apply]]" />
                        <input type="submit" class="grayButton" value="[[Save]]" />
                    </div>
                </td>
			</tr>
		</table>
	</form>
{/if}
<div id="runManually"></div>

<script>
	var total = {if $smarty.foreach.pluginSettings.total}{$smarty.foreach.pluginSettings.total}{else}0{/if};

	function CheckAll(obj)
	{
		for (i = 4; i <= total; i++) {
			if (checkbox = document.getElementById('checkbox_' + i))
				checkbox.checked = obj.checked;
		}
	}

    $('#apply').click(
        function(){
            $('input[name="submit"]').attr('value', 'apply');
        }
    );

	$(document).ready(function () {
		showOrHideJBDBlock();
	});

	$("select[name='simplyHiredSiteUrl']").change(function() {
		showOrHideJBDBlock();
	});

	function showOrHideJBDBlock() {
		if ($("select[name='simplyHiredSiteUrl']").val() == 'simplyhired.com') {
			$("#jobAMaticDomain").show();
		} else {
			$("#jobAMaticDomain").hide();
		}
	}
</script>
