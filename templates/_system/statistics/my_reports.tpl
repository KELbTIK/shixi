<h1>[[My Reports]]</h1>
{if $errors}
	{foreach from=$errors item="errorData" key="errorId"}
		{if $errorId == 'NOT_EMPLOYER'}
			<p class="error">[[You are not employer]]</p>
		{/if}
	{/foreach}
{else}
	<div id="reports">
		<div id="reports-navigation">
			<div id="reports-navigation-in">
				<div id="reports-navigation-in-border">
					<a href="{$GLOBALS.site_url}/my-reports/" class="first-item current">[[Quick Stats]]</a> |
					<a href="{$GLOBALS.site_url}/general-stats/" class="middle-item">[[General Stats]]</a> |
					<a href="{$GLOBALS.site_url}/job-stats/" class="last-item">[[Job Stats]]</a>
				</div>
			</div>
		</div>
        <br/>
        <div class="plan stripped Category"  style="margin-bottom:0 !important;">
            <div class="header">
                <h3>[[Quick Stats]]</h3>
            </div>
        </div>

		<div id="quick-stats" class="table-responsive">
			<table class="table">
				<tbody>
					<tr>
						<td>[[Current live jobs]]</td>
						<td>{$quickStat.countActiveListings}</td>
					</tr>
					<tr>
						<td>[[Jobs posted this month]]</td>
						<td>{$quickStat.countPostedListings}</td>
					</tr>
					<tr>
						<td>[[Job views this month]]</td>
						<td>{$quickStat.countViewedListings}</td>
					</tr>
					<tr>
						<td>[[Applications received this month]]</td>
						<td>{$quickStat.countApplications}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
{/if}