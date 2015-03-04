{if $errors}
	{include file="../errors/errors.tpl"}
{else}
	<script type="text/javascript">
		function share(url) {
			if (url) {
				var height = 400;
				var width  = 640;
				var top    = (screen.height / 2) - (height / 2);
				var left   = (screen.width / 2) - (width / 2);
				window.open(url, "name", "height = " + height + ", width = " + width + ", top = " + top + ", left = " + left);
			}
		}
	</script>
	
	{foreach from=$buttons key=network item=button}
		{include file=$network|cat:".tpl"}
		<div id="{$network}-posted" style="display:none;">[[Posted]]</div><br />
	{/foreach}
{/if}