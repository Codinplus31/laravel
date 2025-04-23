<?php

namespace App\Jobs;

use App\Mail\UploadComplete;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $email;
    protected string $token;
    protected string $downloadLink;

    /**
     * Create a new job instance.
     */
    public function __construct(string $email, string $token, string $downloadLink)
    {
        $this->email = $email;
        $this->token = $token;
        $this->downloadLink = $downloadLink;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->email)->send(
            new UploadComplete($this->token, $this->downloadLink)
        );
    }
}