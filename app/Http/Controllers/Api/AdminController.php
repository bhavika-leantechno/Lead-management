<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller
{
    /**
     * Get a list of users where user_type = 'freelancer'.
     */
    public function getFreelancers(Request $request)
    {
        try {
            // Check if the authenticated user is an admin
            if ($request->user()->user_type !== 'admin') {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
    
            // Retrieve users with user_type = 'freelancer'
            $freelancers = User::where('user_type', 'freelancer')->get();
    
            return response()->json([
                'success' => true,
                'data' => $freelancers,
            ]);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            \Log::error('Error fetching freelancers:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
    
            // Return a generic error response
            return response()->json([
                'error' => 'An error occurred while fetching the freelancers.',
                'message' => $e->getMessage(), // Optional: Include in development mode
            ], 500);
        }
    }

    public function getFreelancersApprove(Request $request)
{
    try {
        // Check if the authenticated user is an admin
        if ($request->user()->user_type !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate the input
        $validated = $request->validate([
            'freelancer_id' => 'required|exists:users,id', // Ensure freelancer_id exists in users table
        ]);

        // Find the freelancer by ID
        $freelancer = User::where('id', $validated['freelancer_id'])
            ->where('user_type', 'freelancer') // Ensure it's a freelancer
            ->first();

        if (!$freelancer) {
            return response()->json(['error' => 'Freelancer not found or not a valid freelancer'], 404);
        }

        // Update the approve_status to 1
        $freelancer->approve_status = 1;
        $freelancer->save();

        return response()->json([
            'success' => true,
            'message' => 'Freelancer approved successfully.',
            'data' => $freelancer,
        ]);
    } catch (\Exception $e) {
        // Log the exception
        \Log::error('Error approving freelancer:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        // Return an error response
        return response()->json([
            'error' => 'An error occurred while approving the freelancer.',
            'message' => $e->getMessage(),
        ], 500);
    }
}

public function getMobileServicesLeads(Request $request)
{
    try {
        // Check if the authenticated user is an admin
        if ($request->user()->user_type !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Retrieve leads with lead_type = 'Mobile services'
        $leads = Lead::where('lead_type', 'mobile services')->get();

        return response()->json([
            'success' => true,
            'data' => $leads,
        ]);
    } catch (\Exception $e) {
        // Log the exception
        \Log::error('Error fetching mobile services leads:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'error' => 'An error occurred while fetching mobile services leads.',
            'message' => $e->getMessage(),
        ], 500);
    }
}
public function getOutsourcingLeads(Request $request)
{
    try {
        // Check if the authenticated user is an admin
        if ($request->user()->user_type !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Retrieve leads with lead_type = 'Outsourcing'
        $leads = Lead::where('lead_type', 'outsourcing')->get();

        return response()->json([
            'success' => true,
            'data' => $leads,
        ]);
    } catch (\Exception $e) {
        // Log the exception
        \Log::error('Error fetching outsourcing leads:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'error' => 'An error occurred while fetching outsourcing leads.',
            'message' => $e->getMessage(),
        ], 500);
    }
}

public function getLeadDetails($id, Request $request)
{
    try {
        // Check if the authenticated user is an admin
        if ($request->user()->user_type !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Retrieve the lead by ID
        $lead = Lead::find($id);

        if (!$lead) {
            return response()->json([
                'error' => 'Lead not found.',
            ], 404); // Not Found status
        }

        // Return the lead details
        return response()->json([
            'success' => true,
            'data' => $lead,
        ]);
    } catch (\Exception $e) {
        // Log the exception
        \Log::error('Error fetching lead details:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'error' => 'An error occurred while fetching lead details.',
            'message' => $e->getMessage(),
        ], 500); // Internal Server Error status
    }
}

public function updateLeadStatus($id, Request $request)
{
    try {
        // Check if the authenticated user is an admin
        if ($request->user()->user_type !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate the request to ensure an agent ID is provided
        $validated = $request->validate([
            'agent_id' => 'required|exists:users,id', // Ensure agent exists in the users table
            'change_status' => 'required|string',    // Ensure change_status is provided (e.g., 'assigned', 'in progress', etc.)
        ]);

        // Retrieve the lead by ID
        $lead = Lead::find($id);

        if (!$lead) {
            return response()->json([
                'error' => 'Lead not found.',
            ], 404); // Not Found status
        }

        // Update the lead with the new agent and status
        $lead->agent_id = $validated['agent_id'];
        $lead->change_status = $validated['change_status'];
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Lead status updated successfully.',
            'data' => $lead,
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'Validation failed.',
            'errors' => $e->errors(),
        ], 422); // Unprocessable Entity status
    } catch (\Exception $e) {
        // Log the exception
        \Log::error('Error updating lead status:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'error' => 'An error occurred while updating the lead status.',
            'message' => $e->getMessage(),
        ], 500); // Internal Server Error status
    }
}

public function createAgent(Request $request)
{
    // Validate incoming request data using Validator
    $validator = Validator::make($request->all(), [
        'full_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'phone_number' => 'required|string|max:20',
        'address' => 'nullable|string|max:500',
        'profile_picture' => 'nullable|string',
        'assigned_report' => 'nullable|boolean',
        'approve_freelancer' => 'nullable|boolean',
        'assigned_agent' => 'nullable|boolean',
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422);
    }

    try {
        // Store the image if provided
        $profile_picture_path = null;
        if ($request->hasFile('profile_picture')) {
            $profile_picture_path = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        // Create a new agent record
        $agent = User::create([
            'name' => $request->full_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make('agent@123'),  // Hash the password
            'address' => $request->address,
            'user_type' => 'agent', // Set user_type as 'agent'
            'profile_picture' => $profile_picture_path,
            'assigned_report' => $request->assigned_report ?? false,
            'approve_freelancer' => $request->approve_freelancer ?? false,
            'assigned_agent' => $request->assigned_agent ?? false,

        ]);

        return response()->json([
            'message' => 'Agent created successfully',
            'data' => $agent,
        ], 201);

    } catch (\Exception $e) {
        \Log::error('Error creating agent: ' . $e->getMessage());
        return response()->json([
            'message' => 'An error occurred while creating the agent.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function getAgents(Request $request)
{
    try {
        // Fetch all agents
        $agents = User::where('user_type', 'agent')->get();

        return response()->json([
            'message' => 'Agents fetched successfully',
            'data' => $agents,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred while fetching agents.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function viewAgent($id)
{
    try {
        // Find the agent by ID
        $agent = User::findOrFail($id);

        return response()->json([
            'message' => 'Agent details fetched successfully',
            'data' => $agent,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Agent not found.',
            'error' => $e->getMessage(),
        ], 404);
    }
}

public function editAgent(Request $request, $id)
{
    // Validate incoming data
    $validated = Validator::make($request->all(), [
        'full_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
        'phone_number' => 'required|string|max:20',
        'address' => 'nullable|string|max:500',
        'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png',
        'assigned_report' => 'nullable|boolean',
        'approve_freelancer' => 'nullable|boolean',
        'assigned_agent' => 'nullable|boolean',
    ]);

    if ($validated->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validated->errors(),
        ], 422);
    }

    try {
        // Find the agent by ID
        $agent = User::findOrFail($id);

        // Update agent details
        if ($request->hasFile('profile_picture')) {
            $profile_picture_path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $agent->profile_picture = $profile_picture_path;
        }

        $agent->name = $request->full_name;
        $agent->email = $request->email;
        $agent->phone_number = $request->phone_number;
        $agent->address = $request->address;
        $agent->assigned_report = $request->assigned_report ?? false;
        $agent->approve_freelancer = $request->approve_freelancer ?? false;
        $agent->assigned_agent = $request->assigned_agent ?? false;

        $agent->save();

        return response()->json([
            'message' => 'Agent updated successfully',
            'data' => $agent,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred while updating the agent.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function deleteAgent($id)
{
    try {
        // Find the agent by ID
        $agent = User::findOrFail($id);

        // Delete the agent
        $agent->delete();

        return response()->json([
            'message' => 'Agent deleted successfully',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Agent not found or an error occurred.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    
}