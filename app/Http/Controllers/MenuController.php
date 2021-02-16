<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 2019/5/25
 * Time: 17:06
 */

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Http\Requests\CreateMenuRequest;
use App\Repositories\Contracts\MenuRepository;
use App\Http\Controllers\Controller as BaseController;

class MenuController extends BaseController
{
    public function __construct(MenuRepository $menu)
    {
        $this->menu = $menu;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backstage.menu.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {

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
        return $this->menu->store($menu_fields);
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
     * @param  App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request , Menu $menu)
    {
        return $this->menu->update($menu, $request->except('menu_id'));
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