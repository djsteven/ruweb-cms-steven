<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMediaRequest;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $query = Media::query()->latest();

        if ($request->filled('type')) {
            $query->where('mime_type', 'like', $request->input('type') . '/%');
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('original_filename', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('alt', 'like', "%{$search}%");
            });
        }

        $perPage = 24;
        if ($request->wantsJson()) {
            $perPage = min(max((int) $request->input('per_page', 20), 1), 100);
        }

        $media = $query->paginate($perPage)->withQueryString();

        if ($request->wantsJson()) {
            return response()->json($media);
        }

        return view('admin.media.index', compact('media'));
    }

    public function store(StoreMediaRequest $request): JsonResponse
    {
        $uploadedFiles = $request->file('files') ?: [$request->file('file')];
        $uploadedFiles = array_values(array_filter((array) $uploadedFiles));

        $allowedExtensions = config('cms.upload.allowed_extensions');
        $createdMedia = [];

        foreach ($uploadedFiles as $file) {
            $extension = strtolower($file->getClientOriginalExtension());
            if (! in_array($extension, $allowedExtensions)) {
                return response()->json(['error' => __('admin.media_error_extension_not_allowed')], 422);
            }

            if ($extension === 'svg') {
                $content = file_get_contents($file->getRealPath());
                if (preg_match('/<script|on\w+\s*=/i', $content)) {
                    return response()->json(['error' => __('admin.media_error_svg_malicious')], 422);
                }
            }

            $filename = Str::uuid() . '.' . $extension;
            $directory = 'media/' . now()->format('Y/m');
            $path = $file->storeAs($directory, $filename, 'public');

            $createdMedia[] = Media::create([
                'filename' => $filename,
                'original_filename' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'extension' => $extension,
                'size' => $file->getSize(),
                'alt' => $request->input('alt'),
                'title' => $request->input('title', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)),
                'disk' => 'public',
                'uploaded_by' => $request->user()->id,
            ]);
        }

        if (count($createdMedia) === 1) {
            return response()->json($createdMedia[0], 201);
        }

        return response()->json(['data' => $createdMedia], 201);
    }

    public function show(Media $media): JsonResponse
    {
        return response()->json($media);
    }

    public function update(Request $request, Media $media): JsonResponse
    {
        $validated = $request->validate([
            'alt' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $media->update($validated);

        return response()->json($media);
    }

    public function destroy(Media $media): JsonResponse
    {
        Storage::disk($media->disk)->delete($media->path);
        $media->delete();

        return response()->json(['message' => __('admin.media_deleted')]);
    }
}
