<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\TeethDetail;
use App\Utils\ErrorCode;
use Illuminate\Http\Request;

class AdController extends Controller
{
    public function oneAdArticle(Request $request)
    {
        if (!$id = $request->get('id', '')) throw new InvalidRequestException('网络延迟');
        return $this->reponseJson(ErrorCode::SUCCESS, Ad::query()->find($id)->toArray());
    }

    public function oneAd(Request $request)
    {
        return $this->reponseJson(ErrorCode::SUCCESS, Ad::getRandomAd($request->get('company_id')));
    }

    public function teethObjDetail(Request $request)
    {
        return $this->reponseJson(ErrorCode::SUCCESS, TeethDetail::detail($request->get('type')));
    }
}
