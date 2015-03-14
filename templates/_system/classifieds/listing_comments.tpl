<div>
	<div class="comment">[[Comments]]:</div>
	<a name="comment_1"></a>
	<div class="comment_holder">
		{foreach from=$comments item=comment name=each_comment}
			<a name="comment_{$comment.id}"></a>
			{include file="listing_comments_item.tpl" listing=$listing iteration=$smarty.foreach.each_comment.iteration}
		{/foreach}
	</div>
 	<div id="prop_form_box" ></div>
	<div id="ProgBar" style="display: none">
		<img style="vertical-align: middle;" src="{$GLOBALS.site_url}/system/ext/jquery/progbar.gif" alt="[[Please wait ...]]" /> [[Please wait ...]]
	</div>
	<br />
	<div id="FormBar" >
		<div class="comment">[[Add your comment]]</div>
		<form method="post" action="" onsubmit="return false">
			<input type="hidden" name="listing_id" id="listing_id" value="{$listing_id}" />
			<input type="hidden" name="total" value="{$comments_total}" id="total" />
			<textarea class="form-control"   name="message" id="message" cols="60" rows="3"></textarea><br/>
			<input type="button" id="but_send" name="send" value="[[Add]]" class="btn btn-success"/>
		</form>
	</div>
</div>
{literal}
	<script type="text/javascript">

	$(document).ready(function(){
		var ajax_url = "{/literal}{$GLOBALS.site_url}/ajax/{literal}";
		var listing_id = "{/literal}{$listing_id}{literal}";
		$("#but_send").click(function(){
			var mess = $.trim($("#message").val());
			if (mess == "")
				alert("{/literal}[[Message empty!]]{literal}");
			else {
				$("#ProgBar").show();
				$.post(ajax_url, {action: "comment", listing: listing_id, message: mess}, function(data) {
					if ($(".comment_item").size() > 0) {
						var lastComponent = $(".comment_item:last");
						lastComponent.next(".error").remove();
						lastComponent.next(".error").remove();
						lastComponent.after(data);
					} else {
						$(".comment_holder").html(data);
					}
					
					if (!$("<div />").html(data).find(".error").length) {
						$("#message").val("");
					}
					
					$("#ProgBar").hide();
				});
			}
		});
	});

	</script>
{/literal}
