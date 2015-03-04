{if $form_fields}
	<div id="poll">
		<h2 class="pollTitle">[[Poll]]</h2>
		<div class="clr"></div>
		{if $isVoted == 0}
			<form method="post" action="">
				<input type="hidden" name="action" value="save"/>
				<input type="hidden" name="sid" value="{$sid}"/>
				{foreach from=$form_fields item=form_field}
					<div class="text-center strong">[[$form_field.caption]]</div>
					<div class="clr"></div>
					{input property=$form_field.id template='radiobuttons.tpl'}
				{/foreach}
				<div class="clr"><br/></div>
				<input id="pollButton" type="submit" name="vote" value="[[Vote]]"/>
			</form>
		{else}
			{foreach from=$form_fields item=form_field}
				<div class="text-center strong"">[[$form_field.caption]]</div>
				<div class="clr"></div>
			{/foreach}
			<p class="message">[[You've already voted]]</p>
		{/if}

		{if $display_results}<br/>&nbsp; <a href="{$GLOBALS.site_url}/poll-results/{$sid}/{$question|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html">[[View Results]]</a>{/if}
		<div class="clr"><br/></div>
	</div>
{/if}