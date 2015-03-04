{breadcrumbs}<a href="{$GLOBALS.site_url}/system/miscellaneous/plugins/">Plugins</a> &#187; <a href="{$GLOBALS.site_url}/system/miscellaneous/plugins/?action=settings&plugin={$plugin}">{$plugin} Settings</a> &#187; Edit {$type}{/breadcrumbs}
<h1>Edit {$type}</h1>
{foreach from=$fieldErrors item="error_message" key="error"}
	{foreach from=$settings item=captchaSettings}
		{if $error == $captchaSettings.id}<p class="error">{$captchaSettings.caption} is empty</p>{/if}
	{/foreach}
{/foreach}
<form method="post">
	<input type="hidden" name="action" value="editCaptcha">
	<input type="hidden" name="event" value="save">
    <input type="hidden" id="submit" name="submit" value="save">
	<input type="hidden" name="type" value="{$type}">
	<input type="hidden" name="plugin" value="{$plugin}">
	<table  class="basetable" width="50%">
		<tr class="headrow">
			<td>Name</td>
			<td>Value</td>
		</tr>
		{foreach from=$settings item=captchaSettings}
			{assign var=setting_name value=$captchaSettings.id}
			<tr class="{cycle values = 'evenrow,oddrow'}">
				<td>{$captchaSettings.caption}</td>
				<td><span style="color: red">*</span>
					{if $captchaSettings.type == 'boolean'}
						<input type="hidden" name="settings[{$setting_name}]" value="0" /><input type="checkbox" name="settings[{$setting_name}" value="1" {if $savedSettings.$setting_name}checked="checked" {/if} />
					{elseif  $captchaSettings.type == 'string'}
						<input type="text" name="settings[{$captchaSettings.id}]" value="{$savedSettings.$setting_name}" style="width: 250px"/>
					{elseif  $captchaSettings.type == 'integer'}
						<input type="text" name="settings[{$captchaSettings.id}]" value="{$savedSettings.$setting_name}" style="width: 250px"/>
					{elseif  $captchaSettings.type == 'text'}
						<textarea name="settings[{$captchaSettings.id}]" style="width: 250px; height: 150px;">{$savedSettings.$setting_name}</textarea>
					{elseif  $captchaSettings.type == 'list'}
						<select name="settings[{$captchaSettings.id}]">
						{foreach from=$captchaSettings.list_values item=list}
							<option value="{$list.id}" {if $savedSettings.$setting_name == $list.id}selected="selected" {/if}>{$list.caption}</option>
						{/foreach}
						</select>
					{elseif  $captchaSettings.type == 'multilist'}
						<select name="settings[{$captchaSettings.id}][]" multiple="multiple">
						{assign var=selectedItems value=$savedSettings.$setting_name}
						{foreach from=$captchaSettings.list_values item=list}
							<option value="{$list.id}" {if in_array($list.id, explode(',', $selectedItems))}selected{/if}>{$list.caption}</option>
						{/foreach}
						</select>
					{/if}
					{if $captchaSettings.comment}
						<br/><small>{$captchaSettings.comment}</small>
					{/if}
				</td>
			</tr>
		{/foreach}
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td colspan="2" align="right">
                <span class="greenButtonEnd"><input type="submit" class="greenButton" value="Save" /></span>
                <span class="greenButtonEnd"><input type="submit" id="apply" value="Apply" class="greenButton" /></span>
            </td>
		</tr>
	</table>
</form>

{literal}
	<script>
        $('#apply').click(
            function(){
                $('#submit').attr('value', 'apply');
            }
        );
    </script>
{/literal}