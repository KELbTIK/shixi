{if $displayMode == 'fieldset'}
	<div class="inputName">[[$captcha.caption]]</div>
	<div class="inputField">{input property='captcha'}</div>
{else}
	<label>[[$captcha.caption]]:<br/>{input property='captcha'}</label>
{/if}
