<?php

namespace App\Console\Commands\Cron;

use App\Utils\Wechat\AppointmentMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Config;

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
        $notify->setTemplateId(config('miniWechat.message.appoint'));
        //账单通知
//        $notify->setTemplateId('6agnykuZddRbPjnMSWrZD0iecg32D7kWaMYmD8bOmho');
//        $notify->setTemplateId('6agnykuZddRbPjnMSWrZD0iecg32D7kWaMYmD8bOmho');
        $notify->setToUser('ojmpP44ox2nyiktlVLynsTpK8dN8');
        $notify->setData('dd','2022-1-2 3:12:23','test','ddd');
        $data = $notify->getData();
        $app = app('easyWechat');
        $res = $app->subscribe_message->send($data);
        Log::info($res);
    }
}
