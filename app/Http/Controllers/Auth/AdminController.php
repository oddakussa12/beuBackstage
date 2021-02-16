<?php

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminRequest;
use App\Repositories\Contracts\AdminRepository;
use App\Repositories\Contracts\PermissionRepository;

class AdminController extends Controller
{

    public function __construct(AdminRepository $admin)
    {
        $this->admin = $admin;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $role = new Role();
        $roles = $role->all();
        $permission = resolve(PermissionRepository::class);
        $sortPermissions = $permission->getSortPermissions();
        $admins = $this->admin->paginate(6);
        foreach ($admins as $k=>$admin)
        {
            $permissions = array();
            foreach ($admin->getAllPermissions() as $permission)
            {
                array_push($permissions , $permission['id']);
            }
            $admins[$k]['admin_permissions'] = json_encode($permissions);
            $admins[$k]['admin_roles'] = json_encode($admin->getRoleNames());
        }
        return view('backstage.admin.index' , compact('admins' , 'sortPermissions' , 'roles'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAdminRequest $request)
    {
        $admin_fields = $request->all();
        $admin_fields[$this->admin->getDefaultPasswordField()] = password_hash(config('common.default_password') , PASSWORD_DEFAULT);
        return $this->admin->store($admin_fields)->syncRoles($request->input('admin_roles'))->syncPermissions($request->input('admin_auth'));
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Admin $admin , Request $request)
    {
        $fields = $request->except(['admin_id' , 'admin_email' , 'layuiTreeCheck']);
        return $this->admin->update($admin, $fields)->syncRoles(explode(',' , $request->input('admin_roles')))->syncPermissions($request->input('admin_auth'));
//        $admin->(['edit articles', 'delete articles']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Admin $admin)
    {
        //
        $this->admin->destroy($admin);
        return response()->json([
            'result' => 'success',
        ]);
    }
}
