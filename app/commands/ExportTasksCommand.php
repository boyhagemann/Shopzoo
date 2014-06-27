<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;


/**
 * Class ExportTasks
 */
class ExportTasksCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'export:tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export all tasks to the main taskreward application';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        // We need to manually build the queue here, because we need to set
        // a custom priority
        $payload = json_encode(array(
            'job' => 'ExportTasks',
            'data' => array(),
        ));

        Queue::getPheanstalk()->useTube('default')->put(
            $payload,
            Pheanstalk_PheanstalkInterface::DEFAULT_PRIORITY - 500,
            Pheanstalk_PheanstalkInterface::DEFAULT_DELAY
        );
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array();
    }
}