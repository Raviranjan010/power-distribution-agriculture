<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transformer;
use App\Models\Complaint;
use App\Models\Connection;

class OfficerController extends Controller
{
    public function dashboard()
    {
        $officer = Auth::user();
        
        $transformers = Transformer::where('officer_id', $officer->id)->get();
        $assignedComplaints = Complaint::where('assigned_officer_id', $officer->id)
                                        ->whereIn('status', ['open', 'in_progress'])
                                        ->get();
        
        $pendingConnections = Connection::where('status', 'pending')
                                        ->with('farmer.user')
                                        ->get();

        return view('officer.dashboard', compact('transformers', 'assignedComplaints', 'pendingConnections'));
    }

    public function approveConnection(Request $request, $id)
    {
        $request->validate([
            'transformer_id' => 'required|exists:transformers,id',
            'allocated_load_kw' => 'required|numeric|min:1',
        ]);

        $connection = Connection::findOrFail($id);
        $connection->update([
            'transformer_id' => $request->transformer_id,
            'allocated_load_kw' => $request->allocated_load_kw,
            'status' => 'active',
            'connection_date' => now(),
        ]);

        return back()->with('success', 'Connection approved and allocated successfully!');
    }

    public function resolveComplaint($id)
    {
        $complaint = Complaint::findOrFail($id);
        $complaint->update(['status' => 'resolved']);

        return back()->with('success', 'Complaint marked as resolved.');
    }
}
