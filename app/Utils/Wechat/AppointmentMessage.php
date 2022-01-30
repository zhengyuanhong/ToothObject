<?php

namespace App\Utils\Wechat;
/** * Created by PhpStorm.
 * User: zheng
 * Date: 2022/1/29
 * Time: 10:19
 */
class AppointmentMessage extends Message
{
    public $template_id = 'QEn7cF3QOpjGDDyO8AZCXFevxERJhWMH7_aO8MUr6Cs';
    public $touser = '';
    public $page = '';
    public $data = [
        'name1' => [
            'value' => ''
        ],
        'date3' => [
            'value' => ''
        ],
        'thing2' => [
            'value' => ''
        ],
        'thing7' => [
            'value' => ''
        ]
    ];

    public function setData($user_name, $appoint_date, $appoint_address, $note)
    {
        $this->data['name1']['value'] = $user_name;
        $this->data['date3']['value'] = $appoint_date;
        $this->data['thing2']['value'] = $appoint_address;
        $this->data['thing7']['value'] = $note;
    }
}
