<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppointRecordResource extends JsonResource
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
            'id'=>$this->id,
            'obj_name' => $this->obj_name,
            'type' => $this->type,
            'appoint_status' => $this->appoint_status,
            'appoint_addr' => empty($this->appoint_addr) ? '**' : $this->appoint_addr,
            'appoint_date_at' => $this->appoint_date_at,
            'sale_user_id' => $this->sale_user_id,
        ];
    }
}
