<?php

namespace App\Console;

use App\Console\Commands\CancelOrders;
use App\Console\Commands\Demo;
use App\Console\Commands\DemoCommand;
use App\Console\Commands\HandleDivide;
use App\Console\Commands\YeepayTest;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //demo任务注册
        DemoCommand::class,
        demo::class,
        YeepayTest::class,
        Demo::class,
        CancelOrders::class,
        HandleDivide::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //每分钟执行， 单任务执行  withoutOverlapping 避免任务重复#
        $schedule->command('command:DemoCommand')->everyMinute()->withoutOverlapping();
    }
}
