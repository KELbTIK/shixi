<object>
	<param name="movie" value="https://www.youtube.com/v/{$value|regex_replace:'|https?://(www\.)?youtube.com/watch\?v=|':''|escape:'url'}"></param>
	<param name="allowFullScreen" value="true"></param>
	<param name="allowscriptaccess" value="always"></param>
	<embed src="https://www.youtube.com/v/{$value|regex_replace:'|https?://(www\.)?youtube.com/watch\?v=|':''|escape:'url'}" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true"></embed>
</object>