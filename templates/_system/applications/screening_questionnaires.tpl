{literal}
	<script type="text/javascript">
		function deleteMessage(title, message, link){
			$("#messageBox").dialog( 'destroy' ).html(message);
			$("#messageBox").dialog({
				width: 300,
				height: 200,
				modal: true,
				title: title,
					buttons: {
					Ok: function() {
						$(this).dialog('close');
						location.href=link;
					},
					Cancel: function(){
						$(this).dialog('close');
					}
				}
				
			}).dialog( 'open' );
			return false;
		}
	</script>
{/literal}
<h1>[[Screening Questionnaires]]</h1>
<a class="btn btn-primary" href="{$GLOBALS.site_url}/screening-questionnaires/new/">[[Create New Questionnaire]]</a>
<div class="table-responsive">
    <table class="tableSearchResultApplications table table-condensed">
        <thead>
            <tr>
                <th class="tableLeft"> </th>
                <th>[[Questionnaire Name]]</th>
                <th colspan="2">[[Actions]]</th>
                <th class="tableRight"> </th>
            </tr>
        </thead>
        <tbody>
            {foreach item=question from=$questionnaires}
                <tr class="{cycle values = 'evenrow,oddrow'}">
                    <td></td>
                    <td>
                        {$question.name}
                    </td>
                    <td>
                        <a class="btn btn-warning btn-sm" href="{$GLOBALS.site_url}/screening-questionnaires/edit/{$question.sid}">[[Edit]]</a>
                    </td>
                    <td>
                        <a class="btn btn-danger btn-sm" href="{$GLOBALS.site_url}/screening-questionnaires/?action=delete&sid={$question.sid}" onclick="return deleteMessage('[[Delete Questionnaire]]', '[[Are you sure you want to delete the selected Questionnaire(s)?
                            It will be removed from your Job postings as well.]]', '{$GLOBALS.site_url}/screening-questionnaires/?action=delete&sid={$question.sid}');">[[Delete]]</a>
                    </td>
                    <td></td>
                </tr>
            {foreachelse}
                <tr class="{cycle values = 'evenrow,oddrow'}">
                    <td colspan="5" align="center"><div class="alert alert-info">[[There are no screening questionnaires]]</div></td>
                </tr>
            {/foreach}
        </tbody>
    </table>
</div>