<div id="suggestedJobs">
    <div id="suggestedJobs-title"><h2>[[Suggested Jobs]]</h2></div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th class="tableLeft"> </th>
                    <th class="rec-sug-title-th">[[Job Title]]</th>
                    <th class="rec-sug-comp-th">[[Company]]</th>
                    <th class="rec-sug-loc-th">[[Location]]</th>
                    <th class="tableRight"> </th>
                </tr>
            </thead>
            {if $listings}
                {foreach from=$listings item=listing name=listings_block}
                    <tr class="{cycle values = 'evenrow,oddrow' advance=true}">
                        <td> </td>
                        <td class="rec-sug-title"><a href="{$GLOBALS.site_url}/display-job/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html">{$listing.Title}</a></td>
                        <td class="rec-sug-comp">{if $listing.user.CompanyName}<a href="{$GLOBALS.site_url}/company/{$listing.user.id}/{$listing.user.CompanyName}">{$listing.user.CompanyName}{/if}</a></td>
                        <td class="rec-sug-loc">{locationFormat location=$listing.Location format="short"}</td>
                        <td> </td>
                    </tr>
                {/foreach}
            {else}
                <tr>
                    <td> </td>
                    <td colspan="3">[[There are no suggested jobs at the moment. Please try to enrich your profile info and resume(s).]]</td>
                    <td> </td>
                </tr>
            {/if}
        </table>
    </div>
</div>