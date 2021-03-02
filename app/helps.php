<?php
if (! function_exists('locale')) {
    function locale($locale = null)
    {
        if (is_null($locale)) {
            return app()->getLocale();
        }
        app()->setLocale($locale);
        return app()->getLocale();
    }
}

if(!function_exists('qnToken')){
    function qnToken($drive='qn_default' , $key = null, $expires = 3600, $policy = null, $strictPolice = null)
    {
        $config = config('filesystems.disks.'.$drive);
        $name = "$(etag)$(ext)";
        $url = $config['domain'];
        $disk = Storage::disk($drive);
        $policy = $policy==null?[
            'saveKey'=>"$(etag)$(ext)",
//            'mimeLimit'=>'image/*',
            'forceSaveKey'=>true,
            'returnBody'=>"{\"name\": \"$name\", \"hash\": \"$(etag)\", \"w\": $(imageInfo.width),\"h\": $(imageInfo.height),\"size\": \"$(fsize)\",\"url\":\"$url\"}"
        ]:$policy;
        return array('token'=>$disk->getUploadToken($key , $expires , $policy , $strictPolice) , 'domain'=>config('filesystems.disks.'.$drive.'.domain'));
    }
}

if(!function_exists('qnBundleToken')){
    function qnBundleToken($drive='qn_default' , $key = null, $expires = 3600, $policy = null, $strictPolice = null)
    {
        $config = config('filesystems.disks.'.$drive);
        $name = "$(etag)$(ext)";
        $url = $config['domain'];
        $disk = Storage::disk($drive);
        $policy = $policy==null?[
            'saveKey'=>"$(etag)$(ext)",
//            'mimeLimit'=>'image/*',
            'forceSaveKey'=>true,
            'returnBody'=>"{\"name\": \"$name\", \"hash\": \"$(etag)\", \"size\": \"$(fsize)\",\"url\":\"$url\"}"
        ]:$policy;
        return array('token'=>$disk->getUploadToken($key , $expires , $policy , $strictPolice) , 'domain'=>config('filesystems.disks.'.$drive.'.domain'));
    }
}

if (! function_exists('common_signature')) {
    function common_signature(&$params)
    {
        if (!isset($params['time_stamp'])) {
            $params['time_stamp'] = time();
        }
        // 1. 字典升序排序
        ksort($params);

        // 2. 拼按URL键值对
        $str = '';
        foreach ($params as $key => $value)
        {
            if ($value !== '')
            {
                $str .= $key . '=' . $value . '&';
            }
        }
        // 3. 拼接app_key
        $app_key = config('common.common_secret');
        $str .= 'app_key=' . $app_key;
        // 4. MD5运算+转换大写，得到请求签名

        return strtolower(md5($str));
    }
}

if (! function_exists('block_user_list')) {

    function block_user_list()
    {
        $users = array();
        $filePath = 'block/users.json';
        if(\Storage::exists($filePath))
        {
            $users = (array)\json_decode(\Storage::get($filePath) , true);
        }
        return $users;
    }
}
if (! function_exists('essence_post_list')) {

    function essence_post_list()
    {
        $posts = array();
        $filePath = 'essence/posts.json';
        if(\Storage::exists($filePath))
        {
            $posts = (array)\json_decode(\Storage::get($filePath) , true);
        }
        return $posts;
    }
}


if (! function_exists('essence_post')) {

    function essence_post($id)
    {
        $posts = array();
        $filePath = 'essence/posts.json';
        if(\Storage::exists($filePath))
        {
            $posts = (array)\json_decode(\Storage::get($filePath) , true);
            $keys = array_keys($posts);
            if(!in_array($id , $keys))
            {
                $posts[$id] = time();
            }
        }else{
            $posts[$id] = time();
        }
        \Storage::put($filePath , \json_encode($posts , JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        return $posts;
    }
}

if (! function_exists('non_essence_post')) {

    function non_essence_post($id=null)
    {
        $res = false;
        $posts = array();
        $filePath = 'essence/posts.json';
        if(\Storage::exists($filePath))
        {
            $posts = (array)\json_decode(\Storage::get($filePath) , true);
            if(array_key_exists($id , $posts))
            {
                unset($posts[$id]);
                $res = true;
            }
        }
        \Storage::put($filePath , \json_encode($posts , JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        return $res;
    }
}

if (! function_exists('block_user')) {

    function block_user($id)
    {
        $users = array();
        $filePath = 'block/users.json';
        if(\Storage::exists($filePath))
        {
            $users = (array)\json_decode(\Storage::get($filePath) , true);
            $keys = array_keys($users);
            if(!in_array($id , $keys))
            {
                $users[$id] = time();
            }
        }else{
            $users[$id] = time();
        }
        \Storage::put($filePath , \json_encode($users , JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        return $users;
    }
}

if (! function_exists('unblock_user')) {

    function unblock_user($id=null)
    {
        $res = false;
        $users = array();
        $filePath = 'block/users.json';
        if(\Storage::exists($filePath))
        {
            $users = (array)\json_decode(\Storage::get($filePath) , true);
            if(array_key_exists($id , $users))
            {
                unset($users[$id]);
                $res = true;
            }
        }
        \Storage::put($filePath , \json_encode($users , JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        return $res;
    }
}

if (! function_exists('carousel_post')) {

    function carousel_post($id , $locale , $image)
    {
        $posts = array();
        $filePath = 'carousel/posts.json';
        if(\Storage::exists($filePath))
        {
            $posts = (array)\json_decode(\Storage::get($filePath) , true);
            $keys = array_keys($posts);
            if(!in_array($id , $keys))
            {
                $posts[$id] = array($locale=>$image);
            }else{
                $post = $posts[$id];
                $post[$locale] = $image;
                $posts[$id] = $post;
            }
        }else{
            $posts[$id] = array($locale=>$image);
        }
        \Storage::put($filePath , \json_encode($posts , JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        return $posts;
    }
}

if (! function_exists('non_carousel_post')) {

    function non_carousel_post($id)
    {
        $posts = array();
        $filePath = 'carousel/posts.json';
        if(\Storage::exists($filePath))
        {
            $posts = (array)\json_decode(\Storage::get($filePath) , true);
            $keys = array_keys($posts);
            if(in_array($id , $keys))
            {
                unset($posts[$id]);
            }
        }
        \Storage::put($filePath , \json_encode($posts , JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        return $posts;
    }
}

if (! function_exists('carousel_post_list')) {

    function carousel_post_list($id)
    {
        $post = array();
        $filePath = 'carousel/posts.json';
        if(\Storage::exists($filePath))
        {
            $posts = (array)\json_decode(\Storage::get($filePath) , true);
            if(array_key_exists($id , $posts))
            {
                $post = $posts[$id];
            }
        }
        return $post;
    }
}

if (! function_exists('front_url')) {

    function front_url($uri='')
    {
        return config('common.mt_front_domain').$uri;
    }
}


if (!function_exists('domain')) {
    /**
     * Calculates the rate for sorting by hot.
     *
     * @param null $domain
     * @param string $item
     * @return float
     */

    function domain($domain=null,$item='host')
    {
        if($domain==null){
            $url = parse_url(url()->current());
        }else{
            $url = parse_url($domain);
        }
        if(isset($url[$item]))
        {
            return $url[$item];
        }
        return '';

    }
}
/**
 * @param $type
 * @param $value
 * @return mixed
 * Post Model中 post_media
 */
if (!function_exists('postMedia')) {
    function postMedia($type, $value, $thumbModel=5)
    {
        $imgDomain     = config('common.qnUploadDomain.thumbnail_domain');
        $thumbDomain   = config('common.awsUploadDomain.thumbnail_domain');
        $videoDomain   = config('common.awsUploadDomain.video_domain');
        $videoDomainCn = config('common.awsUploadDomain.video_domain_cn');

        if ($type == 'video') {
            $value = gettype($value)=='array'?$value:\json_decode($value , true);
            $domain = domain() == 'api.mmantou.cn' ? $videoDomainCn : $videoDomain;

            $value[$type]['video_url'] = $domain . $value[$type]['video_url'];
            $value[$type]['video_thumbnail_url'] = $thumbDomain . $value[$type]['video_thumbnail_url'];
            $video_subtitle = (array)$value[$type]['video_subtitle_url'];
            $video_subtitle = \array_filter($video_subtitle, function ($v, $k) {
                return !empty($v) && !empty($k);
            }, ARRAY_FILTER_USE_BOTH);
            $value[$type]['video_subtitle_url'] = \array_map(function ($v) {
                return config('common.qnUploadDomain.subtitle_domain') . $v;
            }, $video_subtitle);
        } else if ($type == 'news') {
            $value = gettype($value)=='array'?$value:\json_decode($value , true);
            $value[$type]['news_cover_image'] = $imgDomain . $value[$type]['news_cover_image'];
        } else if ($type == 'image') {
            $value = gettype($value)=='array'?$value:\json_decode($value , true);
            $value[$type]['image_cover'] = $imgDomain . $value[$type]['image_cover'];
            $image_url = $value[$type]['image_url'];
            $value[$type]['image_url'] = \array_map(function ($v) use ($imgDomain) {
                return $imgDomain . $v . '?imageMogr2/auto-orient/interlace/1|imageslim';
            }, $image_url);
            $value[$type]['thumb_image_url'] = \array_map(function ($v) use ($imgDomain, $thumbModel) {
                return $imgDomain . $v . "?imageView2/$thumbModel/w/300/h/300/interlace/1|imageslim";
            }, $image_url);
        }
        return $value;
    }

}


/**
 * @return mixed|null
 * PHP 数组按多个字段排序
 */
if (!function_exists('sortArrByManyField')) {
    function sortArrByManyField()
    {
        $args = func_get_args(); // 获取函数的参数的数组
        if (empty($args)) {
            return null;
        }
        $arr = array_shift($args);
        if (!is_array($arr)) {
            return $arr;
        }
        foreach ($args as $key => $field) {
            if (is_string($field)) {
                $temp = array();
                foreach ($arr as $index => $val) {
                    $temp[$index] = $val[$field];
                }
                $args[$key] = $temp;
            }
        }
        $args[] = &$arr; //引用值
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }
}

