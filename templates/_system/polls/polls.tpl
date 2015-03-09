{if $form_fields}
<h2>Poll</h2>
	<div id="poll">

		{if $isVoted == 0}
			<form method="post" action="">
				<input type="hidden" name="action" value="save"/>
				<input type="hidden" name="sid" value="{$sid}"/>
				{foreach from=$form_fields item=form_field}
					<div class="strong">[[$form_field.caption]]</div>
                    <div class="clearfix"></div>
					{input property=$form_field.id template='radiobuttons.tpl'}
				{/foreach}
				<div class="clearfix"></div>
				<a id="pollButton" href="#" class="btn btn-default btn-sm">Vote</a>

			</form>
		{else}
			{foreach from=$form_fields item=form_field}
				<div class="text-center strong"">[[$form_field.caption]]</div>
                <div class="clearfix"></div>
			{/foreach}
			<p class="message">[[You've already voted]]</p>
		{/if}

		{if $display_results} &nbsp; <a href="{$GLOBALS.site_url}/poll-results/{$sid}/{$question|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html">[[View Results]]</a>{/if}
        <div class="clearfix"></div>
	</div>
{/if}