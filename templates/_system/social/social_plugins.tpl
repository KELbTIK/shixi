<div class="social_plugins_div">
	<span class="text-center text-muted">
		{if $label eq 'link'}
			[[Social network you want to link your account with]]:
		{elseif $label eq 'login'}
			[[Social network you want to login with]]:
		{else}
			[[Social network you want to login/join with]]:
		{/if}
	</span>
    <div class="clearfix"></div>
    <ul class="social-links circle clearfix {if $GLOBALS.user_page_uri == '/login/'}colored{/if}">
		{foreach from=$social_plugins item="plugin"}
			<li class="{$plugin.id}">
                <a href="{$GLOBALS.site_url}/social/?network={$plugin.id}{if $user_group_id}&amp;user_group_id={$user_group_id}{/if}{if $shoppingCart}&amp;returnToShoppingCart=1{/if}" class="social_login_button slb_{$plugin.id}" title="[[Connect using $plugin.name]]">
                    {if $plugin.id == 'google_plus'}
                        <i class="fa fa-google-plus"></i>
                    {else}
                        <i class="fa fa-{$plugin.id}"></i>
                    {/if}
                </a>
            </li>
		{foreachelse}
			<li>[[Sorry, there are no active plugins]]</li>
		{/foreach}
	</ul>
	<div class="clearfix"></div>
</div>