<?php

namespace App\Jobs;

class ExampleJob extends Job
{

    protected $param;

    public $queue =  "testData";


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($param)
    {
        //
        $this->param = $param;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        var_dump($this->param);
    }
}
