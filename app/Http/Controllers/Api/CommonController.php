<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Utils\ErrorCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CommonController extends Controller
{
    public function uploadImg(Request $request)
    {
        if (empty($request->file('img'))) {
            throw new InvalidRequestException('请选择图片上传');
        }
        $path = $request->file('img')->store('users', 'public');

        $img_url = env('APP_URL') . 'storage/' . $path;
        return $img_url;
    }

    public function delImg(Request $request)
    {
        if (empty($filename = $request->get('filename'))) {
            throw new InvalidRequestException('删除失败');
        }
        Log::info('filename:'.$filename);
        if (!Storage::exists($filename)) {
            throw new InvalidRequestException('文件不存在');
        }
        Storage::delete($filename);
        return $this->reponseJson(ErrorCode::SUCCESS);
    }
}
