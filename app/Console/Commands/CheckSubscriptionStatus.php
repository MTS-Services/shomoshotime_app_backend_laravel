<?php

namespace App\Console\Commands;

use App\Jobs\HandleSubscriptionStatus;
use Illuminate\Console\Command;

class CheckSubscriptionStatus extends Command
{
    protected $signature = 'subscriptions:check-status {--sync : Run synchronously}';
    protected $description = 'Check subscription status and update user premium status';

    public function handle()
    {
        $this->info('Checking subscription status...');

        try {
            if ($this->option('sync')) {
                $this->info('Running synchronously...');
                (new HandleSubscriptionStatus())->handle();
                $this->info('✓ Completed successfully.');
            } else {
                HandleSubscriptionStatus::dispatch();
                $this->info('✓ Job dispatched to queue.');
            }
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('✗ Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}