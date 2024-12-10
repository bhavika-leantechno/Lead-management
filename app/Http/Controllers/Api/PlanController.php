<?php

namespace App\Http\Controllers;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class PlanController extends Controller
{
    // Create a new plan
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'planname' => 'required|string|max:255',
            'price' => 'required|numeric',
            'status' => 'required|in:active,inactive',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $plan = Plan::create([
                'planname' => $request->planname,
                'price' => $request->price,
                'created_by' => auth()->id(), // assuming user authentication is set up
                'status' => $request->status,
            ]);

            return response()->json($plan, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create plan.'], 500);
        }
    }

    // Get all plans
    public function index()
    {
        try {
            $plans = Plan::all();
            return response()->json($plans);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve plans.'], 500);
        }
    }

    // Show a single plan
    public function show($id)
    {
        try {
            $plan = Plan::findOrFail($id);
            return response()->json($plan);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Plan not found.'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve plan.'], 500);
        }
    }

    // Update a plan
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'planname' => 'required|string|max:255',
            'price' => 'required|numeric',
            'status' => 'required|in:active,inactive',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $plan = Plan::findOrFail($id);
            $plan->update([
                'planname' => $request->planname,
                'price' => $request->price,
                'updated_by' => auth()->id(),
                'status' => $request->status,
            ]);

            return response()->json($plan);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Plan not found.'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update plan.'], 500);
        }
    }

    // Soft delete a plan
    public function destroy($id)
    {
        try {
            $plan = Plan::findOrFail($id);
            $plan->update(['deleted_by' => auth()->id()]);
            $plan->delete();

            return response()->json(['message' => 'Plan deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Plan not found.'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete plan.'], 500);
        }
    }
}