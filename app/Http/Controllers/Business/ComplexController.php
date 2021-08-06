<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ComplexController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(Request $request)
    {
        $locale = locale();
        $type = $request->input('type' , 'shop_order');
        $promo_code = $request->input('promo_code' , '');
        $iframes = array(
            'shop_order'=>"/{$locale}/backstage/business/shop_order?promo_code={$promo_code}",
            'delivery_order'=>"/{$locale}/backstage/business/delivery_order",
            'shopping_cart'=>"/{$locale}/backstage/business/shopping_cart",
        );
        $type = isset($iframes[$type])?$type:'shop_order';
        $iframe = $iframes[$type];
        return view('backstage.business.complex.index'  , compact('type' , 'iframe' , 'iframes' , 'promo_code'));
    }

}
