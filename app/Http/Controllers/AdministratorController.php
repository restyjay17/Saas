<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Admin;
use App\Models\Admin_logs;

class AdministratorController extends Controller
{
    public function getAllAdministrators()
    {
        $administrators = Admin::where('adm_id', '>', 0)->orderBy('uname')->paginate(10);

        return response()->json([
            'administrators' => $administrators
        ]);
    }


    public function saveAdministrator(Request $request)
    {
        $uname = Admin::where('uname', $request->uname)->first();
        if (!empty($uname) && !isset($request->target) && empty($request->target)) {
            return response()->json([
                'message' => 'Username already exist'
            ], 400);
        }


        if (isset($request->target) && !empty($request->target)) {
            $administrator = Admin::find($request->target);
            $logAction = 'updated admin account details of ' . $request->name;
        } else {
            $administrator = new Admin();
            $logAction = 'created ' . $request->name . ' details as new administrator ';
        }


        if (isset($request->pword) && !empty($request->pword)) $administrator->password = Hash::make(md5($request->pword));

        $administrator->uname = $request->uname;
        $administrator->name = $request->name;
        $administrator->email = $request->email;
        $administrator->status = $request->status;
        $administrator->save();

        // save logs
        $uname = (Session::get('uname')) ? Session::get('uname') : 'unknown';
        $logs = new Admin_logs();
        $logs->action = $logAction;
        $logs->data = serialize([
            'target' => $request->target,
            'uname' => $request->uname,
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->status
        ]);
        $logs->logged_by = $uname;
        $logs->logged_at = date('Y-m-d H:i:s');
        $logs->save();

        return response()->json([
            'message' => 'Administrator details successfully saved'
        ], 200);
    }


    public function getAdministratorDetails($id)
    {

        $administrator = DB::table('admins')
                            ->select('adm_id as id', 'uname', 'name', 'email', 'status')
                            ->where('adm_id', $id)
                            ->first();

        return response()->json([
            'details' => $administrator
        ], 200);
        
    }


    public function deleteAdministrator($id)
    {
        $administrator = Admin::find($id);
        $delete = Admin::where('adm_id', $id)->delete();

        if (!$delete) return response()->json(['message' => 'Something went wrong while deleting administrator details;']);

        // save logs
        $uname = (Session::get('uname')) ? Session::get('uname') : 'unknown';
        $logs = new Admin_logs();
        $logs->action = 'deleted administrator details of ' . $administrator->name;
        $logs->data = serialize([
            'id' => $administrator->id,
            'uname' => $administrator->uname,
            'password' => $administrator->password,
            'name' => $administrator->name,
            'email' => $administrator->email,
            'status' => $administrator->status,
            'created_at' => $administrator->created_at,
            'updated_at' => $administrator->updated_at
        ]);
        $logs->logged_by = $uname;
        $logs->logged_at = date('Y-m-d H:i:s');
        $logs->save();

        return response()->json([
            'message' => 'Administrator details successfully deleted.'
        ], 200);
    }
}
