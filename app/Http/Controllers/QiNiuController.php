<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 2019/5/25
 * Time: 17:06
 */

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller as BaseController;

class QiNiuController extends BaseController
{
    public function bundleToken()
    {
        return $this->getToken('qn_eventsource');
    }

    private function getToken($drive='qn_default' , $key = null, $expires = 3600, $policy = null, $strictPolice = null)
    {
        $config = config('filesystems.disks.'.$drive);
        $name = "$(etag)$(ext)";
        $url = $config['domain'];
        $disk = Storage::disk($drive);
        $policy = $policy==null?[
            'saveKey'=>"$(etag)$(ext)",
//            'mimeLimit'=>'image/*',
            'forceSaveKey'=>true,
            'returnBody'=>"{\"name\": \"$name\", \"hash\": \"$(etag)\",\"size\": \"$(fsize)\",\"url\":\"$url\"}"
        ]:$policy;
        return array('token'=>$disk->getUploadToken($key , $expires , $policy , $strictPolice) , 'domain'=>config('filesystems.disks.'.$drive.'.domain'));
    }


}