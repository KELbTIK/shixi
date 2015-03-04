<link type="text/css" href="{$GLOBALS.site_url}/system/ext/jquery/css/jquery-ui.css" rel="stylesheet" />
<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.form.js"></script>
{literal}
<script type="text/javascript">
	function Submit() {
		var options = {
				  target: "#messageBox",
				  url:  $("#notesForm").attr("action")
				}; 
		$("#notesForm").ajaxSubmit(options);
		return false;
	}
	function addNote() {
		document.getElementById('add_notes_block').style.display = 'block';
	}
</script>
{/literal}
{if $error}
	{if $error eq 'LISTING_ID_NOT_SPECIFIED'}
		<p class="error">[[Listing ID not specified]]</p>
	{elseif $error eq 'DENIED_SAVE_LISTING'}
		<p class="error">[[You have no permission to save an ad]]</p>
	{/if}
{else}
	{if !$from_login && !$displayForm}
	    {if $view == 'map'}
            <a href="{$GLOBALS.site_url}/add-notes/?listing_id={$listing.id}" onclick="popUpWindow('{$GLOBALS.site_url}/add-notes/?listing_sid={$listing_sid}&amp;view=map', 500, '[[Add notes]]'); return false;"  class="action">[[Add notes]]</a>&nbsp;&nbsp;
        {else}
            <a href="{$GLOBALS.site_url}/add-notes/?listing_id={$listing_sid}" onclick="SaveAd('formNote_{$listing_sid}', '{$GLOBALS.site_url}/add-notes/?listing_sid={$listing_sid}'); return false;"  class="action">[[Add notes]]</a>&nbsp;&nbsp;
        {/if}
	{else}
		{if $error eq null}
			{if $listing_type == "resume"}
				<p class="message">[[Resume has been saved]]</p>
			{else}
				<p class="message">[[Job has been saved]]</p>
			{/if}
			{if $displayForm}<a href='{$GLOBALS.site_url}/add-notes' onclick='addNote();return false;'>[[Add notes]]</a>
			<div id='add_notes_block' style='display:none;'>
			<form id='notesForm' action='{$GLOBALS.site_url}/add-notes/' onsubmit="return Submit()">
				<input type="hidden" name="actionNew" value='save'/>
				<input type="hidden" name="listing_sid" value='{$listing_sid}'/>
				<textarea name="note" style="width: 100%; margin: 10px 0;"></textarea>
				<br/><input type="submit" value="[[Add]]" class="button" />
			</form>
			</div>
			{/if}
		{elseif $error eq 'LISTING_ID_NOT_SPECIFIED'}
			<p class="error">[[Listing ID not specified]]</p>
		{elseif $error eq 'DENIED_SAVE_LISTING'}
		<p class="error">[[You're not allowed to open this page]]</p>
		{/if}
		{literal}
			<script type="text/javascript">
				var reloadPage = true;
			</script>
		{/literal}
	{/if}
{/if}