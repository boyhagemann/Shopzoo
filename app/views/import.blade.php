<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{ HTML::style('//maxcdn.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css') }}
    {{ HTML::style('css/screen.css') }}
</head>
<body>

    <div class="container">

        @if(Session::has('success'))
        <div class="alert alert-success">{{{ Session::get('success') }}}</div>
        @endif

        <div class="col-lg-3">

            <h2>Import</h2>
            <p>All the imports will create seperate queued jobs in the background.</p>

            <h4>Info</h4>
            <p>Gather commission information and other useful data from all campaigns.</p>
            <p>{{ HTML::link('info', 'Update campaign info', ['class' => 'btn btn-primary btn-sm']) }}</p>

            <h4>Product feeds</h4>
            <p>Import the product feeds for these campaigns.</p>
            <p>{{ HTML::link('import/2626', 'Afvalemmershop.nl', ['class' => 'btn btn-default btn-sm']) }}</p>
            <p>{{ HTML::link('import/867', 'Bestelkado.nl', ['class' => 'btn btn-default btn-sm']) }}</p>
            <p>{{ HTML::link('import/1078', 'Algebeld.nl', ['class' => 'btn btn-default btn-sm']) }}</p>

        </div>

        <div class="col-lg-3">

            <h2>Export</h2>
            <p>This will export the data to the taskreward application</p>

            <h4>Tasks</h4>
            <p>
                Export all tasks that are ready.
            </p>
            <p>
                {{ HTML::link('export', 'Export', ['class' => 'btn btn-primary']) }}
            </p>

            <h4>Clicks</h4>
            <p>
                Get the clicks registered from the affiliate parties and export them to the taskreward application.
            </p>
            <p>
                {{ HTML::link('clicks', 'Export clicks', ['class' => 'btn btn-primary']) }}
            </p>

        </div>

        <div class="col-lg-6">

            <h2>Queue</h2>

            <h4>Stats</h4>
            <ul>
                <li>Jobs in queue: {{ $stats['current-jobs-ready'] }}</li>
            </ul>
            <p>{{ HTML::link('delete', 'Delete all jobs', ['class' => 'btn btn-danger']) }}</p>

            @if($ready)
            <h4>Next job</h4>
            <section style="width: 400px;">
                <pre style=" display: table-cell; white-space: pre-line; padding: 10px">
                    <strong>{{ $ready->getId() }}</strong> {{ trim($ready->getData()) }}
                </pre>
            </section>
            @endif

        </div>

    </div>


</body>
</html>
