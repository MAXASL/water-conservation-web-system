<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Goal;
use App\Models\HomeUsage;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function create()
    {
        $userId = Auth::id(); // Get logged-in user ID
        $goal = Goal::where('user_id', $userId)->latest()->first();
        $currentUsage = HomeUsage::where('user_id', $userId)->sum('usage'); // Sum daily usage
        $similarHouseholdsUsage = HomeUsage::where('user_id', '!=', $userId)->avg('usage'); // Average other users

        // Prevent division by zero
        $progress = $goal ? ($currentUsage / $goal->target_usage) * 100 : 0;
        $progress = $progress > 100 ? 100 : round($progress, 2); // Cap at 100%

        return view('goal', compact('goal', 'currentUsage', 'progress', 'similarHouseholdsUsage'));
    }

    public function store(Request $request)
{
    $request->validate([
        'target_usage' => 'required|numeric|min:1'
    ]);

    Goal::create([
        'user_id' => Auth::id(), // Save goal for the logged-in user
        'target_usage' => $request->target_usage
    ]);

    return redirect()->route('goal.create')->with('success', 'Goal set successfully!');
}
}

