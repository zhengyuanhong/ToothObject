<?php

namespace App\Console\Commands\Cron;

use App\Jobs\SendTemplateMessage;
use App\Models\Message;
use App\Models\WechatUser;
use App\Services\MessageService;
use App\Models\AppointRecord;
use App\Utils\Wechat\AppointmentMessage;
use App\Utils\Wechat\ScheduleMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class AppointmentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'z_notify:appointment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '预约提醒通知';

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
     * @param $messageService
     * @return int
     */
    public function handle(MessageService $messageService)
    {
        AppointRecord::query()
            ->where('is_cancel', AppointRecord::IS_CANCEL['NO'])
            ->chunkById(100, function ($items) use ($messageService) {
                foreach ($items as $item) {
                    //现在的时间和预约的时间比较
                    $dua = Carbon::now()->diffInHours($item->appoint_date_at);
                    if (Carbon::now()->gt(Carbon::parse($item->appoint_date_at)) && $dua > 0) {
                        Log::info('超过' . $dua . '小时' . $item);
                        $this->cancelAppointment($item);
                    } else if ($dua >= 0 && $dua <= 1) {
                        SendTemplateMessage::dispatch($item->user,new ScheduleMessage(), $item, 'schedule', config('miniWechat.message.schedule'));
                        Log::info('还差' . $dua . '到期，订阅消息提醒');
                    }
                }
            });
    }

    public function cancelAppointment(AppointRecord $appointRecord)
    {
        $appointRecord->update(['is_cancel' => AppointRecord::IS_CANCEL['YES']]);
    }
}
