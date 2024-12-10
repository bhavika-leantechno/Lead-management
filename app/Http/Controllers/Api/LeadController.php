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
        try {
            // Validate input fields for Level 1
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'number' => 'required|string|max:20',
                'company_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:leads,email', // Ensure the email is unique
                'location' => 'nullable|string',
            ]);
    
            // Generate a unique processing ID
            do {
                $processingId = Str::random(8);
            } while (Lead::where('processing_id', $processingId)->exists()); // Check if processing ID already exists
    
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
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422); // Unprocessable Entity status
        } catch (\Exception $e) {
            // Catch any other unexpected errors
            return response()->json([
                'message' => 'An error occurred during the lead creation process.',
                'error' => $e->getMessage(),
            ], 500); // Internal Server Error status
        }
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
        try {
            // Validate input for Level 3
            $validated = $request->validate([
                'lead_id' => 'required|exists:leads,id',
                'cr_file' => 'nullable|string|max:255', // Accept file path as a string
                'cc_file' => 'nullable|string|max:255', // Accept file path as a string
                'tl_file' => 'nullable|string|max:255', // Accept file path as a string
            ]);

            // Find the lead and update Level 3 data
            $lead = Lead::findOrFail($validated['lead_id']);

            // Update the lead with provided file paths and set the level to 3
            $lead->update([
                'cr_file' => $validated['cr_file'] ?? $lead->cr_file,
                'cc_file' => $validated['cc_file'] ?? $lead->cc_file,
                'tl_file' => $validated['tl_file'] ?? $lead->tl_file,
                'level' => '3', // Mark as Level 3 (final step)
            ]);

            // Return success response
            return response()->json([
                'message' => 'Lead successfully updated to Level 3.',
                'lead_id' => $lead->id
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422); // Unprocessable Entity
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle case where lead is not found
            return response()->json([
                'message' => 'Lead not found.',
            ], 404); // Not Found
        } catch (\Exception $e) {
            // Handle any other unexpected errors
            return response()->json([
                'message' => 'An error occurred during Level 3 processing.',
                'error' => $e->getMessage(),
            ], 500); // Internal Server Error
        }
    }


    /**
     * Get all leads (Admin access).
     */
    public function getLeads(Request $request)
    {
        // Get all leads
        $leads = Lead::all();

        // Get total count of leads
        $totalCount = $leads->count();

        return response()->json([
            'message' => 'Leads fetched successfully.',
            'total_count' => $totalCount,
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
