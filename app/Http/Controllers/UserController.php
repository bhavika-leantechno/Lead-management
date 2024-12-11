<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function create(Request $request)
    {
        try {
            // Validate the incoming request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
            ]);

            // Create the new user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Return a success response
            return response()->json([
                'status' => true,
                'message' => 'User created successfully!',
                'data' => $user
            ], 200); // Created status
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'data' => $e->errors(),
            ], 200); // Unprocessable Entity
        } catch (\Exception $e) {
            // Catch any other exceptions (e.g., database errors)
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while creating the user.',
                'data' => $e->getMessage(),
            ], 500); // Internal Server Error
        }
    }

    public function changePassword(Request $request)
    {
        try {

        if (!$request->hasHeader('Authorization')) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Authorization token is missing.',
                        'data' => null,
                    ], 401); // Unauthorized
                }
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed.',
                    'data' => $validator->errors(),
                ], 200); // Bad Request
            }

            if (!Hash::check($request->current_password, Auth::user()->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Current password is incorrect',
                    'data' => null,
                ], 200); // Bad Request
            }

            Auth::user()->update([
                'password' => Hash::make($request->new_password),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Password changed successfully',
                'data' => null,
            ], 200); // OK status
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while changing the password.',
                'data' => $e->getMessage(),
            ], 500); // Internal Server Error
        }
    }
}
