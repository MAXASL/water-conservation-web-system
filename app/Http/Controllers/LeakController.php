<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leak;
use Illuminate\Support\Facades\Mail;

class LeakController extends Controller
{
    public function create()
    {
        return view('report');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'location' => 'required',
            'description' => 'required',
            'severity' => 'required',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'contact_info' => 'nullable|string',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('leak_images', 'public');
            $validated['image'] = $imagePath;
        }

        // Store in database
        $leak = Leak::create($validated);

        // Send Email Notification
        Mail::raw("New Leak Reported\n\nLocation: {$leak->location}\nSeverity: {$leak->severity}\nDescription: {$leak->description}\nContact Info: {$leak->contact_info}", function ($message) {
            $message->to('maxaslukwesa0@gmail.com')
                    ->subject('New Leak Report');
        });

        return redirect()->route('leaks.create')->with('success', 'Leak reported successfully!');
    }

    public function index()
    {
        $leaks = Leak::orderBy('created_at', 'desc')->get();
        return view('admin.leaks', compact('leaks'));
    }
}
