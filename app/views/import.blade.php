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
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-success">{{{ Session::get('success') }}}</div>
            </div>
        </div>
        @endif

        <div class="row">

            <div class="col-lg-5">

                <h2>Import</h2>
                <p>All the imports will create seperate queued jobs in the background.</p>

                <h4>Info</h4>
                <p>Gather commission information and other useful data from all campaigns.</p>
                <p>{{ HTML::link('info', 'Update campaign info', ['class' => 'btn btn-primary btn-sm']) }}</p>

                <h4>Product feeds</h4>
                <p>Import the product feeds for these campaigns.</p>
                <p>{{ HTML::link('import', 'Import all campaigns', ['class' => 'btn btn-warning btn-sm']) }}</p>
                <p>{{ HTML::link('import/2626', 'Afvalemmershop.nl', ['class' => 'btn btn-default btn-sm']) }}</p>
                <p>{{ HTML::link('import/867', 'Bestelkado.nl', ['class' => 'btn btn-default btn-sm']) }}</p>
                <p>{{ HTML::link('import/1078', 'Algebeld.nl', ['class' => 'btn btn-default btn-sm']) }}</p>

            </div>

            <div class="col-lg-5">

                <h2>Export</h2>
                <p>This will export the data to the taskreward application</p>

                <h4>Tasks</h4>
                <p>
                    Export all tasks that are ready.
                </p>
                <p>
                    {{ HTML::link('export', 'Export', ['class' => 'btn btn-primary btn-sm']) }}
                </p>

                <h4>Clicks</h4>
                <p>
                    Get the clicks registered from the affiliate parties and export them to the taskreward application.
                </p>
                <p>
                    {{ HTML::link('clicks', 'Export clicks', ['class' => 'btn btn-primary btn-sm']) }}
                </p>

            </div>

            <div class="col-lg-2">

                <h2>Quick</h2>

                <p>{{ HTML::link(Config::get('services.taskreward.homepage'), 'Go to TaskReward', ['target' => '_blank']) }}</p>
                <hr>
                <p>{{ HTML::link('quick/ready', 'Mark all tasks as ready') }}</p>
                <p>{{ HTML::link('quick/refresh', 'Refresh database') }}</p>
                <p>{{ HTML::link('quick/clear-failed-job', 'Clear failed jobs log') }}</p>
                <p>{{ HTML::link('quick/clear-log-files', 'Clear log files') }}</p>

            </div>

        </div>

        <div class="row">

            <div class="col-lg-12">

                <hr>


                @if($ready)

                <h2>Queue <span class="badge badge-danger">{{ $stats['current-jobs-ready'] }}</span></h2>

                <h4>Next job</h4>
                <section>
                    <pre style=" display: table-cell; white-space: pre-line; padding: 10px">
                        <strong>{{ $ready->getId() }}</strong> {{ trim($ready->getData()) }}
                    </pre>
                </section>


                @else

                <h2>Queue <span class="badge">0</span></h2>

                <p>There are no jobs on the queue</p>

                @endif

                @if($failed->count())

                <h4>Failed jobs</h4>

                <table class="table">
                    @foreach($failed as $job)
                    <tr>
                        <td>{{ $job->failed_at }}</td>
                        <td>{{ $job->connection }}</td>
                        <td>{{ $job->queue }}</td>
                        <td><pre>{{ $job->payload }}</pre></td>
                    </tr>
                    @endforeach
                </table>

                {{ $failed->links() }}


                @endif




                <h2>Last logs</h2>
                @foreach($logs as $log)
                <li>{{ $log }}</li>
                @endforeach

            </div>

        </div>

    </div>


</body>
</html>
