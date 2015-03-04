{literal}
<style>
.rating {
	float: left;
	width: 80px;
	height: 15px;
	background: url({/literal}{$GLOBALS.site_url}{literal}/system/lib/rating/n_stars_2.gif) repeat-x;
}

.rating_over {
	float: left;
	width: 0px;
	height: 15px;
	background: url({/literal}{$GLOBALS.site_url}{literal}/system/lib/rating/n_stars_1.gif) repeat-x;
}

.rating_text {
	float: left;
	padding-left: 5px;
}
</style>

&nbsp;&nbsp;<div class="rating" id="rating" title="{/literal}{if $listing.rating_array.title eq 1}[[Please, Vote!]]
{elseif $listing.rating_array.title eq 2}[[You've already voted]] 
{elseif $listing.rating_array.title eq 3}[[Please sign in to vote]]{/if}{literal}"><div class="rating_over" id="rating_over"></div></div>
<div class="rating_text">{/literal}(<span id="rating_rate">{$listing.rating_array.rating}</span> of 5){literal}</div>
<script type="text/javascript">
	$(document).ready(function() {
		var def_rate = {/literal}{$listing.rating_array.rating}{literal};
		var def_sdv = 0; var star_h = 16;
		var pos = $("#rating").offset();
		var ww = 0; var rate = 0; var sdv = 0;
		var listing = {/literal}{$listing.id}{literal};
		var vote_phrase = "{/literal}[[You've already voted]]{literal}";
		var url_to_ajax = "{/literal}{$GLOBALS.site_url}/ajax/{literal}";
		var rating_num = {/literal}{$listing.rating_num}{literal};
		if (def_rate > 0) {
			def_sdv = def_rate * star_h;
			$(".rating_over").width(def_sdv);
		}
		{/literal}
		{if $listing.rating_array.title eq 1}{literal}
			$("#rating").bind("mousemove", function (e) {
				var m_pos_left = e.pageX - pos.left;
				rate = Math.ceil(m_pos_left / star_h);
				sdv = rate * star_h;
				if (rate != ww) {
					$("#rating_over").css("width", sdv);
					ww = rate;
				}
			});

			$("#rating").bind("mouseleave", function () {
				$(".rating_over").width(def_sdv);
				ww = 0;
				rate = 0;
			});

			$("#rating").click(function () {
				$("#rating").unbind();
				$.post(url_to_ajax, {action:"rate", listing:listing, rate:rate}, function (data) {
					if (!isNaN(data) && data > 0) {
						rate = data;
						def_sdv = rate * star_h;
						$("#rating_over").width(def_sdv);
					}
				});
				$("#rating_rate").html(rate);
				$("#rating").attr("title", vote_phrase);
			});{/literal}
		{/if}
	{literal}
	});
</script>
{/literal} 
