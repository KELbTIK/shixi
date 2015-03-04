{if $isDemo}{include file="../menu/demo_theme_switcher.tpl"}{/if}
{if $GLOBALS.user_page_uri != '/' && $GLOBALS.user_page_uri != '/employers/'}
	<div id="header-bg" class="index-header-bg">
		<div id="header-bg-in" class="index-header-bg-in"></div>
	</div>
{/if}
{if !$GLOBALS.current_user.logged_in}
	{if $GLOBALS.user_page_uri == "/employers/"}
		<div id="employers" class="jobseeker-button"><a href="{$GLOBALS.site_url}/">[[Job Seekers]]</a></div>
	{else}
		<div id="employers"><a href="{$GLOBALS.site_url}/employers/">[[Employers]]</a></div>
	{/if}
{else}
	{if $GLOBALS.current_user.group.id == "Employer"}
		<div id="employers"><a href="{$GLOBALS.site_url}/employers/">[[Employers]]</a></div>
	{/if}
{/if}
<div class="main-div">
	<div id="header">
		<div id="header-left">
			<a href="{if $GLOBALS.current_user.group.id == "Employer"}{$GLOBALS.site_url}/employers/{else}{$GLOBALS.site_url}/{/if}"><img src="{image}logo.png" border="0" alt="[[{$GLOBALS.settings.logoAlternativeText}]]" title="[[{$GLOBALS.settings.logoAlternativeText}]]" /></a>
			<div class="clr"><br/></div>
			{module name="menu" function="top_menu"}
		</div>
		<div id="header-right">
			<form id="langSwitcherForm" method="get" action="">
				<select name="lang" onchange="location.href='{$GLOBALS.site_url}{$url}?lang='+this.value+'&amp;{$params}'" class="language-switcher">
					{foreach from=$GLOBALS.languages item=language}
						<option value="{$language.id}"{if $language.id == $GLOBALS.current_language} selected="selected"{/if}>{$language.caption}</option>
					{/foreach}
				</select>
			</form>
			<div class="clr"><br/></div>
			{if $GLOBALS.current_user.logged_in}
				<div class="header-page">
					<div class="header-user-menu">
							<span>[[Welcome]] <span class="longtext-60">{if $GLOBALS.current_user.subuser}{$GLOBALS.current_user.subuser.username}{else}{$GLOBALS.current_user.username}{/if}</span> &nbsp;
								{if $GLOBALS.current_user.new_messages > 0}
									{if $acl->isAllowed('use_private_messages')}
										<a href="{$GLOBALS.site_url}/private-messages/inbox/"><img src="{image}new_msg.gif" border="0"  alt="[[You have]] {$GLOBALS.current_user.new_messages} [[message]]"  title="[[You have]] {$GLOBALS.current_user.new_messages} [[message]]" /></a>
								{elseif $acl->getPermissionParams('use_private_messages') == "message"}
									<a href="{$GLOBALS.site_url}/private-messages/inbox/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 300, '[[You have]] {$GLOBALS.current_user.new_messages} [[message]]'); return false;"><img src="{image}new_msg.gif" border="0"  alt="[[You have]] {$GLOBALS.current_user.new_messages} [[message]]"  title="[[You have]] {$GLOBALS.current_user.new_messages} [[message]]" /></a>
									{/if}
								{/if}
								&nbsp;&nbsp;|&nbsp;&nbsp;
							<a href="{$GLOBALS.site_url}/logout/"> [[Logout]]</a>
							</span>
						<br/>{module name="payment" function="show_shopping_cart"}
					</div>
				</div>
				<div class="clr"></div>

			{else}
				{module name="users" function="login" template="login_top.tpl" internal="true"}
			{/if}
		</div>
	</div>
	<div class="clr"></div>
</div>

<script type="text/javascript">
	$(".language-switcher").selectbox({
		change: function (value) {
			location.href='{$GLOBALS.site_url}{$url}?lang='+value+'&{$params}';
		}
	});
</script>