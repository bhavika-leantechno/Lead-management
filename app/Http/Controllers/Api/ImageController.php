<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;

class ImageController extends Controller
{
public function uploadMultipleImages(Request $request)
{
    try {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'images' => 'required|array|min:1', // Ensure 'images' is an array
            'images.*' => 'file|mimes:jpg,jpeg,png,gif|max:2048', // Validate each file
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'data' => $validator->errors(),
            ], 200);
        }

        $uploadedFiles = [];

        // Process each uploaded file
        foreach ($request->file('images') as $file) {
            // Generate a unique filename
            $filename = time() . '_' . $file->getClientOriginalName();

            // Move the file to the uploads directory
            $filePath = $file->storeAs('uploads', $filename, 'public');

            // Construct file details
            $fileDetails = [
                'originalName' => $file->getClientOriginalName(),
                'mimeType' => $file->getMimeType(),
                'size' => $file->getSize(),
                'path' => $filePath,
                'url' => asset('storage/' . $filePath),
            ];

            $uploadedFiles[] = $fileDetails;
        }

        return response()->json([
            'status' => true,
            'message' => 'Images uploaded successfully.',
            'data' => [
                'files' => $uploadedFiles,
            ],
        ], 200);
    } catch (\Exception $e) {
        \Log::error('Error uploading images:', ['error' => $e->getMessage()]);
        return response()->json([
            'status' => false,
            'message' => 'An error occurred while uploading images.',
            'data' => $e->getMessage(),
        ], 500);
    }
}

}
