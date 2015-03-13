<div id="pmDetails">
	<article class="clearfix blogpost object-non-visible animated object-visible fadeInUpSmall" data-animation-effect="fadeInUpSmall" data-effect-delay="200">
		<div class="blogpost-body">
			<div class="post-info">
				<span class="day">{$message.data|date_format:"%d"}</span>{*{$message.data|date_format:"%H:%M:%S"}*}
				<span class="month">{$message.data|date_format:"%B %Y"}</span>
			</div>
			<div class="blogpost-content">
				<header>
					<h2 class="title">
						{$message.subject}
					</h2>
					<p>
						{$message.data|date_format:$GLOBALS.current_language_data.date_format}
						{$message.data|date_format:"%H:%M:%S"}
					</p>
					<div>
						<i class="fa fa-user pr-5"></i>
						{if $message.outbox == 0}
							{if $message.anonym && $message.anonym == $message.from_id}[[Anonymous User]]
							{elseif $message.from_first_name}{$message.from_first_name} {$message.from_last_name}{else}{$message.from_name}{/if}
						{else}
							<span>to {$message.to_first_name} {$message.to_last_name}</span>
						{/if}
					</div>
				</header>
				<br/>
				<p>{$message.message}</p>
			</div>
		</div>
		<footer class="clearfix">
			<input class="btn btn-danger btn-sm" type="button" id="pm_delete" value="[[Delete]]" />
			<input type="hidden" value="{$GLOBALS.site_url}/private-messages/inbox/read/?id={$message.id}&amp;action=delete" id="pm_delete_link" />
			{if $message.outbox == 0}
				<input class="btn btn-primary btn-sm"type="button" id="pm_reply" value="[[Reply]]" />
				<input type="hidden" value="{$GLOBALS.site_url}/private-messages/reply/?id={$message.id}" id="pm_reply_link" />
			{/if}
		</footer>
	</article>
</div>
<div class="clearfix"></div>
