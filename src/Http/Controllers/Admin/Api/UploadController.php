<?php

namespace Shopen\Http\Controllers\Admin\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Shopen\Http\Controller;

class UploadController extends Controller
{
    public function uploadImage(Request $request): JsonResponse
    {
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded.'], 400);
        }

        $file = $request->file('file');

        if (!$file->isValid() || !str_starts_with($file->getMimeType(), 'image/')) {
            return response()->json(['error' => 'Invalid image.'], 400);
        }

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs('uploads', $filename, 'public');

        $width = $height = null;
        try {
            [$width, $height] = getimagesize(asset('/storage/' . $path));
        } catch (\Exception $e) {}

        $bytes = $file->getSize();
        if ($bytes < 1024) {
            $size = $bytes . ' B';
        } elseif ($bytes < 1048576) {
            $size = round($bytes / 1024, 2) . ' KB';
        } else {
            $size = round($bytes / 1048576, 2) . ' MB';
        }

        return response()->json([
            'location' => asset('/storage/' . $path),
            'path' => $path,
            'width' => $width,
            'height' => $height,
            'size' => $size,
        ]);
    }
}