{if $isDemo}{include file="../menu/demo_theme_switcher.tpl"}{/if}
<div class="MainDiv">
	<div class="headerPage">
		<a href="{$GLOBALS.site_url}" class="logo"><img src="{image}logo.png" alt="[[{$GLOBALS.settings.logoAlternativeText}]]" title="[[{$GLOBALS.settings.logoAlternativeText}]]" /></a>
		<div class="headerUserMenu">
			{if $GLOBALS.current_user.logged_in}
				[[Welcome]] <span class="longtext-90">{if $GLOBALS.current_user.subuser}{$GLOBALS.current_user.subuser.username}{else}{$GLOBALS.current_user.username}{/if}</span>, &nbsp;
				<a href="{$GLOBALS.site_url}/"> [[Home]]</a> | 
				<a href="{$GLOBALS.site_url}/logout/"> [[Logout]]</a>
				{if $GLOBALS.current_user.new_messages > 0} 
					{if $acl->isAllowed('use_private_messages')}
						<a href="{$GLOBALS.site_url}/private-messages/inbox/"><img src="{image}new_msg.gif" border="0"  alt="[[You have]] {$GLOBALS.current_user.new_messages} [[message]]"  title="[[You have]] {$GLOBALS.current_user.new_messages} [[message]]" /></a>
					{elseif $acl->getPermissionParams('use_private_messages') == "message"}
						<a href="{$GLOBALS.site_url}/private-messages/inbox/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 300, '[[You have]] {$GLOBALS.current_user.new_messages} [[message]]'); return false;"><img src="{image}new_msg.gif" border="0"  alt="[[You have]] {$GLOBALS.current_user.new_messages} [[message]]"  title="[[You have]] {$GLOBALS.current_user.new_messages} [[message]]" /></a>
					{/if}
				{/if}
			{else}
				<a href="{$GLOBALS.site_url}/">[[Home]]</a> | 
				<a href="{$GLOBALS.site_url}/registration/">[[Register]]</a> | 
				<a href="{$GLOBALS.site_url}/login/">[[Sign In]]</a><br/>
				{* SOCIAL PLUGIN: LOGIN BUTTON *}
				{module name="social" function="social_login"}
				{* / SOCIAL PLUGIN: LOGIN BUTTON *}
			{/if}
            <br/>{module name="payment" function="show_shopping_cart"}
		</div> 
	</div>
	<div class="clr"></div>
    <div id="underHeader">
	    {module name="menu" function="top_menu"}
        <div id="chooseLanguage">
            <form id="langSwitcherForm" method="get" action="">
                <select name="lang" onchange="location.href='{$GLOBALS.site_url}{$url}?lang='+this.value+'&amp;{$params}'">
                {foreach from=$GLOBALS.languages item=language}
                    <option value="{$language.id}"{if $language.id == $GLOBALS.current_language} selected="selected"{/if}>{$language.caption}</option>
                {/foreach}
                </select>
            </form>
        </div>
    </div>
    <div class="clr"></div>