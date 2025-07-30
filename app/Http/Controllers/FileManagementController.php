<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\FileUploadRequest;
use App\Services\FileManagementService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileManagementController extends Controller
{
    protected $fileService;

    public function __construct(FileManagementService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Show the file management dashboard
     */
    public function showDashboard()
    {
        if (!Auth::check() || !Auth::user()->status) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        return view('file-management.dashboard');
    }

    /**
     * Upload a single file
     */
    public function uploadFile(FileUploadRequest $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 401);
        }

        try {
            $file = $request->file('file');
            $category = $request->input('category', 'all');
            $directory = $request->input('directory', 'uploads');

            $result = $this->fileService->uploadFile($file, $category, $directory);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully.',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('File upload failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'file' => $request->file('file')?->getClientOriginalName()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload multiple files
     */
    public function uploadMultipleFiles(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 401);
        }

        $request->validate([
            'files.*' => 'required|file|max:10240',
            'category' => 'nullable|string|in:images,documents,certificates,photos,all',
            'directory' => 'nullable|string|max:255|regex:/^[a-zA-Z0-9\/_-]+$/',
        ]);

        try {
            $files = $request->file('files');
            $category = $request->input('category', 'all');
            $directory = $request->input('directory', 'uploads');

            $result = $this->fileService->uploadMultipleFiles($files, $category, $directory);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] ? 'Files uploaded successfully.' : 'Some files failed to upload.',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Multiple file upload failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'File upload failed.'
            ], 500);
        }
    }

    /**
     * Download a file
     */
    public function downloadFile(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 401);
        }

        $request->validate([
            'filename' => 'required|string',
            'directory' => 'nullable|string|max:255',
        ]);

        try {
            $filename = $request->input('filename');
            $directory = $request->input('directory', 'uploads');
            $path = $directory . '/' . $filename;

            if (!Storage::disk('public')->exists($path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found.'
                ], 404);
            }

            return Storage::disk('public')->download($path);

        } catch (\Exception $e) {
            Log::error('File download failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'filename' => $request->input('filename')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'File download failed.'
            ], 500);
        }
    }

    /**
     * Delete a file
     */
    public function deleteFile(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 401);
        }

        $request->validate([
            'filename' => 'required|string',
            'directory' => 'nullable|string|max:255',
        ]);

        try {
            $filename = $request->input('filename');
            $directory = $request->input('directory', 'uploads');

            $deleted = $this->fileService->deleteFile($filename, $directory);

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'File deleted successfully.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found or could not be deleted.'
                ], 404);
            }

        } catch (\Exception $e) {
            Log::error('File deletion failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'filename' => $request->input('filename')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'File deletion failed.'
            ], 500);
        }
    }

    /**
     * Delete multiple files
     */
    public function deleteMultipleFiles(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 401);
        }

        $request->validate([
            'filenames' => 'required|array',
            'filenames.*' => 'string',
            'directory' => 'nullable|string|max:255',
        ]);

        try {
            $filenames = $request->input('filenames');
            $directory = $request->input('directory', 'uploads');

            $result = $this->fileService->deleteMultipleFiles($filenames, $directory);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] ? 'Files deleted successfully.' : 'Some files could not be deleted.',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Multiple file deletion failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'File deletion failed.'
            ], 500);
        }
    }

    /**
     * Get file information
     */
    public function getFileInfo(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 401);
        }

        $request->validate([
            'filename' => 'required|string',
            'directory' => 'nullable|string|max:255',
        ]);

        try {
            $filename = $request->input('filename');
            $directory = $request->input('directory', 'uploads');

            $fileInfo = $this->fileService->getFileInfo($filename, $directory);

            if ($fileInfo) {
                return response()->json([
                    'success' => true,
                    'data' => $fileInfo
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found.'
                ], 404);
            }

        } catch (\Exception $e) {
            Log::error('Get file info failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'filename' => $request->input('filename')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get file information.'
            ], 500);
        }
    }

    /**
     * Get storage statistics
     */
    public function getStorageStats(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 401);
        }

        $request->validate([
            'directory' => 'nullable|string|max:255',
        ]);

        try {
            $directory = $request->input('directory', 'uploads');

            $stats = $this->fileService->getStorageStats($directory);

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Get storage stats failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get storage statistics.'
            ], 500);
        }
    }

    /**
     * Clean up orphaned files
     */
    public function cleanupOrphanedFiles(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 401);
        }

        $request->validate([
            'directory' => 'nullable|string|max:255',
            'days_old' => 'nullable|integer|min:1|max:365',
        ]);

        try {
            $directory = $request->input('directory', 'uploads');
            $daysOld = $request->input('days_old', 30);

            $result = $this->fileService->cleanupOrphanedFiles($directory, $daysOld);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] ? 'Cleanup completed successfully.' : 'Cleanup failed.',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Cleanup failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Cleanup failed.'
            ], 500);
        }
    }

    /**
     * List files in directory
     */
    public function listFiles(Request $request)
    {
        if (!Auth::check() || !Auth::user()->status) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 401);
        }

        $request->validate([
            'directory' => 'nullable|string|max:255',
        ]);

        try {
            $directory = $request->input('directory', 'uploads');
            $files = Storage::disk('public')->files($directory);
            $fileList = [];

            foreach ($files as $file) {
                $filename = basename($file);
                $fileInfo = $this->fileService->getFileInfo($filename, $directory);
                if ($fileInfo) {
                    $fileList[] = $fileInfo;
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'directory' => $directory,
                    'files' => $fileList,
                    'count' => count($fileList)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('List files failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to list files.'
            ], 500);
        }
    }
}
