{foreach from=$errors item=error}
	<p class="error">[[{$error}]]</p>
{foreachelse}
	<h3>[[Left]]: <span class="update-cf">{$current_file}</span></h3>
	<h3>[[Right]]: <span class="update-cf">{$update_file}</span></h3>

	<br />
	<br />

	{$diffTbl}

{/foreach}

<script>
	$(function() {
		$(".inner_table").scroll(function() {
			var scrollTopValue = $(this).scrollTop();
			var scrollLeftValue = $(this).scrollLeft();
			$(".inner_table").scrollTop(scrollTopValue);
			$(".inner_table").scrollLeft(scrollLeftValue);
		});
	});
</script>