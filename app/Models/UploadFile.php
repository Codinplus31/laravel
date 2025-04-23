<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class UploadFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'upload_session_id',
        'name',
        'path',
        'size',
        'mime_type',
    ];

    public function uploadSession(): BelongsTo
    {
        return $this->belongsTo(UploadSession::class);
    }

    public function getStoragePath(): string
    {
        return $this->path;
    }

    public function delete()
    {
        // Delete the file from storage
        Storage::disk('uploads')->delete($this->path);
        
        // Delete the model
        return parent::delete();
    }
}