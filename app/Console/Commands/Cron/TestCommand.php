<?php

namespace App\Console\Commands\Cron;

use App\Utils\Wechat\AppointmentMessage;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'z:t';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $notify = new AppointmentMessage();
        $notify->setPage('/pages/index/index');
        //留言通知
        $notify->setTemplateId('kmwgP02wuHK7japL4NgoLIszxfIlIs9tRtPa1-bMLZc');
        //账单通知
        // kmwgP02wuHK7japL4NgoLIszxfIlIs9tRtPa1-bMLZc
        // QEn7cF3QOpjGDDyO8AZCXFevxERJhWMH7_aO8MUr6Cs
//        $notify->setTemplateId('6agnykuZddRbPjnMSWrZD0iecg32D7kWaMYmD8bOmho');
//        $notify->setTemplateId('6agnykuZddRbPjnMSWrZD0iecg32D7kWaMYmD8bOmho');
        $notify->setToUser('oiFuG5EyCouXvc615E2zkCGut1Ag');
        $notify->setData(400,'郑远航','test','ddd');
        $data = $notify->getData();
        $app = app('easyWechat');
        $res = $app->subscribe_message->send($data);
    }
}
