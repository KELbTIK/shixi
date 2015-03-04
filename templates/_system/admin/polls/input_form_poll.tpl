{breadcrumbs}
	<a href="{$GLOBALS.site_url}/manage-polls/">[[Manage Polls]]</a>  &#187; {if $action == 'edit'}[[Edit Poll]]{else}[[Add a New Poll]]{/if}
{/breadcrumbs}

<h1><img src="{image}/icons/bargraph32.png" border="0" alt="" class="titleicon"/>{if $action == 'edit'}[[Edit Poll]]{else}[[Add a New Poll]]{/if}</h1>

{if $action == 'edit'}
	<p>
		<a href="{$GLOBALS.site_url}/manage-polls/?action_name=edit_answer&amp;sid={$sid}" class="grayButton">[[Edit Answers]]</a>
		<a href="{$GLOBALS.site_url}/manage-polls/?action_name=view_results&amp;sid={$sid}" onclick="popUpWindow('{$GLOBALS.site_url}/view-results/?sid={$sid}&action_name=view_results', 350, 300, '[[View Results]]'); return false;" class="grayButton">[[View Results]]</a>
	</p>
{/if}

{include file="field_errors.tpl"}

<fieldset>
	<legend>&nbsp;[[Poll Info]]</legend>
	<form method="post" name='pollInputForm'>
		<input type="hidden" name="sid" value="{$sid}" />
		<input type="hidden" name="action_name" value="save" />
		<table>
			{foreach from=$form_fields key=field_name item=form_field}
			<tr>
				<td>[[{$form_field.caption}]]</td>
				<td class="required">{if $form_field.is_required}*{/if}</td>
				<td width="75%">{input property=$form_field.id}{if $form_field.comment}<br/><small>[[{$form_field.comment}]]</small>{/if}</td>
			</tr>
			{/foreach}
			<tr>
				<td colspan="3" >
					<div class="floatRight">
						<input type="submit" name="apply"  class="grayButton" value="[[Apply]]" /> <input type="submit" name="save" class="grayButton" value="[[Save]]" />
					</div>
				</td>
			</tr>
		</table>
	</form>
</fieldset>