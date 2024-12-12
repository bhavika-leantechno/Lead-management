<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PlanController extends Controller
{
    // Create a new plan
    public function createPlans(Request $request)
{
    // Create the validator instance
    $validator = Validator::make($request->all(), [
        'planname' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'status' => 'nullable|in:active,inactive',  // Ensure 'status' is valid if provided
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 200); // Return a 422 Unprocessable Entity response with errors
    }

    // Proceed to create the plan if validation passes
    try {
        $plan = Plan::create([
            'planname' => $request->planname,
            'price' => $request->price,
            'status' => $request->status ?? 'active',  // Default to 'active' if 'status' is null
            'created_by' => Auth::id() ?? 0,  // Default to 0 if 'created_by' is not available
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Plan created successfully.',
            'data' => $plan,
        ], 200); // Return a 201 Created response with the new plan data
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'An error occurred while creating the plan.',
            'error' => $e->getMessage(),
        ], 500); // Return a 500 Internal Server Error if any exception occurs
    }
}
    // List all plans
    public function getPlans()
    {
        try {
            $plans = Plan::all();

            return response()->json([
                'status' => true,
                'message' => 'Plans retrieved successfully.',
                'data' => $plans,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching the plans.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // View a plan by ID
    public function viewPlans($id)
    {
        try {
            $plan = Plan::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Plan details retrieved successfully.',
                'data' => $plan,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching the plan details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Edit a plan by ID
    public function editPlans(Request $request)
    {
        // Create a custom validator instance
        $validator = Validator::make($request->all(), [
            'planname' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:active,inactive',
            'plan_id' => 'require',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422); // 422 Unprocessable Entity status code for validation errors
        }

        try {
            // Find the plan by ID
            $plan = Plan::findOrFail($request->paln_id);

            // Update the plan with the provided data or keep the existing values
            $plan->update([
                'planname' => $request->planname ?? $plan->planname,
                'price' => $request->price ?? $plan->price,
                'status' => $request->status ?? $plan->status,
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Plan updated successfully.',
                'data' => $plan,
            ], 200); // 200 OK status code for successful update
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating the plan.',
                'error' => $e->getMessage(),
            ], 500); // 500 Internal Server Error if any exception occurs
        }
    }

    // Delete a plan by ID
    public function deletePlans($id)
    {
        try {
            $plan = Plan::findOrFail($id);
            $plan->update(['deleted_by' => Auth::id()]);
            $plan->delete();

            return response()->json([
                'status' => true,
                'message' => 'Plan deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while deleting the plan.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
