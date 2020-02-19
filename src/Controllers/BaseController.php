<?php

namespace Cc\Labama\Controllers;

use Cc\Labama\Exceptions\Err;
use Cc\Labama\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BaseController extends Controller
{
    public function getSysInfo()
    {
        $uid = auth_guard()->user()->uid;
        return [
            'attachUrl' => Storage::disk('attacent')->url('/' . LABAMA_ENTRY . '/'),
            'routeList' => 1 == $uid ? '*' : UserPermission::where('uid', $uid)
                ->pluck('route_path')
                ->toArray(),
        ];
    }

    public function sysInfo(Request $request)
    {
        return succ($this->getSysInfo());
    }

    public function changePassword(Request $request)
    {
        $password = head($this->getInput(['password' => 'required|min:6|confirmed']));
        $user = auth_guard()->user();
        $user->password = bcrypt($password);
        $user->save();
        return succ();
    }

    public function login(Request $request)
    {
        $credentials = $this->getInput(['username', 'password']);
        if (auth_guard()->attempt($credentials, true)) {
            return succ($this->getSysInfo());
        }
        return err('username or password is incorrect');
    }

    public function logout(Request $request)
    {
        auth_guard()->logout();
        return succ();
    }

    public function getInput($rule, $message = [])
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
