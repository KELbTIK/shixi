{breadcrumbs}<a href="{$GLOBALS.site_url}/listing-types/">[[Listing Types]]</a> &#187; [[{$listing_type_info.name}]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperpencil32.png" border="0" class="titleicon" />[[Edit Listing Type Info]]</h1>
<p><a href="{$GLOBALS.site_url}/posting-pages/{$listing_type_info.id|lower}" class="grayButton">[[Edit Posting Pages]]</a></p>

{include file="errors.tpl" errors=$errors}
<fieldset>
	<legend>[[Listing Type Info]]</legend>
	<form method="post" action="">
		<input type="hidden" id="action" name="action" value="save_info" />
		<input type="hidden" name="sid" value="{$listing_type_info.sid}" />
		<table>
			{foreach from=$form_fields key=field_name item=form_field}
				<tr>
					<td valign="top">[[{$form_field.caption}]]</td>
					<td valign="top" class="required">{if $form_field.is_required}*{/if}</td>
					<td valign="top">
						{input property=$form_field.id}
						{if $form_field.id eq 'email_alert' && $listing_type_info.email_alert}
							<a href="{$GLOBALS.site_url}/edit-email-templates/alerts/{$listing_type_info.email_alert}/" target="_blank" title="[[Edit Email Template]]" class="edit-email-template"></a>
						{elseif $form_field.id eq 'guest_alert_email' && $listing_type_info.guest_alert_email}
							<a href="{$GLOBALS.site_url}/edit-email-templates/alerts/{$listing_type_info.guest_alert_email}/" target="_blank" title="[[Edit Email Template]]" class="edit-email-template"></a>
						{/if}
					</td>
				</tr>
			{/foreach}
			<tr>
				<td colspan="3" align="right">
					<div class="floatRight">
						<input type="submit" id="apply" value="[[Apply]]" class="greenButton"/>
						<input type="submit" value="[[Save]]" class="greenButton" />
					</div>
				</td>
			</tr>
		</table>
	</form>
</fieldset>

<script type="text/javascript">
	$('#apply').click(
		function(){
			$('#action').attr('value', 'apply_info');
		}
	);
	$('input[name="id"]').attr("disabled","disabled").after('<div style="font-size:11px;margin-top:5px">[[This is a system field. It cannot be changed.]]</div>');
</script>