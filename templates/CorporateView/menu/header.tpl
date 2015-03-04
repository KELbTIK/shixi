{if $isDemo}{include file="../menu/demo_theme_switcher.tpl"}{/if}
<div id="maindiv">
	<div id="header-border"></div>
	<div id="innerdiv">
		{module name="menu" function="top_menu"}
		<div class="clr"><br/></div>
		<div id="header-left"><a href="{$GLOBALS.site_url}"><img src="{image}logo.png" border="0" alt="[[{$GLOBALS.settings.logoAlternativeText}]]" title="[[{$GLOBALS.settings.logoAlternativeText}]]" /></a></div>
		<div id="header-right">
			<form id="langSwitcherForm" method="get" action="">
				<select name="lang" onchange="location.href='{$GLOBALS.site_url}{$url}?lang='+this.value+'&amp;{$params}'">
					{foreach from=$GLOBALS.languages item=language}
						<option value="{$language.id}"{if $language.id == $GLOBALS.current_language} selected="selected"{/if}>{$language.caption}</option>
					{/foreach}
				</select>
			</form>
			<br/>
			{if $GLOBALS.current_user.logged_in}
				<i>[[Welcome]] <span class="longtext-50">{if $GLOBALS.current_user.subuser}{$GLOBALS.current_user.subuser.username}{else}{$GLOBALS.current_user.username}{/if}</span></i>
				{if $GLOBALS.current_user.new_messages > 0}
					{if $acl->isAllowed('use_private_messages')}
						<a href="{$GLOBALS.site_url}/private-messages/inbox/"><img src="{image}new_msg.gif" border="0"  alt="[[You have]] {$GLOBALS.current_user.new_messages} [[message]]"  title="[[You have]] {$GLOBALS.current_user.new_messages} [[message]]" /></a>
					{elseif $acl->getPermissionParams('use_private_messages') == "message"}
						<a href="{$GLOBALS.site_url}/private-messages/inbox/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 300, '[[You have]] {$GLOBALS.current_user.new_messages} [[message]]'); return false;" ><img src="{image}new_msg.gif" border="0"  alt="[[You have]] {$GLOBALS.current_user.new_messages} [[message]]"  title="[[You have]] {$GLOBALS.current_user.new_messages} [[message]]" /></a>
					{/if}
				{/if}
				{if $GLOBALS.current_user.logged_in}
					| <a href="{$GLOBALS.site_url}/logout/"> [[Logout]]</a>
				{/if}
			{/if}
			{if $GLOBALS.current_user.logged_in != true}
				<a href="{$GLOBALS.site_url}/registration/">[[Register]]</a> <img src="{image}menuSep.png" border="0" alt=""/> <a href="{$GLOBALS.site_url}/login/">[[Sign In]]</a>
				<div class="clr"></div>
				{module name="social" function="social_login"}
			{/if}
			<div class="clr"></div>
			{module name="payment" function="show_shopping_cart"}
		</div>
		<div class="clr"><br/></div>
