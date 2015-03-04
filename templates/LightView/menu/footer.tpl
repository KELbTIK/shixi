	<div class="clr"><br/></div>
	<div id="footer">2008-{$smarty.now|date_format:"%Y"} [[Powered by]] <a target="_blank" href="http://www.smartjobboard.com" title="Job Board Software, Script">SmartJobBoard Job Board Software</a> &nbsp; &nbsp; <a href="{$GLOBALS.site_url}/about/">[[About Us]]</a> &nbsp;  <a href="{$GLOBALS.site_url}/site-map/">[[Sitemap]]</a>
		{if $GLOBALS.settings.cookieLaw}
			&nbsp;  <a href="#" onClick="return cookiePreferencesPopupOpen();">[[Cookie Preferences]]</a>
		{/if}
		{if isset($GLOBALS.mobileUrl)}
			&nbsp;  <a href="{$GLOBALS.mobileUrl}{if $GLOBALS.SessionId}?authId={$GLOBALS.SessionId}{/if}">[[Mobile Version]]</a>
		{/if}
	</div>
</div>