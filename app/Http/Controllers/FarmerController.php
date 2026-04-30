<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Connection;
use App\Models\Complaint;

class FarmerController extends Controller
{
    public function dashboard()
    {
        $farmer = Auth::user()->farmer;
        
        $connections = $farmer ? $farmer->connections()->with('transformer', 'bills')->get() : [];
        $complaints = Auth::user()->complaints()->latest()->get();

        return view('farmer.dashboard', compact('farmer', 'connections', 'complaints'));
    }

    public function storeConnection(Request $request)
    {
        $request->validate([
            'requested_load_kw' => 'required|numeric|min:1|max:50',
        ]);

        Connection::create([
            'farmer_id' => Auth::user()->farmer->id,
            'requested_load_kw' => $request->requested_load_kw,
            'status' => 'pending',
        ]);

        return back()->with('success', 'New connection requested successfully! Waiting for officer approval.');
    }

    public function storeComplaint(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        Complaint::create([
            'user_id' => Auth::user()->id,
            'subject' => $request->subject,
            'description' => $request->description,
            'status' => 'open',
        ]);

        return back()->with('success', 'Complaint filed successfully! An officer will review it shortly.');
    }
}
