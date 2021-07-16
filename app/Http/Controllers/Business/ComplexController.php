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
        $type = $request->input('type' , 'shop_order');
        $iframes = array(
            'shop_order'=>"/backstage/business/shop_order",
            'delivery_order'=>"/backstage/business/delivery_order",
            'shopping_cart'=>"/backstage/business/shopping_cart",
        );
        $type = isset($iframes[$type])?$type:'shop_order';
        $iframe = $iframes[$type];
        return view('backstage.business.complex.index'  , compact('type' , 'iframe' , 'iframes'));
    }

}
