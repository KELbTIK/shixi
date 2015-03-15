<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Shixi.com [[Admin Panel]]</title>
	<link rel="StyleSheet" type="text/css" href="{image src="auth.css"}"/>
	{if $GLOBALS.current_language_data.rightToLeft}<link rel="StyleSheet" type="text/css" href="{image src="designRight.css"}" />{/if}
	<script language="JavaScript" type="text/javascript" src="{$GLOBALS.user_site_url}/system/ext/jquery/jquery.js"></script>
{literal}
	<script type="text/javascript">$(function () {
		$('input[name=username]').focus();
	})</script>{/literal}
</head>
<body>
	<div id="loginForm">
		<div id="headerLogo">
			<img src="{image}authLogo.png" border="0" width="199" height="30"/><br/>
			
		</div>
		<div class="clr"></div>
		<div id="authFormLogin">
			<form method="post" action="">
				{$form_hidden_params}
				{if $ERROR}
					{foreach from=$ERROR item=error key=errorCode}
						{if $errorCode == "NOT_VALID"}
							<fieldset id="errorAuth">[[Security code is not valid]]</fieldset>
						{elseif $errorCode == "EMPTY_VALUE"}
							<fieldset id="errorAuth">[[Enter Security code]]</fieldset>
						{elseif $errorCode == "LOGIN_PASS_NOT_CORRECT"}
							<fieldset id="errorAuth">[[The username or password you entered is incorrect]]</fieldset>
						{/if}
					{/foreach}
				{/if}
				<label>[[Username]]:<br/><input type="text" name="username" {if $isDemo}value="admin"{/if} /></label>
				<label>[[Password]]:<br/><input type="password" name="password"  {if $isDemo}value="admin"{/if} /></label>
				{module name="miscellaneous" function="captcha_handle"}

				<input type="submit" value="[[Login]]" id="loginButton"/>
			</form>

			<form method="get" action="">
				<select id="languages" name="lang" onchange="location.href='{$GLOBALS.admin_site_url}{$url}?lang='+this.value+'&amp;{$params}'">
				{foreach from=$GLOBALS.languages item=language}
					<option value="{$language.id}"{if $language.id == $GLOBALS.current_language} selected="selected"{/if}>{$language.caption}</option>
				{/foreach}
				</select>
			</form>
		</div>
	</div>
	<div class="clr"></div>
	<div id="copyright">[[Copyright]] 2015 &copy; Shixi.com [[All rights reserved]]</div>
</body>
</html>