<?php

namespace App\Utils\Wechat;
/**
 * Class ScheduleMessage
 * @package App\Utils\Wechat
 *
 * 预约项目{thing1.DATA}}
 * 预约时间{character_string2.DATA}}
 * 行程地点{thing3.DATA}}
 * 行程提醒{thing4.DATA}}
 * 备注{thing6.DATA}}
 */
class ScheduleMessage extends Message
{
    public $template_id = 'kmwgP02wuHK7japL4NgoLIszxfIlIs9tRtPa1-bMLZc';
    public $touser = '';
    public $page = '';
    public $data = [
        'thing1' => [
            'value' => ''
        ],
        'character_string2' => [
            'value' => ''
        ],
        'thing3' => [
            'value' => ''
        ],
        'thing4' => [
            'value' => ''
        ],
        'thing6' => [
            'value' => ''
        ]
    ];

    /**
     * @param $obj_name
     * @param $note
     * 预约项目{thing1.DATA}}
     * 预约时间{character_string2.DATA}}
     * 行程地点{thing3.DATA}}
     * 行程提醒{thing4.DATA}}
     * 备注{thing6.DATA}}
     */
    public function setData($obj_name, $appoint_date_at,$appoint_addr,$tip,$note)
    {
        $this->data['thing1']['value'] = $obj_name;
        $this->data['character_string2']['value'] = $appoint_date_at;
        $this->data['thing3']['value'] = $appoint_addr;
        $this->data['thing4']['value'] = $tip;
        $this->data['thing6']['value'] = $note;
    }

}
