{if $GLOBALS.is_ajax}


	{foreach from=$content item=item}
        <article class="clearfix blogpost object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="200">
            <div class="blogpost-body">
                <div class="post-info">
                    <span class="day">{$item.date|date_format:"%d"}</span>
                    <span class="month">{$item|date_format:"%B %Y"}</span>
                </div>
                <div class="blogpost-content">
                    <header>
                        <h2 class="title"><a href="{$item.link}" class="blogLink">{$item.title}</a></h2>
                        {*<div class="submitted"><i class="fa fa-user pr-5"></i> by <a href="#">John Doe</a></div>*}
                    </header>
                    <p>{$item.description}</p>
                </div>
            </div>
            <footer class="clearfix">
                <a class="pull-right link" href="{$item.link}"><span>Read more</span></a>
            </footer>
        </article>
	{foreachelse}
		<br/><div class="alert alert-danger">[[There are no blog posts in the system.]]</div><br/>
	{/foreach}

{else}

	<div id="innerBlogContentDiv"></div>


	<!-- preloader row here -->
	<div id="ajax_preloader_blog" class="preloader">
		<img src="{$GLOBALS.site_url}/templates/_system/main/images/ajax_preloader_circular_32.gif" />
	</div>


	<script  type="text/javascript">
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