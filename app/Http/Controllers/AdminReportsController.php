<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leak;
use App\Models\Goal;
use App\Models\User;
use Carbon\Carbon;

class AdminReportsController extends Controller
{
    public function index1(Request $request)
    {

        // Get filter values from request
        $status = $request->input('status');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $userId = $request->input('user_id');
        $severity = $request->input('severity');

        // Get all users for filter dropdown
        $users = User::where('is_admin', false)->get();

        // Query for leaks
        $leaks = Leak::query()
            ->when($status, function($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($severity, function($query) use ($severity) {
                return $query->where('severity', $severity);
            })
            ->when($dateFrom, function($query) use ($dateFrom) {
                return $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function($query) use ($dateTo) {
                return $query->whereDate('created_at', '<=', $dateTo);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'leaks_page');

        // Query for goals
        $goals = Goal::with('user')
            ->when($userId, function($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'goals_page');

        return view('admin.reports', compact('leaks', 'goals', 'users'));
    }

    public function updateLeakStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,investigating,resolved'
        ]);

        $leak = Leak::findOrFail($id);
        $leak->status = $request->status;
        $leak->save();

        return back()->with('success', 'Leak status updated successfully!');
    }

    public function updateGoalProgress(Request $request, $id)
    {
        $request->validate([
            'current_progress' => 'required|numeric|min:0'
        ]);

        $goal = Goal::findOrFail($id);
        $goal->current_progress = $request->current_progress;
        $goal->save();

        return back()->with('success', 'Goal progress updated successfully!');
    }
}
