<?php
namespace App\Http\Controllers; // Ensure the correct namespace is used

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class OtpVerificationController extends Controller
{
   public function verifyOtp(Request $request)
{
    try {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'data' => $validator->errors(),
            ], 200); // Unprocessable Entity status
        }

        // Retrieve input data
        $email = $request->input('email');
        $otp = $request->input('otp');

        // Retrieve the OTP data from the database
        $resetData = DB::table('password_resets')->where('email', $email)->first();

        // Check if the OTP exists and is not expired
        if (!$resetData || $resetData->otp !== $otp || $resetData->expiry_time < now()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired OTP.',
                'data' => null,
            ], 200); // Bad Request status
        }

        // OTP is valid, allow user to reset password
        return response()->json([
            'status' => true,
            'message' => 'OTP verified successfully. You can now reset your password.',
            'data' => null,
        ]);
    } catch (\Exception $e) {
        // Handle unexpected errors
        return response()->json([
            'status' => false,
            'message' => 'An error occurred while verifying the OTP.',
            'data' => $e->getMessage(),
        ], 500); // Internal Server Error status
    }
}

}
