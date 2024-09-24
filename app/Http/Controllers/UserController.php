<?php

namespace App\Http\Controllers;

use App\Models\Users;
use App\Models\Admin_logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function getAllUsers(Request $request)
    {
        $search = $request->search;
        $status = $request->status;

        $users = DB::table('users')
                    ->select('usr_id as id', 'lname', 'fname', 'contact_number', 'email', 'company_name', 'status')
                    ->where(function($query) use ($search) {
                        if (isset($search)) {
                            $query->where('lname', 'LIKE', '%' . $search . '%')
                                    ->orWhere('fname', 'LIKE', '%' . $search . '%')
                                    ->orWhere('contact_number', 'LIKE', '%' . $search . '%')
                                    ->orWhere('email', 'LIKE', '%' . $search . '%')
                                    ->orWhere('company_name', 'LIKE', '%' . $search . '%');
                        }
                    })
                    ->where(function($query) use ($status) {
                        if (isset($status)) {
                            $query->where('status', $status);
                        }
                    }) 
                    ->paginate(1);

        return response()->json([
            'users' => $users
        ], 200);
    }


    public function saveUser(Request $request)
    {
        $email = Users::where('email', $request->email)->first();
        if (!empty($email) && (!isset($request->target) && !empty($request->target))) {
            return response()->json([
                'message' => 'Email address already exist'
            ], 400);
        }


        if (isset($request->target) && !empty($request->target)) {
            $user = Users::find($request->target);
            $status = $request->status;
            $logAction = 'updated user account details of ' . $request->fname . ' ' . $request->lname;
        } else {
            $user = new Users();
            $status = 0;
            $logAction = 'created ' . $request->fname . ' ' . $request->lname . ' details as new user';
        }

        $user->lname = $request->lname;
        $user->fname = $request->fname;
        $user->mname = $request->mname;
        $user->contact_number = $request->contactnumber;
        $user->email = $request->email;
        $user->company_name = $request->company;
        $user->company_contact_number = $request->companycontactnumber;
        $user->company_email = $request->companyemail;
        $user->company_address = $request->companyaddress;
        $user->status = $status;
        $save = $user->save();

        if (!$save) {
            return response()->json([
                'message' => 'Something went wrong while saving user details'
            ], 400);
        }

        // save logs
        $uname = (Session::get('uname')) ? Session::get('uname') : 'unknown';
        $logs = new Admin_logs();
        $logs->action = $logAction;
        $logs->data = serialize([
            'target' => $request->target,
            'lname' => $request->lname,
            'fname' => $request->fname,
            'mname' => $request->mname,
            'contactnumber' => $request->contactnumber,
            'email' => $request->email,
            'company' => $request->company,
            'companycontactnumber' => $request->companycontactnumber,
            'companyemail' => $request->companyemail,
            'companyaddress' => $request->companyaddress,
            'status' => $status
        ]);
        $logs->logged_by = $uname;
        $logs->logged_at = date('Y-m-d H:i:s');
        $logs->save();

        return response()->json([
            'message' => 'User details successfully saved'
        ], 200);
    }


    public function getUserDetails($id)
    {
        $user = DB::table('users')
                    ->select('usr_id as id', 'lname', 'fname', 'mname', 'contact_number', 'email', 'company_name', 'company_contact_number', 'company_email', 'company_address', 'status')
                    ->where('usr_id', $id)
                    ->first();

        return response()->json([
            'details' => $user
        ], 200);
    }
}
