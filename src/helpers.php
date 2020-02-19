<?php

if (!function_exists('succ')) {
    function succ($data = '', $msg = 'success', $code = 0)
    {
        $data = [
            'msg' => $msg,
            'code' => $code,
            'data' => $data,
        ];
        return response()->json($data);
    }
}

if (!function_exists('err')) {
    function err($msg = 'error', $code = 1)
    {
        $data = [
            'msg' => $msg,
            'code' => $code,
        ];
        return response()->json($data);
    }
}

if (!function_exists('auth_guard')) {
    function auth_guard()
    {
        return Auth::guard(LABAMA_ENTRY);
    }
}
