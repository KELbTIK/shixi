{breadcrumbs}<a href="{$GLOBALS.site_url}/users/?restore=1">[[Users]]</a> &#187; [[Edit User Info]]{/breadcrumbs}
<h1>  <img src="{image}/icons/linedpaperminus32.png" border="0" alt="" class="titleicon"/>[[Edit User Info]]</h1>
{foreach from=$errors item=message key=error}
	{if $error eq 'PARAMETERS_MISSED'}
		<p class="error">[[The key parameters are not specified]]</p>
	{elseif $error eq 'WRONG_PARAMETERS_SPECIFIED'}
		<p class="error">[[Wrong parameters specified]]</p>
	{/if}{foreachelse}
	<p>[[File deleted successfully]]</p>
	<a href="{$GLOBALS.site_url}/edit-user/?user_sid={$user_sid}">[[Back to edit profile]]</a>
{/foreach}