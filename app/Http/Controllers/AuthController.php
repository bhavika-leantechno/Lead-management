<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

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
            // Check if user exists and password is correct
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Password is incorrect.',
                ], 401); // Unauthorized status
            }
    
            // Generate the token
            $token = $user->createToken('auth_token')->plainTextToken;
    
            // Return the token
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
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
        // Delete the current access token for the user
        $request->user()->currentAccessToken()->delete();
        
        // Return a JSON response indicating successful logout
        return response()->json(['message' => 'Logged out successfully']);
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
            'is_deleted' => false,  // Default deletion status
        ]);

        // Step 4: Return success response with user details
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

}
