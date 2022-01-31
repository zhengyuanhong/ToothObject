<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Utils\ErrorCode;
use Illuminate\Http\Request;

class AdController extends Controller
{
    public function oneAdArticle(Request $request)
    {
        if (!$id = $request->get('id', '')) throw new InvalidRequestException('网络延迟');
        return $this->reponseJson(ErrorCode::SUCCESS, Ad::query()->find($id)->toArray());
    }

    public function oneAd(){
        return $this->reponseJson(ErrorCode::SUCCESS,Ad::getRandomAd());
    }
}
