&nbsp;&nbsp;<div class="rating" id="rating" title="{if $listing.rating_array.title eq 1}[[Please, Vote!]]
{elseif $listing.rating_array.title eq 2}[[You've already voted]] 
{elseif $listing.rating_array.title eq 3}[[Please sign in to vote]]{/if}"><div class="rating_over" id="rating_over"></div></div>
<div class="rating_text">(<span id="rating_rate">{$listing.rating_array.rating}</span> of 5)</div>
<script type="text/javascript">
	$(document).ready(function() {
		var def_rate = {$listing.rating_array.rating};
		var def_sdv = 0; var star_h = 16;
		var pos = $("#rating").offset();
		var ww = 0; var rate = 0; var sdv = 0;
		var listing = {$listing.id};
		var vote_phrase = "[[You've already voted]]";
		var url_to_ajax = "{$GLOBALS.site_url}/ajax/";
		var rating_num = {$listing.rating_num};
		if (def_rate > 0) {
			def_sdv = def_rate * star_h;
			$(".rating_over").width(def_sdv);
		}

		{if $listing.rating_array.title eq 1}
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
				$.post(url_to_ajax, { action:"rate", listing:listing, rate:rate}, function (data) {
					if (!isNaN(data) && data > 0) {
						rate = data;
						def_sdv = rate * star_h;
						$("#rating_over").width(def_sdv);
					}
				});
				$("#rating_rate").html(rate);
				$("#rating").attr("title", vote_phrase);
			});
		{/if}

	});
</script>
