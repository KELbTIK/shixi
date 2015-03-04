{breadcrumbs}
	<a href="{$GLOBALS.site_url}/edit-email-templates/">[[Manage Email Templates]]</a> &#187;
	<a href="{$GLOBALS.site_url}/edit-email-templates/{$group}/">[[{$etGroups.$group}]]</a> &#187; [[Edit]] "[[{$tplInfo.name}]]" [[Template]]
{/breadcrumbs}
<h1><img src="{image}/icons/contactbook32.png" border="0" alt="" class="titleicon"/>[[Edit]] "[[{$tplInfo.name}]]" [[Template]]</h1>

{foreach from=$errors item=error}
	{if $error == 'NOT_ALLOWED_IN_DEMO'}
		<p class="error">[[This action is not allowed in Demo mode]]</p>
	{/if}
{/foreach}

<fieldset>
	<legend>&nbsp;[[Edit Email Template]]</legend>
	{include file='../users/field_errors.tpl'}
	<p>[[Fields marked with an asterisk (<span class="required">*</span>) are mandatory]]</p>
	<form method="post" enctype="multipart/form-data" action="" id="email-template-edit">
		<input type="hidden" id="action" name="action" value="save_info"/>
		<table>
			{foreach from=$form_fields item=form_field}
				{if $form_field.id == 'user_defined'}
				{else}
					<tr>
						<td valign="top">[[{$form_field.caption}]]</td>
						<td valign="top" class="required">&nbsp;{if $form_field.is_required}*{/if}</td>
						<td>{if $form_field.id eq 'file'}{input property=$form_field.id template="file_et.tpl"}{else}{input property=$form_field.id}{/if}</td>
					</tr>
				{/if}
			{/foreach}
			<tr>
				<td colspan="3">
                    <div class="floatRight">
                        <input type="submit" id="apply" name="apply" value="[[Apply]]" class="grayButton"/>
                        <input type="submit" name="save" value="[[Save]]" class="grayButton" />
                    </div>
                </td>
			</tr>
		</table>
	</form>

	<h3>[[You can use the following variables in this Email Template]]:</h3>
	<div class="email-tpl-vars">
		<ul class="et-vars-groups">
			<li>[[Global Variables]]:
				<div class="et-vars">
					{literal}
					<span class="et-var-val">{$GLOBALS.user_site_url} - [[Website URL]]</span>
					<span class="et-var-val">{$GLOBALS.settings.site_title} - [[Website Title]]</span>
					<span class="et-var-val">{$GLOBALS.settings.notification_email}  - [[System Email]]</span>
					{/literal}
				</div>
			</li>
			{if $userGroups}
				<li>[[User Variables]]:
					<div class="et-vars">
					{if $tplInfo.name == 'New Private Message Notification'}
						<span class="spec-vars-title spec-vars-var">{literal}{$sender} or {$recipient}{/literal} - [[array(s) containing the following variables]]:</span>
					{else}
						<span class="spec-vars-title spec-vars-var">{literal}{$user}{/literal} - [[array containing the following variables]]:</span>
					{/if}
					<div class="specific-vars">
					{foreach from=$userGroups item="userGroup"}
						<h5>[[{$userGroup.caption}]]</h5>
						<ul>
							{foreach from=$userGroup.fields item="field"}
							<li>.{$field}</li>
							{/foreach}
						</ul>
					{/foreach}
					</div>
					</div>
				</li>
			{/if}
			{* >>>>>>>>>>> LISTING VARS >>>>>>>>>>>>>>>>>>*}
			{if $listingTypes}
			<li>[[Lisitng Variables]]:
				<div class="et-vars">
					<span class="spec-vars-title spec-vars-var">{literal}{$listing}{/literal} - [[array containing the following variables]]:</span>
					<div class="specific-vars">
						{foreach from=$listingTypes item="listingType"}
							<h5>[[{$listingType.name}]]</h5>
							<ul>
								{foreach from=$listingType.fields item="field"}
								<li>.{$field}</li>
								{/foreach}
							</ul>
						{/foreach}
					</div>
				</div>
			</li>
			{/if}
			{* <<<<<<<<<<<<<< LISTING VARS <<<<<<<<<<<<<<<<<<<<<<<*}
			{if $group != 'user' && $group != 'listing'}
			<li>[[Other Variables]]:
				<div class="et-vars">
			{* >>>>>>>>>>>>>> PRODUCT VARS >>>>>>>>>>>>>>>>>>>>>>> *}
			{if $productTypes && $tplInfo.sid != 25}
				<span class="spec-vars-title spec-vars-var">{literal}{$product}{/literal} - [[array containing the following variables]]:</span>
				<div class="specific-vars">
					{foreach from=$productTypes item="productType"}
						<h5>[[{$productType.caption}]]</h5>
						<ul>
							{foreach from=$productType.fields item="field"}
							<li>.{$field}</li>
							{/foreach}
						</ul>
					{/foreach}
				</div>
			{/if}
			{* <<<<<<<<<<<<<< PRODUCT VARS <<<<<<<<<<<<<<<<<<<<<<< *}

			{* >>>>>>>>>>>>>> PRODUCT VARS >>>>>>>>>>>>>>>>>>>>>>> *}
			{if $searchTplVars}
				<span class="spec-vars-title spec-vars-var">{literal}{$savedSearch}{/literal} - [[array containing the following variables]]:</span>
				<div class="specific-vars">
					<ul>
					{foreach from=$searchTplVars item="field"}
					<li>.{$field}</li>
					{/foreach}
					</ul>
				</div>
			{/if}
			{* <<<<<<<<<<<<<< ALERTS VARS <<<<<<<<<<<<<<<<<<<<<<< *}

			{if $subadmin}
				<span class="spec-vars-title spec-vars-var">{literal}{$subadmin}{/literal} - [[array containing the following variables]]:</span>
				<div class="specific-vars">
						<ul>
							{foreach from=$subadmin item="field"}
							<li>.{$field}</li>
							{/foreach}
						</ul>
				</div>
			{/if}

			{if $tplInfo.name == 'Apply Now Email'}
				<span class="spec-vars-title spec-vars-var">{literal}{$applicant_request}{/literal} - [[array of all data sent from apply now form containing the following variables]]:</span>
				<div class="specific-vars">
					<ul>
						<li>.name - [[applicant’s name]]</li>
						<li>.email - [[applicant’s email]]</li>
						<li>.comments - [[cover letter]]</li>
					</ul>
				</div>
				<span class="spec-vars-title spec-vars-var">{literal}{$questionnaire_info}{/literal} - [[array of data on questionnaire containing the following variables]]:</span>
				<div class="specific-vars">
					<ul>
						<li>.name - [[screening questionnaire name]]</li>
						<li>.passing_score - [[questionnaire status (Passed/Failed)]]</li>
					</ul>
				</div>
				<span class="spec-vars-title spec-vars-var">{literal}{$questionnaire}{/literal} - [[array of data on questionnaire containing the following variables]]:</span>
				<div class="specific-vars">
					<ul>
						<li>.question - [[question]]</li>
						<li>.answer - [[answer or answers]]</li>
					</ul>
				</div>
				<span class="spec-vars-var">{literal}{$score}{/literal} - [[score]]</span>
				<span class="spec-vars-title spec-vars-var">{literal}{$data_resume}{/literal} - [[array containing the following variables]]:</span>
				<div class="specific-vars">
					<ul>
						<li>.sid - [[applicant’s resume ID]]</li>
						<li>.Title - [[applicant’s resume title]]</li>
					</ul>
				</div>
			{elseif $tplInfo.sid == 6}
			<span class="spec-vars-var">{literal}{$network}{/literal} - [[Social network user has registered with]]</span>
			{elseif $tplInfo.sid == 25}
				<span class="et-var-val spec-vars-var">{literal}{$bannerGroup.id}{/literal} - [[Banner Group ID]]</span>
				<span class="et-var-val spec-vars-var">{literal}{$bannerGroup.sid}{/literal} - [[Banner Group SID]]</span>
			{* Banner rejection *}
			{elseif $tplInfo.sid == 43}
			<span class="spec-vars-var">{literal}{$bannerInfo}{/literal} - [[Banner info]]</span>
			<span class="spec-vars-var">{literal}{$bannerInfo}{/literal} - [[Banner info]]</span>
			<span class="spec-vars-var">{literal}{$reason}{/literal} - [[rejection reason]]</span>
			<span class="spec-vars-var">{literal}{$admin_email}{/literal} - [[admin email]]</span>
			{elseif $tplInfo.name == 'Mass Mailing Email'}
			<span class="spec-vars-var">{literal}{$subject}{/literal} - [[mass mailing subject]]</span>
			<span class="spec-vars-var">{literal}{$message}{/literal} - [[mass mailing message]]</span>
			{elseif $tplInfo.name == 'Tell a Friend Email'}
				<span class="spec-vars-title spec-vars-var">{literal}{$submitted_data}{/literal} - [[array containing the following variables]]:</span>
				<div class="specific-vars">
					<ul>
						<li>.name - [[adviser’s name]]</li>
						<li>.friend_name - [[friend’s name]]</li>
						<li>.comment - [[recommendation comment]]</li>
					</ul>
				</div>
			{elseif $tplInfo.name == 'New Private Message Notification'}
				<span class="spec-vars-title spec-vars-var">{literal}{$message}{/literal} - [[array containing the following variables]]:</span>
				<div class="specific-vars">
					<ul>
						<li>.data - [[date of message receipt]]</li>
						<li>.subject - [[message subject]]</li>
						<li>.message - [[message body]]</li>
						<li>.id - [[message ID]]</li>
					</ul>
				</div>
			{elseif $tplInfo.name eq 'Contact Form Email'}
			<span class="spec-vars-var">{literal}{$name}{/literal} - [[name of a user that submitted a comment]]</span>
			<span class="spec-vars-var">{literal}{$email}{/literal} - [[user email]]</span>
			<span class="spec-vars-var">{literal}{$comments}{/literal} - [[comments]]</span>
			{elseif $tplInfo.name eq 'Screening Questionnaire Auto Reply'}
			<span class="spec-vars-var">{literal}{$text}{/literal} - [[auto-reply text to candidates on Screening questionnaire results]]</span>
			{elseif $tplInfo.name eq 'Sub-admin Registration Email' || $tplInfo.name eq 'Sub-user Registration Email'}
				{if $tplInfo.name eq 'Sub-user Registration Email'}
					<span class="spec-vars-title spec-vars-var">{literal}{$user}{/literal} - [[array of the registered subadmin containing the following variables]]:</span>
					<div class="specific-vars">
						<ul>
							<li>.username</li>
							<li>.email.original</li>
							<li>.parent.sid - [[parent profile SID]]</li>
							<li>.group.id</li>
							<li>.group.caption</li>
						</ul>
					</div>
				{/if}
				<span class="spec-vars-title spec-vars-var">{literal}{$permissions}{/literal} - [[array of permissions of the registered subadmin containing the following variables]]:</span>
				<div class="specific-vars">
					<ul>
						<li>.title - [[Title of a permission]]</li>
						<li>.value - [[Permission Value]]</li>
					</ul>
				</div>
				{if $tplInfo.name eq 'Sub-admin Registration Email'}
				<span class="spec-vars-var">{literal}{$admin_email}{/literal} - [[admin email]]</span>
				{/if}
			{/if}
				</div>
			</li>
			{/if}
		</ul>
	</div>
</fieldset>

<script type="text/javascript">
	$('#apply').click(
		function(){
			$('#action').attr('value', 'apply_info');
		}
	);
	$(".spec-vars-title, .spec-vars-title-h").click(function(){
		if ($(this).attr("class") == 'spec-vars-title') {
			showSpecVars($(this));
		}else {
			hideSpecVars($(this));
		}
	});
	hideSpecVars($(".spec-vars-title, .spec-vars-title-h"));
	function showSpecVars(obj){
		obj.next(".specific-vars").show("slow");
		obj.attr("class", "spec-vars-title-h");
	}
	function hideSpecVars(obj){
		obj.next(".specific-vars").hide("slow");
		obj.attr("class", "spec-vars-title");
	}
</script>
