<?php

namespace Cc\Labama\Controllers;

use Cc\Labama\Facades\Auth;
use Cc\Labama\Models\UserPermission;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BaseController extends Controller
{
    public function getSysInfo()
    {
        $uid = Auth::user()->uid;
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
        $user = Auth::user();
        $user->password = Hash::make($password);
        $user->save();
        $token = Auth::refresh();
        return $this->responseWithToken($token);
    }

    public function login(Request $request)
    {
        $credentials = $this->getInput(['username', 'password']);
        $token = Auth::attempt($credentials, true);
        if ($token) {
            return $this->responseWithToken($token, $this->getSysInfo());
        }
        return err('username or password is incorrect');
    }

    public function logout(Request $request)
    {
        if (config('jwt.blacklist_enabled')) {
            Auth::logout();
        }
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
            throw new Exception(implode("\n", $validator->errors()->all()));
        }
        return request()->only(array_keys($rule));
    }

    private function responseWithToken($token, $data = '')
    {
        return response()
            ->succ($data)
            ->header('Authorization', 'Bearer ' . $token);
    }
}
