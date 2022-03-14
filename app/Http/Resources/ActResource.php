<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'content' => $this->content,
            'address' => $this->address,
            'geo_code' => $this->geo_code,
            'company_id' => $this->company_id,
            'user_id' => $this->user_id,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'distance' => $this->get_distance($request->get('lat'), $request->get('lon'), $this->lat, $this->lon)
        ];
    }

    public function getTag($arr)
    {
        $data = [];
        foreach ($arr as $k => $value) {
            $data[] = $value;
        }
        return $data;
    }

    function get_distance($lat1, $lng1, $lat2, $lng2)
    {

        // 将角度转为狐度
        $radLat1 = deg2rad($lat1);// deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);

        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;

        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378137;

        if ($s >= 1000) {
            return round($s / 1000, 1) . 'km';
        }
        if ($s < 1000) {
            return round($s, 1) . 'm';
        }
    }
}
