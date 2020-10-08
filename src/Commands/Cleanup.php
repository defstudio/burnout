<?php

namespace DefStudio\Burnout\Commands;

use DefStudio\Burnout\Models\BurnoutEntry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Cleanup extends Command
{
    protected $signature = "burnout:cleanup {older_than_days?}";

    protected $description = 'Cleans old Burnout records';

    public function handle()
    {

        $older_than_days = $this->hasArgument('older_than_days')
            ? $this->argument('older_than_days')
            : config('burnout.delete_logs_older_than_days');

        BurnoutEntry::where('created_at', '<', now()->subDays($older_than_days))->delete();

        Log::debug('Burnout cleanup completed');
    }
}
