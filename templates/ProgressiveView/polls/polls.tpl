{if $form_fields}
	<div id="poll">
		{if $isVoted == 0}
			<form method="post" action="">
				<input type="hidden" name="action" value="save"/>
				<input type="hidden" name="sid" value="{$sid}"/>
				{foreach from=$form_fields item=form_field}
					<div class="strong">[[$form_field.caption]]</div>
					<div class="clr"></div>
					<div class="polls-variable">{input property=$form_field.id template='radiobuttons.tpl'}</div>
				{/foreach}
				<input id="pollButton" type="submit" name="vote" value="[[Vote]]"/>
			</form>
		{else}
			{foreach from=$form_fields item=form_field}
				<div class="strong">[[$form_field.caption]]</div>
				<div class="clr"></div>
			{/foreach}
			<p class="message">[[You've already voted]]</p>
		{/if}
	</div>
	{if $display_results}
		<div class="clr"><br/></div>
		<div class="view-all">
			<a href="{$GLOBALS.site_url}/poll-results/{$sid}/{$question|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html">[[View Results]]</a>
		</div>
	{/if}
{/if}