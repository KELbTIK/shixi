<div id="display-contact">
{title} {$listing.Title} {/title}
{keywords} {$listing.Title} {/keywords}
{description} {$listing.Title} {/description}
{if $errors}
	{foreach from=$errors key=error_code item=error_message}
		<p class="error">
			{if $error_code == 'UNDEFINED_CONTACT_ID'} [[Contact ID is not defined]]
				{title} [[404 Not Found]] {/title}
			{elseif $error_code == 'WRONG_CONTACT_ID_SPECIFIED'} [[There is no contact in the system with the specified ID]]
			{/if}
		</p>
	{/foreach}
	{else}
	<script type="text/javascript">
			{literal}
			function SaveAd(noteId, url){
				$.get(url, function(data){
					$("#"+noteId).html(data);
				});
			}
			{/literal}
	</script>

    <!-- PROFILE BLOCK -->
	<div id="contactInfo">
        <div id="contactInfo-in">
            <div id="user-top">
                <div id="contact-info">
                    {if $contactInfo.group.id eq "Employer"}
                        <h1>{$contactInfo.ContactName}</h1>
                        <div id="contact-group">{$contactInfo.group.caption} |
							<a href="{$GLOBALS.site_url}/company/{$contactInfo.id}/{$contactInfo.CompanyName|regex_replace:"/[\s\/\\\\]/":"-"|escape:"url"}/">{$contactInfo.CompanyName}</a><br/>
                        </div>
                    {elseif $contactInfo.group.id eq "JobSeeker"}
                        <h1>{$contactInfo.FirstName} {$contactInfo.LastName}</h1>
                        <div id="contact-group">{$contactInfo.group.caption}</div>
                    {/if}
                    <div class="clr"><br/></div>

                    <fieldset>
                        <div class="contact-detail-cap">[[Email]]:</div>
                        <div class="contact-detail-info">{$contactInfo.email}</div>
                    </fieldset>
                    <fieldset>
                        <div class="contact-detail-cap">[[Phone Number]]:</div>
                        <div class="contact-detail-info">{$contactInfo.PhoneNumber}</div>
                    </fieldset>
                    {if $contactInfo.Location.City || $contactInfo.Location.State || $contactInfo.Location.Country}
                        <fieldset>
                            <div class="contact-detail-cap">[[Location]]:</div>
                            <div class="contact-detail-info">{locationFormat location=$contactInfo.Location format="long"}</div>
                        </fieldset>
                    {/if}
                    {if $contactInfo.Location.Address}
                        <fieldset>
                            <div class="contact-detail-cap">[[Address]]:</div>
                            <div class="contact-detail-info">{$contactInfo.Location.Address}</div>
                        </fieldset>
                    {/if}
                    {if $contactInfo.WebSite}
                        <fieldset>
                            <div class="contact-detail-cap">[[Web Site]]:</div>
                            <div class="contact-detail-info">{$contactInfo.PhoneNumber}</div>
                        </fieldset>
                    {/if}
                </div>

                <div id="contact-image">
                    {if $contactInfo.Logo.file_url}
                        <img src="{$contactInfo.Logo.file_url}" alt="" />
                        {elseif $contactInfo.Picture.file_url}
                        <img src="{$contactInfo.Picture.file_url}" alt="" />
                        {else}
                        <img src="{image}no-profile-picture.png" border="0" alt="" />
                    {/if}
                </div>
                <div class="clr"></div>

                <div id="under-contact">
                    <div id="note-block">
                        <span id = 'formNote_{$contactInfo.sid}'>
                            {if $contactInfo.note && $contactInfo.note != ''}
                                <span class="strong">[[My notes]]:</span> {$contactInfo.note|escape:"html"}
                            {/if}
                        </span>
                        <span id='notes_{$contactInfo.sid}'>
                            {if $contactInfo.note && $contactInfo.note != ''}
                                <a href="{$GLOBALS.site_url}/private-messages/contact/{$contactInfo.sid}/?action=edit_note"
                                   onclick="SaveAd( 'formNote_{$contactInfo.sid}', '{$GLOBALS.site_url}/private-messages/contact/{$contactInfo.sid}/?action=edit_note'); return false;"
                                   class="action">[[Edit notes]]</a>&nbsp;&nbsp;
                                {else}
                                <a href="{$GLOBALS.site_url}/private-messages/contact/{$contactInfo.sid}/?action=add_note"
                                   onclick="SaveAd( 'formNote_{$contactInfo.sid}', '{$GLOBALS.site_url}/private-messages/contact/{$contactInfo.sid}/?action=add_note'); return false;"
                                   class="action">[[Add notes]]</a>&nbsp;&nbsp;
                            {/if}
                        </span>
                    </div>

                    {if $acl->isAllowed('use_private_messages')}
                        <div id="cont-info-sendpm">
                            <input type="button" name="send-message" value="[[Send private message]]" onclick="javascript: location.href='{$GLOBALS.site_url}/private-messages/send/?to={$contactInfo.sid}';"/>
                        </div>
                    {/if}
                </div>
                <div class="clr"></div>

                <div class="user-listings">
                    {if $contactInfo.group.id eq "Employer"}
                        <a href="{$GLOBALS.site_url}/search-results-jobs/?action=search&amp;username[equal]={$contactInfo.sid}">[[View all jobs by this user]]</a>
                    {elseif $contactInfo.group.id eq "JobSeeker" && $acl->isAllowed('view_resume_search_results')}
                        <a href="{$GLOBALS.site_url}/search-results-resumes/?action=search&username[equal]={$contactInfo.sid}">[[View all resumes by this user]]</a>
                    {/if}
                </div>

            </div>
        </div>
	</div>
    <div id="contactInfo-footer"></div>
	<!-- END PROFILE BLOCK -->
</div>
{/if}
