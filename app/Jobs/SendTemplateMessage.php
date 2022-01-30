<?php

namespace App\Jobs;

use App\Models\AppointRecord;
use App\Services\MessageService;
use App\Utils\Wechat\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTemplateMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $message;
    public $appointRecord;
    public $type;
    public $templateId;

    /**
     * Create a new job instance.
    sendMessage($user,Message $message, AppointRecord $appointRecord, $type, $templateId)
     * @param $user
     * @param Message $message
     * @param AppointRecord $appointRecord
     * @param $type
     * @param $templateId
     * @return void
     */
    public function __construct($user,Message $message,AppointRecord $appointRecord,$type,$templateId)
    {
        $this->user = $user;
        $this->message = $message;
        $this->appointRecord = $appointRecord;
        $this->type = $type;
        $this->templateId = $templateId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
//        if(\App\Models\Message::query()->where('type',$this->type)->count() >= 2) return;
        app(MessageService::class)->sendMessage($this->user,$this->message,$this->appointRecord,$this->type,$this->templateId);
    }
}
