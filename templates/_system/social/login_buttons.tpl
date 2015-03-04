<div class="social_plugins_div">
	<span class="login_buttons_txt">[[Connect with social network]]:</span>
	{foreach from=$aSocPlugins item="plugin"}
	<a href="{$GLOBALS.site_url}/social/?network={$plugin.id}{if $user_group_id}&amp;user_group_id={$user_group_id}{/if}" class="social_login_button slb_{$plugin.id}" title="[[Connect using $plugin.name]]"></a>
	{/foreach}
	<div style="clear:both;"></div>
</div>
