<?php

namespace App\Console\Commands;

use App\Services\FileService;
use Illuminate\Console\Command;

class CleanExpiredUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:expired-uploads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired upload sessions and their files';

    /**
     * Execute the console command.
     */
    public function handle(FileService $fileService): int
    {
        $this->info('Cleaning expired uploads...');
        
        $count = $fileService->deleteExpiredSessions();
        
        $this->info("Deleted {$count} expired upload sessions.");
        
        return Command::SUCCESS;
    }
}