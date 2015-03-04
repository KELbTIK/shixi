{literal}
<script type="text/javascript">
	function Submit(id) {
		var options = {
				  target: "#formNote_{/literal}{$listing_sid}{literal}",
				  url:  $("#notesForm_" + id).attr("action")
				};
		$("#notesForm_" + id).ajaxSubmit(options);
		return false;
	}

    function editNote(id)
    {
        var note = $("textarea[name=note]").val();
        var data = '';
        if (note) {
            data = '<a href="{/literal}{$GLOBALS.site_url}/edit-notes/?listing_id={$listing_sid}" onclick="popUpWindow(\'{$GLOBALS.site_url}/edit-notes/?listing_sid={$listing_sid}&amp;view=map\', 500, \'[[Edit notes]]\'); return false;"  class="action">[[Edit notes]]{literal}</a>&nbsp;&nbsp;';
        } else {
            data = '<a href="{/literal}{$GLOBALS.site_url}/add-notes/?listing_id={$listing_sid}" onclick="popUpWindow(\'{$GLOBALS.site_url}/add-notes/?listing_sid={$listing_sid}&amp;view=map\', 500, \'[[Add notes]]\'); return false;"  class="action">[[Add notes]]{literal}</a>&nbsp;&nbsp;';
        }
        $('#notes_'+id).html(data);
    }
</script>
{/literal}
{if !$errors}
	{if $action == 'save'}
		{if $noteSaved}
			<script type="text/javascript"> window.location.reload();</script>
		{/if}
	{elseif $action == 'close'}
		{if $saved_listing.note && $saved_listing.note != ''}
			<span style="color: rgb(120, 120, 120);"><b>[[My notes]]:</b> {$saved_listing.note}</span>
		{elseif $saved_listing.note == ''}
			<script type="text/javascript">
				$("#trNote_" + {$apps_id}).removeClass("table-application-border-bottom");
				$("#tdNote_" + {$apps_id}).removeClass("ApplicationPointedInListingInfo");
				$("#tdCheckbox_" + {$apps_id}).attr("rowspan", "1");
			</script>
		{/if}
	{elseif $action == 'edit'}
	<div style='font-weight: bold;'>[[My notes]]:</div>
	<form id="notesForm_{$apps_id}" action="{$GLOBALS.site_url}/edit-notes/" onsubmit="return Submit('{$apps_id}')">
		<input type="hidden" name="actionNew" value='save'/>
		<input type="hidden" name="page" value='{$page}'/>
		<input type="hidden" name="apps_id" value='{$apps_id}'/>
		<input type="hidden" id='close' name="close" value=''/>
		<input type="hidden" name="listing_sid" value='{$listing_sid}'/>
		<textarea style='width:100%;' cols=3 name='note'>{$saved_listing.note}</textarea><br/>
		<input type="submit" value="[[Save]]" class="button" {if $view == 'map'}onclick="$('#messageBox').dialog('close'); editNote({$listing_sid});"{/if}/>
		<input type="submit" value="[[Close]]" class="button" {if $view == 'map'}onclick="$('#messageBox').dialog('close');"{else}onclick='$("#close").val("close")'{/if}/>
	</form>
	{/if}
{else}
	{foreach from=$errors key=error_code item=error_message}
			<p class="error">
				{if $error_code == 'UNDEFINED_LISTING_ID'}
					[[Listing ID is not defined]]
				{elseif $error_code == 'UNDEFINED_APPS_ID'}
					[[Application ID is not defined]]
				{else}
					[[{$error_message}]]
				{/if}
			</p>
	{/foreach}
{/if}