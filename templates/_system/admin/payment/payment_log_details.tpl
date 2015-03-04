<p><strong>[[Date]]:</strong> {tr type="date"}{$paymentLogItem.date}{/tr}</p>
<p><strong>[[Gateway]]:</strong> {$paymentLogItem.gateway}</p>
<p><strong>[[Callback Response]]:</strong></p>
<pre>{$paymentLogItem.message|wordwrap:50:"\n"}</pre>