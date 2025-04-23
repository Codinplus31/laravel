<?php

namespace App\Http\Controllers;

use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class DownloadController extends Controller
{
    protected FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Download files
     *
     * @param Request $request
     * @param string $token
     * @return \Illuminate\Http\Response|StreamedResponse
     */
    public function download(Request $request, string $token)
    {
        $uploadSession = $this->fileService->getUploadSessionByToken($token);

        if (!$uploadSession) {
            return response()->json([
                'success' => false,
                'message' => 'Download link not found or expired',
            ], 404);
        }

        if ($uploadSession->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'Download link has expired',
            ], 410);
        }

        // Check password if protected
        if ($uploadSession->isPasswordProtected()) {
            $password = $request->input('password');
            
            if (!$password || !$uploadSession->verifyPassword($password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password required or incorrect',
                    'requires_password' => true,
                ], 401);
            }
        }

        // Increment download count
        $uploadSession->incrementDownloadCount();

        // If there's only one file, return it directly
        if ($uploadSession->files->count() === 1) {
            $file = $uploadSession->files->first();
            
            return Storage::disk('uploads')->download(
                $file->getStoragePath(),
                $file->name,
                [
                    'Content-Type' => $file->mime_type,
                    'Content-Disposition' => 'attachment; filename="' . $file->name . '"',
                ]
            );
        }

        // If there are multiple files, create a zip archive
        $zipName = 'download-' . $uploadSession->token . '.zip';
        $zipPath = storage_path('app/temp/' . $zipName);
        
        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($uploadSession->files as $file) {
            $filePath = Storage::disk('uploads')->path($file->getStoragePath());
            $zip->addFile($filePath, $file->name);
        }

        $zip->close();

        return response()->download($zipPath, $zipName, [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="' . $zipName . '"',
        ])->deleteFileAfterSend(true);
    }
}