{breadcrumbs}[[Backup/Restore]]{/breadcrumbs}
<h1><img src="{image}/icons/download32.png" border="0" alt="" class="titleicon"/>[[Backup/Restore]]</h1>
{if $errors}
	{foreach from=$errors item=error}
		{if $error == 'FTP_DETAILS_NOT_VALID'}
			<p class="error">[[Please enter valid FTP details]]</p>
		{/if}
		{if $error == 'EXP_PERIOD_NOT_VALID'}
			<p class="error">[[You can use only numeric characters for field "Created backup is expired and deleted after"]]</p>
		{/if}
		{if $error == 'SETTINGS_SAVED_WITH_PROBLEMS'}
			<p class="error">[[Backup settings saved with problems, please try again]]</p>
		{/if}
	{/foreach}
{/if}
{if $successSaveMessage}
	<p class="message"> [[Your changes were successfully saved]] </p>
{/if}
<div id="error" class="error" style="display:none;"></div>
	<div id="settingsPane">
		<ul class="ui-tabs-nav">
			<li class="ui-tabs-selected"><a href="#backup"><span>[[Backup]]</span></a></li>
			<li class="ui-tabs-unselect"><a href="#restore"><span>[[Restore]]</span></a></li>
			<li class="ui-tabs-unselect"><a href="{$GLOBALS.site_url}/backup/?action=created_backups"><span>[[Created Backups]]</span></a></li>
		</ul>
		<div id="backup" class="ui-tabs-panel">
		<form method="post" action="">
			<input type="hidden" name='action' value='backup' />
			[[Backup Type]] &nbsp;

			<select name='backup_type' id='backup_type'>
				<option value="full">[[Full site backup]]</option>
				<option value="database">[[Site database only]]</option>
				<option value="files">[[Site files only]]</option>
			</select>

			&nbsp; <input  type='button' name='save' class="grayButton" id="backupButton" value='[[Generate Backup]]' onclick='submitForm()'>

			<div class="clr"><br/></div>
			<div id="progbar"></div>
			<p>&nbsp;</p>
			</form>
			<form method="post" action="">
				<div>
				<input type="hidden" name="action" value="save">
					<p>
						[[Enable Autobackup]] <input type="hidden" name="autobackup" value="0" /><input type="checkbox" name="autobackup" id="autobackup" value="1" {if $settings.autobackup}checked="checked"{/if}/>
						<input type="submit" class="greenButton" value="[[Save]]" id="save">
					</p>
					<p>&nbsp;</p>
					<div id="autobackupSettings" style="display: none;">
						<p>[[Autobackups can be downloaded in "Created Backups" tab]].</p>
						<table width="100%">
							<tr>
								<td width="32%">[[Backup Type]]:</td>
								<td>
									<select name='backup_type' id='backup_type_auto'>
										<option value="full" {if $settings.backup_type == "full"}selected="selected"{/if}>[[Full site backup]]</option>
										<option value="database" {if $settings.backup_type == "database"}selected="selected"{/if}>[[Site database only]]</option>
										<option value="files" {if $settings.backup_type == "files"}selected="selected"{/if}>[[Site files only]]</option>
									</select>
								</td>
							</tr>
							<tr>
								<td width="32%">[[Backup frequency]]:</td>
								<td>
									<select name='backup_frequency' id='backup_frequency'>
										<option value="daily" {if $settings.backup_frequency == "daily"}selected="selected"{/if}>[[Daily]]</option>
										<option value="weekly" {if $settings.backup_frequency == "weekly"}selected="selected"{/if}>[[Weekly]]</option>
										<option value="monthly" {if $settings.backup_frequency == "monthly"}selected="selected"{/if}>[[Monthly]]</option>
									</select>
								</td>
							</tr>
							<tr>
								<td width="32%">[[Backup will be expired and deleted after]]:</td>
								<td>
									<input type="text" id="backup_expired_period" name="backup_expired_period" value="{$settings.backup_expired_period|escape:"html"}"> [[days]]<br/>
									<span class="note">[[Leave empty or put zero for unlimited backup lifetime]]</span>
								</td>
							</tr>
							<tr>
								<td width="32%">[[Save backup to FTP]]:</td>
								<input type="hidden" name="ftp_backup" value="0" />
								<td>
									<input type="checkbox" id="ftp_backup" name="ftp_backup" {if $settings.ftp_backup}checked="checked"{/if} />
									<div id="ftp_settings" style="display: none;">
										<table width="100%">
											<tr>
												<td>[[Remote FTP Host]]: </td><td>ftp:// <input type="text" id="backup_ftp_host" name="backup_ftp_host" value="{$settings.backup_ftp_host|escape:"html"}"></td>
											</tr>
											<tr>
												<td>[[FTP Backup User]]: </td><td><input type="text" id="backup_ftp_user" name="backup_ftp_user" value="{$settings.backup_ftp_user|escape:"html"}"></td>
											</tr>
											<tr>
												<td>[[FTP Backup Password]]:</td><td><input type="password" id="backup_ftp_password" name="backup_ftp_password" value="{$settings.backup_ftp_password|escape:"html"}"></td>
											</tr>
											<tr>
												<td>[[FTP Backup Directory]]:</td><td><input type="text" id="backup_ftp_directory" name="backup_ftp_directory" value="{$settings.backup_ftp_directory|escape:"html"}"></td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</form>
		</div>
	
		<div id="restore" class="ui-tabs-panel ui-tabs-hide">
			<form method="post" action="" enctype="multipart/form-data" id='restoreForm' onsubmit = "return restore();">
				<input type="hidden" name='action' value='restore' />
				[[Backup File]] &nbsp;
				<input type="file" name="restore_file"> <small>([[max.]] {$uploadMaxFilesize} M)</small>
				&nbsp; <input type="submit" name="save" value="[[Restore Now]]" class="grayButton" />

				<div class="clr"><br/></div>
				<div id='progbarRestore'></div>
			</form>
		</div>
	</div>	

<script type="text/javascript">
	$(document).ready(function(){
		$("#settingsPane").tabs();
		if ($("#autobackup").attr('checked')) {
			$("#autobackupSettings").show();
		}
		if ($("#ftp_backup").attr('checked')) {
			$("#ftp_settings").show();
		}
	});
	$("#autobackup").bind('click', function() {
		if ($("#autobackup").attr('checked')) {
			$("#autobackupSettings").show();
		} else {
			$("#autobackupSettings").hide();
		}
	});

	$("#ftp_backup").bind('click', function() {
		if ($("#ftp_backup").attr('checked')) {
			$("#ftp_settings").show();
		} else {
			$("#ftp_settings").hide();
		}
	});

	function submitForm() {
		var url = "{$GLOBALS.site_url}/backup/";
		var backup_type = $('#backup_type').val();
		var identifier = "{$identifier}";
		$("#backupButton").attr("disabled", true);
		$("#progbar").html('<iframe src="{$GLOBALS.user_site_url}/system/ext/jquery/progbar.gif" title="[[Please wait ...]]" scrolling="no" frameborder="0"></iframe>[[Please wait ...]]');
		$.post(url, { action: "backup", backup_type: backup_type, identifier: identifier}, function(data){

		});
		setTimeout('check()',5000);
		$("#progbar iframe").load(function(){
			$(this).contents().find('body').css({ "margin": "1px 0 0 0"});
		})
	}

	function restore() {
		var options = {
				  url:  "{$GLOBALS.site_url}/backup/",
				  identifier: "{$identifier}"
				}; 
		$("#restoreForm").ajaxSubmit(options);
		$("#progbarRestore").html('<img style="vertical-align: middle" src="{$GLOBALS.user_site_url}/system/ext/jquery/progbar.gif" alt="[[Please wait ...]]" /> &nbsp; [[Please wait ...]]');
		setTimeout('restoreCheck()',5000);
		return false;
	}
	
	function check() {
		var url = "{$GLOBALS.site_url}/backup/";
		var identifier = "{$identifier}";
		$.post(url, { action: "check", identifier: identifier}, function(data){
			$("#error").hide();
			if (data == 1) {
				setTimeout('check()',2000);
			}
			else if (data == 'error' || data.search('Error') != -1) {
				$("#progbar").html('');
				$("#backupButton").attr("disabled", false);
				$.post(url, { action: "error"}, function(data){
					$("#error").html(data);
					$("#error").show();
				});
			}
			else {
				$("#backupButton").attr("disabled", false);
				$("#progbar").html('');
				window.location = data;
			}
		});
	}

	function restoreCheck() {
		var url = "{$GLOBALS.site_url}/backup/";
		var identifier = "{$identifier}";
		$.post(url, { action: "check", identifier: identifier}, function(data){
			$("#error").hide();
			if (data == 1) {
				setTimeout('restoreCheck()',2000);
			}
			else if (data == 'error' || data.search('Error') != -1) {
				$("#progbarRestore").html('');
				$.post(url, { action: "error"}, function(data){
					$("#error").html(data);
					$("#error").show();
				});
			}
			else {
				$("#progbarRestore").html('');
				window.location = data;
			}
		});
	}
</script>