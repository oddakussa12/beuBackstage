<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 2019/5/25
 * Time: 17:06
 */

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Services\TranslationsService;
use App\Http\Requests\UpdateTranslationRequest;
use App\Http\Controllers\Controller as BaseController;

class TranslationController extends BaseController
{
    /**
     * TranslationController constructor.
     * @param TranslationsService $translation
     */
    public function __construct(TranslationsService $translation)
    {
        $this->translation = $translation;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $translations = $this->translation->getFileAndDatabaseMergedTranslations();
        return view('backstage.translation.index' , compact('translations'));
    }


    /**
     * @param CreateMenuRequest $request
     * @return mixed
     */
    public function create(CreateMenuRequest $request)
    {
        //
        $menu_fields = $request->only(['menu_name', 'menu_url' , 'menu_auth' , 'menu_p_id']);
        return $this->translation->create($menu_fields);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMenuRequest $request)
    {
        $menu_fields = $request->all();
       // dd($menu_fields);
        return $this->menu->create($menu_fields);
//        $menu = $this->menu->find($request->input('menu_id'));
//        $menu_fields = $request->only(['menu_name', 'menu_url' , 'menu_auth' , 'menu_p_id']);
//        return $this->menu->update($menu , $menu_fields);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTranslationRequest $request , $key)
    {
        return $this->translation->saveTranslationForLocaleAndKey(
            $request->input('locale'),
            $key,
            $request->input('translation_value')
        );
        //return $this->translation->updateTranslationToValue($translationTranslation, $request->get('oldValue'));
        //return $this->menu->update($menu, $request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function destroy(Menu $menu)
    {
        $sub_menu = $this->menu->findByAttributes(['menu_p_id'=>$menu->menu_id]);
        if($sub_menu)
        {
            abort(403, trans('menu.confirm.prompt.delete'));
        }
        $this->menu->destroy($menu);
        return response()->json([
            'result' => 'success',
        ]);
    }
}