<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            if ($request->user()->user_type !== 'admin') {
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have sufficient permissions to access this resource.',
                    'data' => null
                ], 200); // HTTP 403 Forbidden
            }

            $freelancers = User::where('user_type', 'freelancer')->get();

            return response()->json([
                'status' => true,
                'message' => 'Freelancers fetched successfully.',
                'data' => $freelancers,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching freelancers:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching the freelancers.',
                'data' => $e->getMessage(),
            ], 500);
        }
    }

    public function getFreelancersApprove(Request $request)
    {
        try {
            if ($request->user()->user_type !== 'admin') {
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have sufficient permissions to access this resource.',
                    'data' => null
                ], 200);
            }

            $validated = $request->validate([
                'freelancer_id' => 'required|exists:users,id',
            ]);

            $freelancer = User::where('id', $validated['freelancer_id'])
                ->where('user_type', 'freelancer')
                ->first();

            if (!$freelancer) {
                return response()->json([
                    'status' => false,
                    'message' => 'Freelancer not found or not a valid freelancer',
                    'data' => null,
                ], 200);
            }

            $freelancer->approve_status = 1;
            $freelancer->save();

            return response()->json([
                'status' => true,
                'message' => 'Freelancer approved successfully.',
                'data' => $freelancer,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error approving freelancer:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while approving the freelancer.',
                'data' => $e->getMessage(),
            ], 500);
        }
    }

    public function getMobileServicesLeads(Request $request)
    {
        try {
            if ($request->user()->user_type !== 'admin') {
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have sufficient permissions to access this resource.',
                    'data' => null
                ], 200);
            }

            $leads = Lead::where('lead_type', 'mobile services')->get();

            return response()->json([
                'status' => true,
                'message' => 'Mobile services leads fetched successfully.',
                'data' => $leads,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching mobile services leads:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching mobile services leads.',
                'data' => $e->getMessage(),
            ], 500);
        }
    }

    public function getOutsourcingLeads(Request $request)
    {
        try {
            if ($request->user()->user_type !== 'admin') {
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have sufficient permissions to access this resource.',
                    'data' => null
                ], 200);
            }

            $leads = Lead::where('lead_type', 'outsourcing')->get();

            return response()->json([
                'status' => true,
                'message' => 'Outsourcing leads fetched successfully.',
                'data' => $leads,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching outsourcing leads:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching outsourcing leads.',
                'data' => $e->getMessage(),
            ], 500);
        }
    }

    public function getLeadDetails($id, Request $request)
    {
        try {
            if ($request->user()->user_type !== 'admin') {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized',
                    'data' => null
                ], 200);
            }

            $lead = Lead::find($id);

            if (!$lead) {
                return response()->json([
                    'status' => false,
                    'message' => 'Lead not found.',
                    'data' => null
                ], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'Lead details fetched successfully.',
                'data' => $lead,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching lead details:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching lead details.',
                'data' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateLeadStatus($id, Request $request)
    {
        try {
            if ($request->user()->user_type !== 'admin') {
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have sufficient permissions to access this resource.',
                    'data' => null
                ], 200);
            }

            $validated = $request->validate([
                'agent_id' => 'required|exists:users,id',
                'change_status' => 'required|string',
            ]);

            $lead = Lead::find($id);

            if (!$lead) {
                return response()->json([
                    'status' => false,
                    'message' => 'Lead not found.',
                    'data' => null
                ], 200);
            }

            $lead->agent_id = $validated['agent_id'];
            $lead->change_status = $validated['change_status'];
            $lead->save();

            return response()->json([
                'status' => true,
                'message' => 'Lead status updated successfully.',
                'data' => $lead,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'data' => $e->errors(),
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error updating lead status:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating the lead status.',
                'data' => $e->getMessage(),
            ], 500);
        }
    }

    public function createAgent(Request $request)
    {
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

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'data' => $validator->errors(),
            ], 200);
        }

        try {
            $profile_picture_path = null;
            if ($request->hasFile('profile_picture')) {
                $profile_picture_path = $request->file('profile_picture')->store('profile_pictures', 'public');
            }

            $agent = User::create([
                'name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make('agent@123'),
                'address' => $request->address,
                'user_type' => 'agent',
                'profile_picture' => $profile_picture_path,
                'assigned_report' => $request->assigned_report ?? false,
                'approve_freelancer' => $request->approve_freelancer ?? false,
                'assigned_agent' => $request->assigned_agent ?? false,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Agent created successfully.',
                'data' => $agent,
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error creating agent: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while creating the agent.',
                'data' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAgents(Request $request)
    {
        try {
            $agents = User::where('user_type', 'agent')->get();

            return response()->json([
                'status' => true,
                'message' => 'Agents fetched successfully.',
                'data' => $agents,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching agents.',
                'data' => $e->getMessage(),
            ], 500);
        }
    }

    public function viewAgent($id)
    {
        try {
            $agent = User::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Agent details fetched successfully.',
                'data' => $agent,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Agent not found.',
                'data' => $e->getMessage(),
            ], 404);
        }
    }

    public function editAgent(Request $request, $id)
    {
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
                'status' => false,
                'message' => 'Validation failed',
                'data' => $validated->errors(),
            ], 200);
        }

        try {
            $agent = User::findOrFail($id);

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
                'status' => true,
                'message' => 'Agent updated successfully.',
                'data' => $agent,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating the agent.',
                'data' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteAgent($id)
    {
        try {
            $agent = User::findOrFail($id);
            $agent->delete();

            return response()->json([
                'status' => true,
                'message' => 'Agent deleted successfully.',
                'data' => null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Agent not found or an error occurred.',
                'data' => $e->getMessage(),
            ], 500);
        }
    }
}
