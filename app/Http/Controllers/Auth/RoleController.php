<?php

namespace App\Http\Controllers\Auth;


use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Repositories\Contracts\RoleRepository;
use App\Repositories\Contracts\PermissionRepository;

class RoleController extends Controller
{

    public function __construct(RoleRepository $role)
    {
        $this->role = $role;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $roles = $this->role->all();
        $permission = resolve(PermissionRepository::class);
        $sortPermissions = $permission->getSortPermissions();
        return view('backstage.role.index' ,compact('roles' , 'sortPermissions'));
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
    public function store(StoreRoleRequest $request)
    {
        $role_fields = $request->only(['name']);
        return $this->role->store($role_fields)->givePermissionTo($request->only('role_auth'));
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
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $fields = $request->only(['name']);
        return $this->role->update($role, $fields)->syncPermissions($request->only('role_auth'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        //
        $this->role->destroy($role);
        return response()->json([
            'result' => 'success',
        ]);
    }
}
