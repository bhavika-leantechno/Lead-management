<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LeadController extends Controller
{

    public function levelOne(Request $request)
    {
        // Validate input fields for Level 1
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'location' => 'nullable|string',
        ]);

        // Generate a unique processing ID
        $processingId = Str::random(8);

        // Create new lead at level 1 and store in database
        $lead = Lead::create([
            'name' => $validated['name'],
            'number' => $validated['number'],
            'company_name' => $validated['company_name'],
            'email' => $validated['email'],
            'location' => $validated['location'],
            'processing_id' => $processingId,
            'level' => '1', // Mark as Level 1
        ]);

        // Return response with lead data and next step
        return response()->json([
            'message' => 'Step 1 data saved, proceed to Level 2.',
            'lead_id' => $lead->id,
            'processing_id' => $processingId,
            'step' => 2
        ]);
    }

    /**
     * Handle Level 2: Additional Details.
     */
    public function levelTwo(Request $request)
    {
        // Validate input fields for Level 2
        $validated = $request->validate([
            'some_text' => 'required|string|max:255',
        ]);

        // Find the lead and update Level 2 data
        $lead = Lead::findOrFail($request->lead_id);
        $lead->update([
            'some_text' => $validated['some_text'],
            'level' => '2', // Mark as Level 2
        ]);

        // Return response with lead data and next step
        return response()->json([
            'message' => 'Step 2 data saved, proceed to Level 3.',
            'lead_id' => $lead->id,
            'step' => 3
        ]);
    }

    /**
     * Handle Level 3: File Upload and Finalization.
     */
    public function levelThree(Request $request)
    {
        // Validate file upload for Level 3
        $validated = $request->validate([
            'cr_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'cc_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'tl_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Find the lead and update Level 3 data with file uploads
        $lead = Lead::findOrFail($request->lead_id);

        // Handle file uploads
        if ($request->hasFile('cr_file')) {
            $crFilePath = $request->file('cr_file')->store('leads/cr', 'public');
            $lead->cr_file = $crFilePath;
        }

        if ($request->hasFile('cc_file')) {
            $ccFilePath = $request->file('cc_file')->store('leads/cc', 'public');
            $lead->cc_file = $ccFilePath;
        }

        if ($request->hasFile('tl_file')) {
            $tlFilePath = $request->file('tl_file')->store('leads/tl', 'public');
            $lead->tl_file = $tlFilePath;
        }

        // Update the lead status and set to final step (Level 3)
        $lead->update([
            'file_path' => $lead->cr_file ?? $lead->cc_file ?? $lead->tl_file,
            'level' => '3', // Mark as Level 3 (final step)
        ]);

        // Return success response
        return response()->json([
            'message' => 'Lead successfully created.',
            'lead_id' => $lead->id
        ]);
    }

    /**
     * Get all leads (Admin access).
     */
    public function getLeads(Request $request)
    {
        // Get all leads
        $leads = Lead::all();

        return response()->json([
            'message' => 'Leads fetched successfully.',
            'data' => $leads
        ]);
    }

    /**
     * Get leads by level (e.g., Level 1, Level 2, or Level 3).
     */
    public function getLeadsByLevel(Request $request, $level)
    {
        // Fetch leads based on the level
        $leads = Lead::where('level', $level)->get();

        return response()->json([
            'message' => "Leads for Level $level fetched successfully.",
            'data' => $leads
        ]);
    }
}
