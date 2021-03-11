<?php

namespace App\Http\Controllers\Auth;

use App\Repositories\Contracts\AdminRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class UserController extends Controller
{
    /**
     * @var AdminRepository
     */
    private $admin;

    public function __construct(AdminRepository $admin)
    {
        $this->admin = $admin;
    }
    public function index()
    {
       $user = auth()->user();
       return view('backstage.admin.user.index' , compact('user'));
    }

    /**
     * @param Request $request
     * @return array
     */
    public function update(Request $request)
    {
        $user   = auth()->user();
        $fields = $request->except(['admin_id' , 'admin_email' , 'layuiTreeCheck']);
        if (!empty($fields['password']) && !empty($fields['new_password']) && !empty($fields['new_password'])) {
            if (!password_verify($fields['password'], $user['admin_password'])) {
                return ['code'=>1, 'result'=>'Old Password Error'];
            }
            if (strlen($fields['new_password'])<6 || $fields['new_password'] !== $fields['confirm_password']) {
                return ['code'=>1, 'result'=>trans('passwords.password')];
            }
            $newPass = password_hash($fields['new_password'] , PASSWORD_DEFAULT);
        }
        $arr = collect($user)->except('permissions')->toArray();
        $diff= array_diff($fields, $arr);
        if ($diff) {
            if (!empty($newPass)) {
                $fields['admin_password'] = $newPass;
                unset($fields['password'], $fields['new_password'], $fields['confirm_password']);
                $code=2;
            }
            $this->admin->update($user, $fields);
            return ['code'=>$code ?? 0, 'result'=>trans('common.ajax.result.prompt.operate')];
        }
    }


}
