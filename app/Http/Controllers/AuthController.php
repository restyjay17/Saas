<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    
    public function login(Request $request)
    {
        $auth = Auth::guard('admin')->attempt([
            'uname' => $request->uname,
            'password' => md5($request->pword)
        ]);

        if ($auth) {
            $id = Auth::guard('admin')->id();
            $info = Admin::where('adm_id', $id)->first();

            if ($info->status === 2) {
                return response()->json([
                    'status' => 'Account is deactivated.'
                ], 400);
            }

            $request->session()->put('uname', $info->uname);
            $request->session()->put('name', $info->name);
            $request->session()->put('email', $info->email);

            return response()->json([
                'status' => $auth
            ], 200);
        } else {
            return response()->json([
                'status' => 'Invalid user credentials.'
            ], 400);
        }
    }


    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->forget('uname', 'name', 'email');
        $request->session()->flush();

        return response()->json([
            'redirect' => '/'
        ], 200);
    }


    public function checkSession()
    {
        $check = Auth::guard('admin')->check();

        return response()->json([
            'status' => $check
        ], 200);
    }

}
