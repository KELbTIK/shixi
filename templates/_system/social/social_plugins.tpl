<div class="social_plugins_div">
	<span class="login_buttons_txt">
		{if $label eq 'link'}
			[[Social network you want to link your account with]]:
		{elseif $label eq 'login'}
			[[Social network you want to login with]]:
		{else}
			[[Social network you want to login/join with]]:
		{/if}
	</span>
	<span class="social-buttons">
		{foreach from=$social_plugins item="plugin"}
			<a href="{$GLOBALS.site_url}/social/?network={$plugin.id}{if $user_group_id}&amp;user_group_id={$user_group_id}{/if}{if $shoppingCart}&amp;returnToShoppingCart=1{/if}" class="social_login_button slb_{$plugin.id}" title="[[Connect using $plugin.name]]"></a>
		{foreachelse}
			[[Sorry, there are no active plugins]]
		{/foreach}
	</span>
	<div class="clr"></div>
</div>