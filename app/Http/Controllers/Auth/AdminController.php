<?php

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\Admin;
use App\Mail\NewPassword;
use App\Mail\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminRequest;
use App\Custom\Uuid\RandomStringGenerator;
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
     * @throws \Throwable
     */
    public function index()
    {
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
        $countries = config('country');
        return view('backstage.admin.index' , compact('admins' , 'sortPermissions' , 'roles', 'countries'));

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
        $random = new RandomStringGenerator();
        $pwd = $random->generate(8);
        $admin_fields[$this->admin->getDefaultPasswordField()] = password_hash($pwd , PASSWORD_DEFAULT);
        $admin = $this->admin->store($admin_fields)->syncRoles($request->input('admin_roles'))->syncPermissions($request->input('admin_auth'));
        Mail::to($admin->admin_email)->send(new NewPassword($pwd));
        return response()->json(array(
            'result'=>'success'
        ));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function edit($id)
    {
        $user = auth()->user();
        if($id!=$user->admin_id)
        {
            abort(404);
        }
        return view('backstage.admin.edit' , compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Admin $admin
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Admin $admin , Request $request)
    {
        $fields = $request->except(['admin_id' , 'admin_email']);
        return $this->admin->update($admin, $fields)->syncRoles(explode(',' , $request->input('admin_roles')))->syncPermissions($request->input('admin_auth'));
    }

    public function updateSelf(Request $request)
    {
        $user   = auth()->user();
        $fields = $request->except(['admin_id' , 'admin_email']);
        if (!empty($fields['password']) && !empty($fields['new_password']) && !empty($fields['new_password'])) {
            if (!password_verify($fields['password'], $user['admin_password'])) {
                abort(422 , 'The original password is incorrect!');
            }
            if (strlen($fields['new_password'])<6 || $fields['new_password'] !== $fields['confirm_password']) {
                abort(422 , 'The two passwords are different!');
            }
            $newPass = password_hash($fields['new_password'] , PASSWORD_DEFAULT);
        }
        if (!empty($newPass)) {
            $fields['admin_password'] = $newPass;
            unset($fields['password'], $fields['new_password'], $fields['confirm_password']);
        }
        $this->admin->update($user, $fields);
        return response()->json(['result' => 'success']);
    }

    public function resetPwd($admin_id)
    {
        if(!auth()->user()->hasRole('administrator'))
        {
            abort(403);
        }
        $random = new RandomStringGenerator();
        $pwd = $random->generate(8);
        $admin = Admin::where('admin_id' , $admin_id)->firstOrFail();
        Mail::to($admin->admin_email)->send(new ResetPassword($pwd));
        $newPass = password_hash($pwd , PASSWORD_DEFAULT);
        $fields['admin_password'] = $newPass;
        $this->admin->update($admin, $fields);
        return response()->json(array(
            'result' => 'success',
        ));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Admin $admin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Admin $admin)
    {
        $this->admin->destroy($admin);
        return response()->json([
            'result' => 'success',
        ]);
    }
}
