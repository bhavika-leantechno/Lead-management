<?php

namespace App\Http\Controllers\Api;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;

use Exception;

class PlanController extends Controller
{
    /**
     * Create a new plan
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'planname' => 'required|string|max:255',
            'price' => 'required|numeric',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'data' => $validator->errors(),
            ], 200);
        }

        try {
            $plan = Plan::create([
                'planname' => $request->planname,
                'price' => $request->price,
                'created_by' => auth()->id(), // assuming user authentication is set up
                'status' => $request->status,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Plan created successfully.',
                'data' => $plan,
            ], 200);
        } catch (Exception $e) {
            \Log::error('Error creating plan:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while creating the plan.',
                'data' => null,
            ], 500);
        }
    }

    /**
     * Get all plans
     */
    public function index()
    {
        try {
            $plans = Plan::all();
            return response()->json([
                'status' => true,
                'message' => 'Plans retrieved successfully.',
                'data' => $plans,
            ],200);
        } catch (Exception $e) {
            \Log::error('Error fetching plans:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching the plans.',
                'data' => null,
            ], 500);
        }
    }

    /**
     * Show a single plan
     */
    public function show($id)
    {
        try {
            $plan = Plan::findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Plan retrieved successfully.',
                'data' => $plan,
            ],200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Plan not found.',
                'data' => null,
            ], 200);
        } catch (Exception $e) {
            \Log::error('Error fetching plan details:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching the plan.',
                'data' => null,
            ], 500);
        }
    }

    /**
     * Update a plan
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'planname' => 'required|string|max:255',
            'price' => 'required|numeric',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'data' => $validator->errors(),
            ], 200);
        }

        try {
            $plan = Plan::findOrFail($id);
            $plan->update([
                'planname' => $request->planname,
                'price' => $request->price,
                'updated_by' => auth()->id(),
                'status' => $request->status,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Plan updated successfully.',
                'data' => $plan,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Plan not found.',
                'data' => null,
            ], 200);
        } catch (Exception $e) {
            \Log::error('Error updating plan:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating the plan.',
                'data' => null,
            ], 500);
        }
    }

    /**
     * Soft delete a plan
     */
    public function destroy($id)
    {
        try {
            $plan = Plan::findOrFail($id);
            $plan->update(['deleted_by' => auth()->id()]);
            $plan->delete();

            return response()->json([
                'status' => true,
                'message' => 'Plan deleted successfully.',
                'data' => null,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Plan not found.',
                'data' => null,
            ], 200);
        } catch (Exception $e) {
            \Log::error('Error deleting plan:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while deleting the plan.',
                'data' => null,
            ], 500);
        }
    }
}
