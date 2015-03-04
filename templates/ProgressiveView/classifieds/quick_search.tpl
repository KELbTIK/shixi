<div id="quick-search">
	<div id="quick-search-left">
		<div class="label"><h1>[[Find a Job]]</h1></div>
		<div class="right-arrow"></div>
	</div>
	<div id="quick-search-right">
		<form action="{$GLOBALS.site_url}/search-results-jobs/" id="quickSearchForm">
			<input type="hidden" name="action" value="search" />
			<input type="hidden" name="listing_type[equal]" value="Job" />
			<fieldset>
				<div class="quick-search-input">{search property=keywords}</div><span class="in">[[in]]</span>
				<div class="quick-search-input">{search property=Location searchWithin=false fields=$locationFields template="location.like.tpl"}</div>
				<div class="quick-search-btn"><div class="find-button-zoom"><input type="submit" id="btnSearch" value="[[Search]]"/></div></div>
			</fieldset>

			<fieldset>
				<div id="inputStat">{module name="classifieds" function="count_listings"}</div>
				<div id="quickSearchLinks">
					<ul>
						<li><a href="#" id="moreOptions">[[More Options]]</a>
							<ul id="moreOprtionsItem">
								<li><a href="{$GLOBALS.site_url}/find-jobs/">&#187; <span>[[Advanced search]]</span></a></li>
								{if $acl->isAllowed('open_search_by_company_form')}
									<li><a href="{$GLOBALS.site_url}/browse-by-company/">&#187; <span>[[Search by Company]]</span></a></li>
								{/if}
							</ul>
						</li>
					</ul>
				</div>
			</fieldset>
		</form>
	</div>
</div>


<script type="text/javascript">
	$(function() {
		$("#keywords").val('[[Keywords]]');

		$("#quickSearchForm").submit(function(){
			if ($("#keywords").val()=='[[Keywords]]')
			{
				$("#keywords").val('');
			}
		});

		$("#keywords").focus(function(){
			if ($("#keywords").val()=='[[Keywords]]')
			{
				$("#keywords").val('');
			}
		});

		$("#keywords").blur(function(){
			if ($("#keywords").val()=='')
			{
				$("#keywords").val('[[Keywords]]');
			}
		});
	});
</script>