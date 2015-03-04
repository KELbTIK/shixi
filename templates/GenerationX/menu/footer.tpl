	<div class="clr"><br/></div>
	<div class="Footer">
		<div id="footer-left"></div>
		&nbsp; &copy; 2008-{$smarty.now|date_format:"%Y"} [[Powered by]] <a target="_blank" href="http://www.smartjobboard.com" title="Job Board Software, Script" style="color:white;">SmartJobBoard Job Board Software</a> &nbsp; &nbsp; <a href="{$GLOBALS.site_url}/about/"style="color:white;">[[About Us]]</a> &nbsp;  <a href="{$GLOBALS.site_url}/site-map/"style="color:white;">[[Sitemap]]</a>
		{if isset($GLOBALS.mobileUrl)}
			&nbsp;  <a href="{$GLOBALS.mobileUrl}{if $GLOBALS.SessionId}?authId={$GLOBALS.SessionId}{/if}" style="color:white;">[[Mobile Version]]</a>
		{/if}
		{if $GLOBALS.settings.cookieLaw}
			&nbsp;  <a href="#" onClick="return cookiePreferencesPopupOpen();" style="color:white;">[[Cookie Preferences]]</a>
		{/if}
		<div id="footer-right"></div>
	</div>
	<div class="clr"><br/></div>
</div>