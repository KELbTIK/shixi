{if $GLOBALS.is_ajax}

	{foreach from=$content item=item}
		<div class="blogAuthor"></div>
		<div class="blogPost">
			<a href="{$item.link}" class="blogLink">{$item.title}</a>
			<span class="blogDate">{$item.date}</span>
			<div class="clr"></div>
			<span class="blogtext"><p>{$item.description}</p></span>
		</div>
		<div class="blogBottom"></div>
		<div class="clr"><br/></div>
	{foreachelse}
		<br/><div class="text-center">[[There are no blog posts in the system.]]</div><br/>
	{/foreach}

{else}

	<div id="innerBlogContentDiv"></div>


	<!-- preloader row here -->
	<div id="ajax_preloader_blog" class="preloader">
		<img src="{$GLOBALS.site_url}/templates/_system/main/images/ajax_preloader_circular_32.gif" />
	</div>


	<script language="javascript" type="text/javascript">
	{literal}

		function getBlogContent() {
			$('#ajax_preloader_blog').show();

			var ajaxUrl = "{/literal}{$GLOBALS.site_url}/ajax/{literal}";
			var ajaxParams = {
				'action' : 'get_blog_content',
				'listing_type[equal]' : 'Job'
			};

			$.get(ajaxUrl, ajaxParams, function(data) {
				$('#ajax_preloader_blog').hide();
				$('#innerBlogContentDiv').append(data);
			});
		}

		// make request for blog content after page loads
		$(function() {
			getBlogContent();
		});

	{/literal}
	</script>

{/if}