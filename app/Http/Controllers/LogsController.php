<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogsController extends Controller
{
    public function getLogUsers()
    {
        $users = DB::table('admin_logs')
                    ->select('logged_by')
                    ->groupBy('logged_by')
                    ->orderBy('logged_by')
                    ->get();

        return response()->json([
            'users' => $users
        ], 200);
    }


    public function getAllLogs(Request $request)
    {
        $search = $request->search;
        $user = $request->user;
        $startDate = $request->startdate;
        $endDate = $request->enddate;

        $logs = DB::table('admin_logs')
                    ->select('action', 'logged_by', DB::raw('DATE_FORMAT(logged_at, "%M %e, %Y %h:%i %p") as logged_at'))
                    ->where(function($query) use ($search) {
                        if (isset($search)) {
                            $query->where('action', 'LIKE', '%' . $search . '%');
                        }
                    })
                    ->where(function($query) use ($user) {
                        if (isset($user)) {
                            $query->where('logged_by', $user);
                        }
                    })
                    ->where(function($query) use ($startDate, $endDate) {
                        if (isset($startDate) || isset($endDate)) {
                            if (!empty($startDate) && empty($endDate)) {
                                $query->where('logged_at', '>=', $startDate);
                            } elseif (empty($startDate) && !empty($endDate)) {
                                $query->where('logged_at', '<=', date('Y-m-d', strtotime($endDate . '1day')));
                            } else {
                                $query->whereBetween('logged_at', [$startDate, date('Y-m-d', strtotime($endDate . '1day'))]);
                            }
                        }
                    })
                    ->paginate(10);

        return response()->json([
            'logs' => $logs
        ], 200);
    }
}
