<?php

namespace App\Services;

use App\Models\UploadFile;
use App\Models\UploadSession;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    /**
     * Create a new upload session with files
     *
     * @param array $files
     * @param int $expiresIn
     * @param string|null $emailToNotify
     * @param string|null $password
     * @return UploadSession
     */
    public function createUploadSession(
        array $files,
        int $expiresIn = 1,
        ?string $emailToNotify = null,
        ?string $password = null
    ): UploadSession {
        // Create upload session
        $uploadSession = UploadSession::create([
            'token' => Str::random(32),
            'expires_at' => Carbon::now()->addDays($expiresIn),
            'email_to_notify' => $emailToNotify,
            'password' => $password ? Hash::make($password) : null,
        ]);

        // Store files
        foreach ($files as $file) {
            $this->storeFile($uploadSession, $file);
        }

        return $uploadSession;
    }

    /**
     * Store a file in the storage and create a record
     *
     * @param UploadSession $uploadSession
     * @param UploadedFile $file
     * @return UploadFile
     */
    public function storeFile(UploadSession $uploadSession, UploadedFile $file): UploadFile
    {
        // Generate a unique filename
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        
        // Store the file
        $path = $file->storeAs(
            $uploadSession->token,
            $filename,
            'uploads'
        );

        // Create file record
        return $uploadSession->files()->create([
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);
    }

    /**
     * Get upload session by token
     *
     * @param string $token
     * @return UploadSession|null
     */
    public function getUploadSessionByToken(string $token): ?UploadSession
    {
        return UploadSession::where('token', $token)
            ->with('files')
            ->first();
    }

    /**
     * Delete expired upload sessions
     *
     * @return int Number of deleted sessions
     */
    public function deleteExpiredSessions(): int
    {
        $expiredSessions = UploadSession::where('expires_at', '<', Carbon::now())->get();
        
        foreach ($expiredSessions as $session) {
            // Delete all files from storage
            Storage::disk('uploads')->deleteDirectory($session->token);
            
            // Delete the session (cascade will delete files records)
            $session->delete();
        }

        return $expiredSessions->count();
    }
}