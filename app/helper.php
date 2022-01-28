<?php

use Carbon\Carbon;

/**
 * Created by PhpStorm.
 * User: zheng
 * Date: 2022/1/22
 * Time: 0:43
 */

function make_time($time)
{
    $dt = Carbon::now();
    return Carbon::parse($dt->year . '-' . $time);
}


function num_to_text($num)
{
    $text_arr = [
        0 => '日',
        1 => '一',
        2 => '二',
        3 => '三',
        4 => '四',
        5 => '五',
        6 => '六',
    ];

    return $text_arr[$num];
}

function make_name()
{
    return 'T' . time();
}

function make_number($num)
{
    return 'NO.' . $num;
}

function getStrTime($no)
{
    if ($no > 0 && $no <= 6) {
        return "凌晨";
    }
    if ($no > 6 && $no < 12) {
        return "上午";
    }
    if ($no >= 12 && $no <= 18) {
        return "下午";
    }
    if ($no > 18 && $no <= 24) {
        return "晚上";
    }
}
