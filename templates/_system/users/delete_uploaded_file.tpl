{foreach from=$errors item=message key=error}

{if $error eq 'PARAMETERS_MISSED'}

<div class="error alert alert-danger">[[The key parameters are not specified]]</div>

{elseif $error eq 'WRONG_PARAMETERS_SPECIFIED'}

<<div class="error alert alert-danger">[[Wrong parameters specified]]</div>

{/if}

{foreachelse}

<div class="message alert alert-success">[[File deleted successfully]]</div>
<a href="{$GLOBALS.site_url}/edit-profile/">[[Back to edit profile]]</a>

{/foreach}