{assign var="LocationValues" value=$value}
{foreach from=$form_fields item=form_field}
	{assign var="fixInstructionsForComplexField" value=true}
	<tr {if $form_field.hidden}style="display:none;"{/if} id="{$parentID}_{$form_field.id}">
		<td valign="top" width="20%">{tr}{$form_field.caption}{/tr|escape:'html'}</td>
		<td valign="top" class="required">&nbsp;{if $form_field.is_required}*{/if}</td>
		<td class="locationField">{input property=$form_field.id parent=$parentID}</td>
	</tr>
{/foreach}
<script language='JavaScript' type='text/javascript'>
function get{$parentID}States(countrySID) {ldelim}
	{foreach from=$form_fields item=form_field}
		{if $form_field.id == 'State'}
			$.get("{$GLOBALS.site_url}/get-states/", {ldelim} country_sid: countrySID, state_sid: "{$LocationValues.State|escape:'javascript'}", parentID: "{$parentID}", display_as: "{$form_field.display_as}" {rdelim},
				  function(data){ldelim}
						$("#{$parentID}_State .locationField").html(data);
				 {rdelim});
		{/if}
	{/foreach}
{rdelim}

get{$parentID}States("{$LocationValues.Country|escape:'javascript'}");
</script>
{assign var="parentID" value=false scope=global}
{assign var="fixInstructionsForComplexField" value=false}
