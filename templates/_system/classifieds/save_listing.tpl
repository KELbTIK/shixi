<link type="text/css" href="{$GLOBALS.site_url}/system/ext/jquery/css/jquery-ui.css" rel="stylesheet" />
<script  type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.form.js"></script>
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
	<div class="error alert alert-danger">
		{if $error eq 'LISTING_ID_NOT_SPECIFIED'}
			[[Listing ID not specified]]
		{elseif $error eq 'DENIED_SAVE_LISTING'}
			[[You have no permission to save an ad]]
		{/if}
	</div>
{else}
	{if !$from_login && !$displayForm}
	    {if $view == 'map'}
            <a href="{$GLOBALS.site_url}/add-notes/?listing_id={$listing.id}" onclick="popUpWindow('{$GLOBALS.site_url}/add-notes/?listing_sid={$listing_sid}&amp;view=map', 500, '[[Add notes]]'); return false;"  class="action">[[Add notes]]</a>&nbsp;&nbsp;
        {else}
            <a href="{$GLOBALS.site_url}/add-notes/?listing_id={$listing_sid}" onclick="SaveAd('formNote_{$listing_sid}', '{$GLOBALS.site_url}/add-notes/?listing_sid={$listing_sid}'); return false;"  class="action">[[Add notes]]</a>&nbsp;&nbsp;
        {/if}
	{else}
		{if $error eq null}
			<div class="message alert alert-info">
				{if $listing_type == "resume"}
					[[Resume has been saved]]
				{else}
					[[Job has been saved]]
				{/if}
			</div>
			{if $displayForm}<a href='{$GLOBALS.site_url}/add-notes' onclick='addNote();return false;'>[[Add notes]]</a>
			<div id='add_notes_block' style='display:none;'>
			<form id='notesForm' action='{$GLOBALS.site_url}/add-notes/' onsubmit="return Submit()">
				<input type="hidden" name="actionNew" value='save'/>
				<input type="hidden" name="listing_sid" value='{$listing_sid}'/>
				<textarea "form-control"  name="note" style="width: 100%; margin: 10px 0;"></textarea>
				<br/><input type="submit" value="[[Add]]" class="btn btn-success" />
			</form>
			</div>
			{/if}
		{elseif $error eq 'LISTING_ID_NOT_SPECIFIED'}
			<div class="error alert alert-danger">[[Listing ID not specified]]</div>
		{elseif $error eq 'DENIED_SAVE_LISTING'}
		<div class="error alert alert-danger"> [[You're not allowed to open this page]]</div>
		{/if}
		{literal}
			<script type="text/javascript">
				var reloadPage = true;
			</script>
		{/literal}
	{/if}
{/if}