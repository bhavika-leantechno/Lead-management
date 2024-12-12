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

    public function userProfile(Request $request)
    {
        try {
            // Get the authenticated user
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found.',
                ], 200);
            }

            // Prepare user profile details
            $profileData = [
                'id' => $user->id,
                'fullname' => $user->firstname && $user->lastname
                ? $user->firstname . ' ' . $user->lastname
                : ($user->name ?? 'N/A'),
                'email' => $user->email,
                'profile_pic' => $user->profile_pic ? $user->profile_pic : null, // Provide full URL for profile_pic
            ];

            return response()->json([
                'status' => true,
                'message' => 'User profile fetched successfully.',
                'data' => $profileData,
            ], 200);

        } catch (\Exception $e) {
            // Handle errors
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching the profile.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function updateProfile(Request $request)
    {
        try {
            // Get the currently authenticated user
            $user = Auth::user();

            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'fullname' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
                'mobilenumber' => 'nullable|string|max:15',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 200);
            }

            // Update user profile
            if ($request->has('fullname')) {
                $names = explode(' ', $request->fullname, 2);
                $user->firstname = $names[0] ?? null; // First name
                $user->lastname = $names[1] ?? null; // Last name
            }

            if ($request->has('email')) {
                $user->email = $request->email;
            }

            if ($request->has('mobilenumber')) {
                $user->mobilenumber = $request->mobilenumber;
            }

            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Profile updated successfully.',
                'data' => [
                    'id' => $user->id,
                    'fullname' => $user->firstname . ' ' . $user->lastname,
                    'email' => $user->email,
                    'mobilenumber' => $user->mobilenumber,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating the profile.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    }
