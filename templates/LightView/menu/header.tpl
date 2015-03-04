{if $isDemo}{include file="../menu/demo_theme_switcher.tpl"}{/if}
<div class="MainDiv">
	<div id="header">
		<div id="logo">
			<a href="{$GLOBALS.site_url}/"><img src="{image}logo.png" border="0" alt="[[{$GLOBALS.settings.logoAlternativeText}]]" title="[[{$GLOBALS.settings.logoAlternativeText}]]" /></a>
		</div>
		<div id="header-right">
			{if $GLOBALS.current_user.logged_in}
				<div class="welcome">
					<p>[[Welcome]] <span class="longtext-65">{if $GLOBALS.current_user.subuser}{$GLOBALS.current_user.subuser.username}{else}{$GLOBALS.current_user.username}{/if}</span>
					{if $GLOBALS.current_user.new_messages > 0}
						{if $acl->isAllowed('use_private_messages')}
							<a href="{$GLOBALS.site_url}/private-messages/inbox/"><img src="{image}new_msg.gif" border="0"  alt="[[You have]] {$GLOBALS.current_user.new_messages} [[message]]"  title="[[You have]] {$GLOBALS.current_user.new_messages} [[message]]" /></a>
						{elseif $acl->getPermissionParams('use_private_messages') == "message"}
							<a href="{$GLOBALS.site_url}/private-messages/inbox/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 300, '[[You have]] {$GLOBALS.current_user.new_messages} [[message]]'); return false;" ><img src="{image}new_msg.gif" border="0"  alt="[[You have]] {$GLOBALS.current_user.new_messages} [[message]]"  title="[[You have]] {$GLOBALS.current_user.new_messages} [[message]]" /></a>
						{/if}
					{/if}
					</p>
				</div>
			{/if}
			<div id="language">
				<form id="langSwitcherForm" method="get" action="">
					<select name="lang" onchange="location.href='{$GLOBALS.site_url}{$url}?lang='+this.value+'&amp;{$params}'">
						{foreach from=$GLOBALS.languages item=language}
							<option value="{$language.id}"{if $language.id == $GLOBALS.current_language} selected="selected"{/if}>{$language.caption}</option>
						{/foreach}
					</select>
				</form>
			</div>
			<div id="user-menu">
				{if $GLOBALS.current_user.logged_in}
					<a href="{$GLOBALS.site_url}/" style="color: white"> [[Home]]</a> |
					<a href="{$GLOBALS.site_url}/logout/" style="color: white"> [[Logout]]</a>
				{else}
					{* SOCIAL PLUGIN: LOGIN BUTTON *}
					{module name="social" function="social_login"}
					{* / SOCIAL PLUGIN: LOGIN BUTTON *}
					<a href="{$GLOBALS.site_url}/" style="color: white"> [[Home]]</a> |
					<a href="{$GLOBALS.site_url}/registration/" style="color: white"> [[Register]]</a> |
					<a href="{$GLOBALS.site_url}/login/" style="color: white"> [[Sign In]]</a>
				{/if}
			</div>
			<div class="clr"></div>
			{module name="menu" function="top_menu"}
		</div>
	</div>
	{module name="payment" function="show_shopping_cart"}
	<div class="clr"><br/></div>