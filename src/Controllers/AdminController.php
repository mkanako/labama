<?php

namespace Cc\Labama\Controllers;

use Cc\Labama\Exceptions\Err;
use Cc\Labama\Models\AdminUserPermission;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function getRoutes(Request $request)
    {
        $uid = admin_guard()->user()->uid;
        if (1 == $uid) {
            return succ(['routeList' => '*']);
        }
        return succ([
            'routeList' => AdminUserPermission::where('uid', $uid)
                ->pluck('route_path')
                ->toArray(),
        ]);
    }

    public function changePassword(Request $request)
    {
        $password = head($this->ensureData(['password' => 'required|min:6|confirmed']));
        $user = admin_guard()->user();
        $user->password = bcrypt($password);
        $user->save();
        return succ();
    }

    public function login(Request $request)
    {
        $credentials = $this->ensureData(['username', 'password']);
        if (admin_guard()->attempt($credentials, true)) {
            return succ('login!');
        }
        return err('login fail');
    }

    public function logout(Request $request)
    {
        admin_guard()->logout();
        return succ();
    }

    public function ensureData($rule, $message = [])
    {
        $rule = collect($rule)->mapWithKeys(function ($item, $key) {
            if (is_int($key)) {
                return [$item => 'required'];
            }
            return [$key => $item];
        })->toArray();
        $validator = Validator::make(request()->all(), $rule, $message);
        if ($validator->fails()) {
            throw new Err(implode("\n", $validator->errors()->all()));
        }
        return request()->only(array_keys($rule));
    }
}
