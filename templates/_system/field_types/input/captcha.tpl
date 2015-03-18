{if $type == 'reCaptcha'}
	{$captchaView}
{elseif $type == 'customCaptcha'}
	<script type="text/javascript">
		function refresh_captcha() {ldelim}
			$.get("{$GLOBALS.site_url}/system/miscellaneous/reloadCustomCaptcha/", function(data){ldelim}
				$("#customCaptcha").html(data);
			{rdelim});
		{rdelim}
	</script>
	<a href="javascript:refresh_captcha();">
		<span class="small">[[Reload Image]]</span>
	</a><br />
	<div id="customCaptcha">{$captchaView}</div><br/>
	<input class="form-control" type="text" name="captcha[input]" />
{else}
	<script type="text/javascript">
		function refresh_captcha() {ldelim}
			document.getElementById('captchaImg').src="{$GLOBALS.site_url}/system/miscellaneous/captcha/?hash=" + Math.round(Math.random() * 1000 + 1000);
		{rdelim}
	</script>

    <div class="row">
        <div class="col-sm-4 col-xs-6 col-md-2">
            <img class="captcha-img" id="captchaImg" src="{$GLOBALS.site_url}/system/miscellaneous/captcha/?hash={php}echo time();{/php}" alt="[[Captcha]]" /><br/>
        </div>
        <div class="col-sm-2 col-xs-6 text-center captcha-refresh">
            <a href="javascript:refresh_captcha();">
                <span class="small visible-xs pull-left">[[Reload Image]]</span>
                <i class="fa fa-refresh"></i>
            </a>
        </div>
        <div class="col-sm-6">
            <input type="text" name="captcha" size="16" class="captcha form-control" />
        </div>
    </div>
{/if}