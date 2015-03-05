{if $isDemo}{include file="../menu/demo_theme_switcher.tpl"}{/if}
<div class="MainDiv">

	<div class="header-top">
		<div class="container">
			<div class="row">
				<div class="col-xs-2 col-sm-6">

					<!-- header-top-first start -->
					<!-- ================ -->
					<div class="header-top-first clearfix">
						<ul class="social-links clearfix hidden-xs">
							<li class="twitter"><a target="_blank" href="http://www.twitter.com"><i class="fa fa-twitter"></i></a></li>
							<li class="skype"><a target="_blank" href="http://www.skype.com"><i class="fa fa-skype"></i></a></li>
							<li class="linkedin"><a target="_blank" href="http://www.linkedin.com"><i class="fa fa-linkedin"></i></a></li>
							<li class="googleplus"><a target="_blank" href="http://plus.google.com"><i class="fa fa-google-plus"></i></a></li>
							<li class="youtube"><a target="_blank" href="http://www.youtube.com"><i class="fa fa-youtube-play"></i></a></li>
							<li class="flickr"><a target="_blank" href="http://www.flickr.com"><i class="fa fa-flickr"></i></a></li>
							<li class="facebook"><a target="_blank" href="http://www.facebook.com"><i class="fa fa-facebook"></i></a></li>
							<li class="pinterest"><a target="_blank" href="http://www.pinterest.com"><i class="fa fa-pinterest"></i></a></li>
						</ul>
						<div class="social-links hidden-lg hidden-md hidden-sm">
							<div class="btn-group dropdown">
								<button type="button" class="btn dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-alt"></i></button>
								<ul class="dropdown-menu dropdown-animation">
									<li class="twitter"><a target="_blank" href="http://www.twitter.com"><i class="fa fa-twitter"></i></a></li>
									<li class="skype"><a target="_blank" href="http://www.skype.com"><i class="fa fa-skype"></i></a></li>
									<li class="linkedin"><a target="_blank" href="http://www.linkedin.com"><i class="fa fa-linkedin"></i></a></li>
									<li class="googleplus"><a target="_blank" href="http://plus.google.com"><i class="fa fa-google-plus"></i></a></li>
									<li class="youtube"><a target="_blank" href="http://www.youtube.com"><i class="fa fa-youtube-play"></i></a></li>
									<li class="flickr"><a target="_blank" href="http://www.flickr.com"><i class="fa fa-flickr"></i></a></li>
									<li class="facebook"><a target="_blank" href="http://www.facebook.com"><i class="fa fa-facebook"></i></a></li>
									<li class="pinterest"><a target="_blank" href="http://www.pinterest.com"><i class="fa fa-pinterest"></i></a></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-10 col-sm-6">

					<div id="header-top-second" class="pull-right language-select">
						<form class="pull-left" id="langSwitcherForm" method="get" action="">
							<select name="lang" onchange="location.href='{$GLOBALS.site_url}{$url}?lang='+this.value+'&amp;{$params}'" style="width: 200px;">
								{foreach from=$GLOBALS.languages item=language}
									<option value="{$language.id}"{if $language.id == $GLOBALS.current_language} selected="selected"{/if}>{$language.caption}</option>
								{/foreach}
							</select>
						</form>
						{module name="payment" function="show_shopping_cart"}
					</div>
					<div class="header-top-dropdown">
						<div class="btn-group dropdown">
							{if $GLOBALS.current_user.logged_in}
								[[Welcome]] <span class="longtext-50">{if $GLOBALS.current_user.subuser}{$GLOBALS.current_user.subuser.username}{else}{$GLOBALS.current_user.username}{/if}</span>,
								{if $GLOBALS.current_user.new_messages > 0}
									{if $acl->isAllowed('use_private_messages')}
										<a class="btn" href="{$GLOBALS.site_url}/private-messages/inbox/"><img src="{image}new_msg.gif" border="0" alt="[[You have]] {$GLOBALS.current_user.new_messages} [[message]]"  title="[[You have]] {$GLOBALS.current_user.new_messages} [[message]]" /></a>
									{elseif $acl->getPermissionParams('use_private_messages') == "message"}
										<a class="btn" href="{$GLOBALS.site_url}/private-messages/inbox/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 300, '[[You have]] {$GLOBALS.current_user.new_messages} [[message]]'); return false;" ><img src="{image}new_msg.gif" border="0" alt="[[You have]] {$GLOBALS.current_user.new_messages} [[message]]"  title="[[You have]] {$GLOBALS.current_user.new_messages} [[message]]" /></a>
									{/if}
								{/if}
								<a class="btn" href="{$GLOBALS.site_url}/logout/"> [[Logout]]</a>
							{else}
								<a class="btn" href="{$GLOBALS.site_url}/registration/"> [[Register]]</a>
								<a class="btn" href="{$GLOBALS.site_url}/login/"><i class="fa fa-user"></i> [[Sign In]]</a>
							{/if}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>




	{module name="menu" function="top_menu"}
	{*SOCIAL PLUGIN: LOGIN BUTTON*}
	{*{module name="social" function="social_login"}*}
	{*/ SOCIAL PLUGIN: LOGIN BUTTON*}