
<h3>Import</h3>
<p>All the imports will create seperate queued jobs in the background.</p>
<li>{{ HTML::link('import/2626', 'Afvalemmershop.nl') }}</li>
<li>{{ HTML::link('import/867', 'Bestelkado.nl') }}</li>
<li>{{ HTML::link('import/1078', 'Algebeld.nl') }}</li>

<h3>Export</h3>
<p>
    This will export all data that is ready to the taskreward application.
    <br>{{ HTML::link('export', 'Export') }}
</p>

<h3>Clicks</h3>
<p>
    Get the clicks registered from the affiliate parties and export them to the taskreward application.
    <br>{{ HTML::link('clicks', 'Export clicks') }}
</p>

<h4>Queue</h4>
<li>Jobs in queue: {{ $stats['current-jobs-ready'] }}</li>
<li>{{ HTML::link('delete', 'Delete all jobs') }}</li>

<h4>Nex job</h4>
<section style="width: 400px;">
    <pre style=" display: table-cell; white-space: pre-line; padding: 10px">
        <strong>{{ $ready->getId() }}</strong> {{ trim($ready->getData()) }}
    </pre>
</section>