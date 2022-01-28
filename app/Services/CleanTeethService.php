<?php

namespace App\Services;

use App\Models\CleanTeeth;
use Illuminate\Support\Carbon;

class CleanTeethService
{
    public function updateData($input)
    {
        CleanTeeth::query()->where('clean_tooth_date', $input['clean_tooth_date'])->update(['appoint_content' => $input['appoint_content']]);
    }

    public function createData($data){
        $teeth_data = CleanTeeth::query()->where('clean_tooth_date', $data)->first();
        if (empty($teeth_data)) {
            $teeth_data = new CleanTeeth();
            $teeth_data->clean_tooth_date = $data;
            $teeth_data->appoint_content = $this->getDefaultData();
            $teeth_data->save();
        }
        return $teeth_data;
    }

    public function getDefaultData()
    {
        return [
            ['id' => 1, 'time' => '9:00', 'head' => 1, 'is_appoint' => 0],
            ['id' => 2, 'time' => '10:00', 'head' => 1, 'is_appoint' => 0],
            ['id' => 3, 'time' => '11:00', 'head' => 1, 'is_appoint' => 0],
            ['id' => 4, 'time' => '12:00', 'head' => 1, 'is_appoint' => 0],
            ['id' => 5, 'time' => '13:00', 'head' => 1, 'is_appoint' => 0],
            ['id' => 6, 'time' => '14:00', 'head' => 1, 'is_appoint' => 0],
            ['id' => 7, 'time' => '15:00', 'head' => 1, 'is_appoint' => 0],
            ['id' => 8, 'time' => '16:00', 'head' => 1, 'is_appoint' => 0],
            ['id' => 9, 'time' => '17:00', 'head' => 1, 'is_appoint' => 0],
            ['id' => 10, 'time' => '18:00', 'head' => 1, 'is_appoint' => 0],
        ];
    }

    public function getDateTime()
    {
        $dt = Carbon::now();
        $year = $dt->year;
        $month = $dt->month;
        $current_day = $dt->day;
        $current_month_last_day = $dt->lastOfMonth()->day;

        $date_text = [];
        $date = [];
        $week = [];
        $current_date = $month . '-' . $current_day;
        for ($day = $current_day; $day <= $current_month_last_day; $day++) {
            $date_text[] = $month . '月' . $day . '日';
            $date[] = $month . '-' . $day;
            $week[] = num_to_text(Carbon::createFromDate($year, $month, $day)->dayOfWeek);
        }

        return ['date_text' => $date_text, 'date' => $date, 'week' => $week, 'current_date' => $current_date];
    }
}
