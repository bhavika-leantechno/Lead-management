<?php
namespace App\Http\Controllers; // Ensure the correct namespace is used

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ResetPasswordController extends Controller
{
   public function resetPassword(Request $request)
{
    try {
        // Validate the inputs
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',  // This checks if 'password' matches 'password_confirmation'
            'otp' => 'required|string|size:6',
        ], [
            'email.exists' => 'The provided email does not exist in our records.',
            'email.email' => 'The provided email address is invalid.',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'data' => $validator->errors(),
            ], 200); // Bad Request status
        }

        $email = $request->input('email');
        $password = $request->input('password');
        $otp = $request->input('otp');

        // Log the inputs for debugging
        Log::info('Reset Password Request', [
            'email' => $email,
            'otp' => $otp,
        ]);

        // Verify OTP
        $resetData = DB::table('password_resets')->where('email', $email)->first();

        if (!$resetData) {
            Log::error('OTP verification failed: No reset data found for email', ['email' => $email]);
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired OTP.',
                'data' => null,
            ], 200); // Bad Request status
        }

        if ($resetData->otp !== $otp || $resetData->expiry_time < now()) {
            Log::error('OTP verification failed: OTP mismatch or expired', ['email' => $email, 'otp' => $otp]);
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired OTP.',
                'data' => null,
            ], 200); // Bad Request status
        }

        // Update the user's password
        $userUpdated = User::where('email', $email)->update([
            'password' => Hash::make($password), // Hash the new password
        ]);

        if (!$userUpdated) {
            Log::error('Password update failed', ['email' => $email]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to update password.',
                'data' => null,
            ], 200); // Internal Server Error status
        }

        // Delete the OTP record after password reset
        DB::table('password_resets')->where('email', $email)->delete();

        // Return success response
        return response()->json([
            'status' => true,
            'message' => 'Password reset successfully.',
            'data' => null,
        ]);
    } catch (Exception $e) {
        // Log the error for debugging
        Log::error('Password reset failed', ['error' => $e->getMessage()]);

        // Return a generic error response
        return response()->json([
            'status' => false,
            'message' => 'An error occurred while resetting the password. Please try again later.',
            'data' => null,
        ], 500); // Internal Server Error status
    }
}

}
