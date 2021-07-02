<?php

namespace App\Http\Controllers\Business;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use App\Models\Business\ShopTag;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Business\ShopTagTranslation;

class ShopTagController extends Controller
{
    public function index(Request $request)
    {
        $params = $request->all();
        $shopTags = ShopTag::orderByDesc('created_at')->paginate(10);
        $shopTagIds = $shopTags->pluck('id')->toArray();
        $shopTagTranslations = ShopTagTranslation::whereIn('tag_id' , $shopTagIds)->get();
        $shopTags->each(function ($shopTag) use ($shopTagTranslations){
            $shopTagTranslations = $shopTagTranslations->where('tag_id' , $shopTag->id);
            foreach ($shopTagTranslations as $shopTagTranslation)
            {
                $shopTag->{$shopTagTranslation->locale} = $shopTagTranslation->tag_content;
            }
        });
        $params['shopTags'] = $shopTags;
        return view('backstage.business.shop_tag.index', $params);

    }

    public function store(Request $request)
    {
        $fields = array();
        $params = $request->all();
        $supportedLocales = \LaravelLocalization::getSupportedLocales();
        foreach ($supportedLocales as $localeCode => $properties)
        {
            if(isset($params[$localeCode.'_tag_content']))
            {
                $fields[$localeCode] = $params[$localeCode.'_tag_content'];
            }
        }
        if(empty($fields))
        {
            abort(422 , 'Parameter error!');
        }
        $this->validate($request, [
            'tag'=> 'required|string|max:32|regex:/^[a-zA-Z0-9]{2,16}$/',
        ]);
        $tag = $request->input('tag');
        $shopTag = ShopTag::where('tag' , $tag)->first();
        if(!empty($shopTag))
        {
            abort(422 , 'Tag cannot be repeated!');
        }
        $id = Uuid::uuid1()->toString();
        $now = date('Y-m-d H:i:s');
        $data = array();
        foreach ($fields as $locale=>$field)
        {
            array_push($data , array(
                'id'=>Uuid::uuid1()->toString(),
                'tag_id'=>$id,
                'locale'=>$locale,
                'tag_content'=>$field,
            ));
        }
        $connection = DB::connection('lovbee');
        try{
            $connection->beginTransaction();
            $connection->table('shops_tags')->insert(array(
                'id'=>$id,
                'tag'=>$tag,
                'created_at'=>$now,
                'updated_at'=>$now,
            ));
            $connection->table('shops_tags_translations')->insert($data);
            $connection->commit();
        }catch (\Exception $e)
        {
            $connection->rollBack();
            Log::info('tag_insert_fail' , array(
                'message'=>$e->getMessage(),
                'data'=>$request->all(),
            ));
            abort(500 , 'Failed to add tag!');
        }
        $this->clear();
        return response()->json(['result'=>'success']);
    }


    public function update(Request $request, $id)
    {
        $fields = array();
        $params = $request->all();
        $supportedLocales = \LaravelLocalization::getSupportedLocales();
        foreach ($supportedLocales as $localeCode => $properties)
        {
            if(isset($params[$localeCode.'_tag_content']))
            {
                $fields[$localeCode] = $params[$localeCode.'_tag_content'];
            }
        }
        if(empty($fields))
        {
            abort(422 , 'Parameter error!');
        }
        $this->validate($request, [
            'tag'=> 'required|string|max:32|regex:/^[a-zA-Z0-9]{2,16}$/',
        ]);
        $tag = $request->input('tag');
        $shopTag = ShopTag::where('id' , $id)->firstOrFail();
        $shopTag->tag = $tag;
        $shopTag->save();
        foreach ($fields as $locale=>$field)
        {
            ShopTagTranslation::where('tag_id' , $id)->where('locale' , $locale)->update(
                array(
                    'tag_content'=>$field,
                )
            );
        }
        $this->clear();
        return response()->json(['result'=>'success']);
    }

    private function clear()
    {

    }

}
