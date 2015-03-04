{breadcrumbs}
	<a href="{$GLOBALS.site_url}/user-groups/">[[User Groups]]</a>
	&#187; <a href="{$GLOBALS.site_url}/edit-user-group/?sid={$group_info.sid}">{$group_info.name}</a>
	&#187; <a href="{$GLOBALS.site_url}/edit-user-profile/?user_group_sid={$group_info.sid}">[[Edit User Profile Fields]]</a>
	&#187; <a href='{$GLOBALS.site_url}/edit-user-profile-field/?sid={$field_info.sid}&user_group_sid={$group_info.sid}'>[[{$field_info.caption}]]</a>
	&#187; <a href='{$GLOBALS.site_url}/edit-user-profile-field/edit-location-fields/?field_sid={$field_sid}'>[[Edit Location Fields]]</a>
	&#187; [[Edit User Profile Field]]
{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperpencil32.png" border="0" alt="" class="titleicon"/>[[Edit User Profile Field]]</h1>

{if $field_type eq 'list' or $field_type eq 'multilist'}
	{if $userFieldInfo.id == 'Country'}
   		<p><a href="{$GLOBALS.site_url}/countries/" class="grayButton">[[Manage Country List]]</a></p>
   	{else}
   		<p><a href="{$GLOBALS.site_url}/states/" class="grayButton">[[Manage State List]]</a></p>
   	{/if}
{elseif $field_type eq 'geo'}
    <p><a href="{$GLOBALS.site_url}/geographic-data/" class="grayButton">[[Edit Geographic Data]]</a></p>
{/if}

{include file="field_errors.tpl"}
<fieldset>
	<legend>[[User Profile Field Info]]</legend>
	<table>
		<form method="post" enctype="multipart/form-data">
			<input type="hidden" name="action" value="edit" />
			<input type="hidden" name="sid" value="{$sid}" />
			<input type="hidden" name="field_sid" value="{$field_sid}" />
			{foreach from=$form_fields key=field_name item=form_field}
				{if $form_field.id == 'default_value'}
					{if $userFieldInfo.id == 'Country'}
						<tr id="tr_{$field_name}">
							<td id="td_caption_{$field_name}">[[{$form_field.caption}]]</td>
							<td class="required">{if $form_field.is_required}*{/if}</td>
							<td>
								<input type="radio" name="default_value_setting" onclick="checkDefaultValue()" value="default_country" {if !$userFieldInfo.default_value || $userFieldInfo.default_value == 'default_country'} checked = "checked" {/if} /> [[Use Default Country]]<br/>
								<input type="radio" name="default_value_setting" onclick="checkDefaultValue()" value="0" {if $userFieldInfo.default_value != '' && $userFieldInfo.default_value != 'default_country'} checked = "checked" {/if} />
								{assign var="disable" value=true}
								{input property=$form_field.id}
								{assign var="disable" value=false}
								{if $form_field.comment}<br /><span class="commentSmall">[[{$form_field.comment}]]</span>{/if}
								<script type="text/javascript">
									function checkDefaultValue() {
										var setting = $('input:radio[name=default_value_setting]:checked').val()
										if (setting == 0) 
											$("#default_value").attr("disabled", false);
										else {
											$("#default_value option:first").attr("selected", "selected");
											$("#default_value").attr("disabled", "disabled");
										}
									}
									checkDefaultValue();
								</script>
							</td>
						</tr>
					{else}
						<tr id="tr_{$field_name}">
							<td id="td_caption_{$field_name}">[[{$form_field.caption}]]</td>
							<td class="required">{if $form_field.is_required}*{/if}</td>
							<td>
								{if $form_field.comment}
									<select disabled="disabled" >
										<option>[[Select]] [[{$form_field.caption}]]</option>
									</select>
								{else}
									{input property=$form_field.id}
								{/if}
								{if $form_field.comment}<br /><span class="commentSmall">[[{$form_field.comment}]]</span>{/if}
							</td>
						</tr>
					{/if}
				{else}
					<tr id="tr_{$field_name}">
						<td id="td_caption_{$field_name}">[[{$form_field.caption}]] </td>
						<td class="required">{if $form_field.is_required}*{/if}</td>
						<td>
							{if $field_name eq 'choiceLimit'}
								{input property=$form_field.id}<br />
								<span class="commentSmall">[[Set empty or 0 for unlimited selection]]</span>
							{else}
								{input property=$form_field.id}
							{/if}
						</td>
					</tr>
				{/if}
			{/foreach}
			<tr>
                <td colspan="3">
                    <div class="floatRight">
                    	<input type="hidden" name="apply" value="no" />
                        <input type="submit" id="apply" name="submit_form" value="[[Apply]]" class="greenButton"/>
                        <input type="submit" name="submit_form"  value="[[Save]]" class="greenButton" />
                    </div>
                </td>
            </tr>
		</form>
	</table>
</fieldset>

<script>
	$("#profile_field").click(function() {
		if ( this.checked == false) {
			$("#defaultValue").css('display', 'block');
		}
		else {
			$("#defaultValue").css('display', 'none');
		}
	});

	$('#apply').click(
		function(){
			$("input[name='apply']").attr('value', 'yes');
		}
	);
</script>