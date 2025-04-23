<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadRequest;
use App\Jobs\SendEmailNotification;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;

class UploadController extends Controller
{
    protected FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Handle file upload
     *
     * @param UploadRequest $request
     * @return JsonResponse
     */
    public function upload(UploadRequest $request): JsonResponse
    {
        $files = $request->file('files');
        $expiresIn = $request->input('expires_in', 1);
        $emailToNotify = $request->input('email_to_notify');
        $password = $request->input('password');

        $uploadSession = $this->fileService->createUploadSession(
            $files,
            $expiresIn,
            $emailToNotify,
            $password
        );

        // Queue email notification if email is provided
        if ($emailToNotify) {
            SendEmailNotification::dispatch(
                $emailToNotify,
                $uploadSession->token,
                URL::to('/api/download/' . $uploadSession->token)
            );
        }

        return response()->json([
            'success' => true,
            'download_link' => URL::to('/api/download/' . $uploadSession->token),
            'stats_link' => URL::to('/api/uploads/stats/' . $uploadSession->token),
        ]);
    }

    /**
     * Get upload stats
     *
     * @param string $token
     * @return JsonResponse
     */
    public function stats(string $token): JsonResponse
    {
        $uploadSession = $this->fileService->getUploadSessionByToken($token);

        if (!$uploadSession) {
            return response()->json([
                'success' => false,
                'message' => 'Upload not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'files' => $uploadSession->files->map(function ($file) {
                    return [
                        'name' => $file->name,
                        'size' => $file->size,
                        'mime_type' => $file->mime_type,
                    ];
                }),
                'total_size' => $uploadSession->getTotalSize(),
                'download_count' => $uploadSession->download_count,
                'expires_at' => $uploadSession->expires_at,
                'created_at' => $uploadSession->created_at,
                'is_password_protected' => $uploadSession->isPasswordProtected(),
            ],
        ]);
    }
}