<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
    
            // Retrieve the user by email
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return response()->json([
                    'message' => 'User not found.',
                ], 404); // Not Found status
            }
    
            // Check if the password is correct
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Password is incorrect.',
                ], 401); // Unauthorized status
            }
    
            // Check user_type and approve_status
            if ($user->user_type === 'freelancer' && $user->approve_status === 0) {
                return response()->json([
                    'message' => 'Your profile is not approved yet. Please contact support.',
                ], 403); // Forbidden status
            }
    
            // Generate the JWT token
            $token = JWTAuth::fromUser($user);
    
            // Return the token along with user information
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'user_type' => $user->user_type,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422); // Unprocessable Entity status
        } catch (\Exception $e) {
            // Catch any other unexpected errors
            return response()->json([
                'message' => 'An error occurred during login.',
                'error' => $e->getMessage(),
            ], 500); // Internal Server Error status
        }
    }
    


    public function logout(Request $request)
    {
        try {
            // Revoke the user's token
            $request->user()->tokens->each(function ($token) {
                $token->delete();
            });
    
            // Return success response
            return response()->json([
                'message' => 'Successfully logged out.'
            ]);
        } catch (\Exception $e) {
            // Return error response if something goes wrong
            return response()->json([
                'message' => 'An error occurred during logout.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    
    

    public function signup(Request $request)
    {
        // Step 1: Validate incoming data
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'mobilenumber' => ['required', 'string', 'max:15', 'unique:users,mobilenumber'],
            'email' => ['required', 'email', 'unique:users,email'],
            'qr_code' => 'nullable',
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|same:password', // Custom validation for confirm_password
            'expirydate' => 'nullable|date',
            'terms_and_conditions' => 'accepted',  // Checkbox for terms and conditions
        ]);

        // Return errors if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Step 2: Handle file upload for QR code (optional)
       

        // Step 3: Create the user
        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'mobilenumber' => $request->mobilenumber,
            'email' => $request->email,
            'qr_code' =>  $request->qr_code,  // Store the file path
            'password' => Hash::make($request->password),  // Hash the password
            'expiredate' => $request->expirydate ? Carbon::parse($request->expirydate) : null,
            'status' => 'active',  // Default status is active
            'user_type' => 'freelancer',
            'profile_picture' => $request->profile_picture,
            'is_deleted' => false,  // Default deletion status
        ]);

        // Step 4: Return success response with user details
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

}
