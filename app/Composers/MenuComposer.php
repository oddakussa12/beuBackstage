<?php
namespace App\Composers;

//use App\Models\Menu;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Repositories\Contracts\MenuRepository;

class MenuComposer
{

    protected $menu;

    protected $request;

    public function __construct(Request $request , MenuRepository $menu)
    {
        $this->menu = $menu;
        $this->request = $request;
    }

    /**
     * Bind data to the view.
     *
     * @param  Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $menus = $this->menu->all();

        $native_menus = $menus->toArray();

        $treeMenu = $this->getMenuTree($menus , 'menu_format_name' , 'menu_name' , 'menu_p_id' , 'menu_id' , 'menu_level' , 0 , 0 , '', $menuPath);

        $urlPath = $this->request->path();
        $urlPath = explode('/' , $urlPath);
        if(locale()==$urlPath[0])
        {
            unset($urlPath[0]);
        }
        $urlPath = '/'.join('/' , $urlPath);
        $menu_path = array();
        if(!empty($menuPath)&&is_array($menuPath)&&array_key_exists($urlPath , $menuPath))
        {
            $menu_path = explode('_' , $menuPath[$urlPath]);
        }
        $debug = env('APP_LOG_LEVEL')=='debug';
        $view->with(array('all_menu'=>$treeMenu ,'menu_path'=>$menu_path , 'native_menus'=>$native_menus , 'debug'=>$debug));
    }

    private function getMenuTree($menus, $showFName, $titleFName, $pidFName = 'pid', $idFName = 'id', $levelFName = 'level', $pid = 0, $level = 0  , $menu_path='' , &$menuPath=array())
    {
        $tree = array();
        foreach ($menus as $key => $value) {
            if ($value[$pidFName] == $pid) {
                $value[$levelFName] = $level;
                $value[config('request.menu_path')] = empty($menu_path)?$value[$idFName]:$menu_path."_".$value[$idFName];
                $value[$showFName] = str_repeat('&nbsp;&nbsp;', $level) . '|-' . $value[$titleFName];
                $menuPath[$value['menu_url']] = $value[config('request.menu_path')];
                unset($menus[$key]);
                $tempArr = $this->getMenuTree($menus, $showFName, $titleFName, $pidFName, $idFName, $levelFName, $value[$idFName], $level + 1 , $value['menu_path'] , $menuPath);
                if(!empty($tempArr)){
                    $value['child'] = $tempArr;
                }
                $tree[$value[$idFName]] = $value;
            }
        }
        return $tree;
    }
}