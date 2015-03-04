{breadcrumbs}
	<a href="{$GLOBALS.site_url}/show-import/">[[XML Import]]</a>
	{if $id > 0}&#187; [[Add new Data Source - step one]]{/if}
{/breadcrumbs}
<h1><img src="{image}/icons/recycle32.png" border="0" alt="" class="titleicon"/> [[Create New Source]]</h1>

<script type="text/javascript">
	$(function() {
		$(".help_div").click( function() {
			$(".help_inner").slideToggle("slow");
			});
	});
</script>


{if $errors}
	{foreach from=$errors item=error}
		<p class="error">[[{$error}]]</p>
	{/foreach}
{/if}

<div class="setting_button" id="mediumButton">[[Please paste XML code of one posting item from the source to be imported. For example]]<div class="setting_icon"><div id="accordeonClosed"></div></div></div>
<div class="setting_block" style="display: none">
	&nbsp;&nbsp;&lt;result&gt;
	<br>&nbsp;&nbsp;&lt;jobtitle&gt;Director Application Development&lt;/jobtitle&gt;
	<br>&nbsp;&nbsp;&lt;company&gt;The ExMScGraw-Hill Companies&lt;/company&gt;
	<br>&nbsp;&nbsp;&lt;city&gt;New York&lt;/city&gt;
	<br>&nbsp;&nbsp;&lt;state&gt;NY&lt;/state&gt;
	<br>&nbsp;&nbsp;&lt;country&gt;US&lt;/country&gt;
	<br>&nbsp;&nbsp;&lt;source&gt;The ExMScGraw-Hill Companies&lt;/source&gt;
	<br>&nbsp;&nbsp;&lt;date&gt;Fri, 27 Mar 2009 03:36:37 GMT&lt;/date&gt;
	<br>&nbsp;&nbsp;&lt;url&gt;http://www.somesite.com/viewjobs?j=9320f73ca0f9fc53&lt;/url&gt;
	<br>&nbsp;&nbsp;&lt;latitude&gt;40.704235&lt;/latitude&gt;
	<br>&nbsp;&nbsp;&lt;longitude&gt;-73.91793&lt;/longitude&gt;
	<br>&nbsp;&nbsp;&lt;jobkey&gt;9320f73ca0f9fc53&lt;/jobkey&gt;
	<br>&nbsp;&nbsp;&lt;/result&gt;</b>
	<br>
</div>
<div class="clr"><br/></div>
<form method="post" action="{$GLOBALS.site_url}/add-import/">
	<input type="hidden" name="add_level" value="2">
	<table>
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td>[[Select type for imported listings]]</td>
			<td align="left">
				<select name="type_id">
					{foreach from=$types item=type key=key}
						<option value="{$key}">[[{$type}]]</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td colspan="2">[[XML code]]</td>
		</tr>
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td colspan="2"><textarea rows="20" cols="79" name="xml">{$xml}</textarea></td>
		</tr>
		<tr id="clearTable">
			<td colspan="2">
                <div class="floatRight"><input type="submit" value="[[Next]]" class="greenButton" /></div>
            </td>
		</tr>
	</table>
</form>

<script type="text/javascript">
$(function() {
	$(".setting_button").click(function(){
		var butt = $(this);
		$(this).next(".setting_block").slideToggle("normal", function(){
				if ($(this).css("display") == "block") {
					butt.children(".setting_icon").html("<div id='accordeonOpen'></div>");
					butt.children("b").text("[[Hide Import Help]]");
				} else {
					butt.children(".setting_icon").html("<div id='accordeonClosed'></div>");
					butt.children("b").text("[[Show Import Help]]");
				}
			});
	});
});
</script>