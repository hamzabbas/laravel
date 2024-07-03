<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\SendReminderEmailJob;
use App\Models\Invoices;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Fetch all invoice IDs from your storage
        $invoiceIds = Invoices::pluck('invoice_id')->toArray(); // Adjust this query as per your actual implementation

        // Dispatch a job for each invoice ID
        foreach ($invoiceIds as $invoiceId) {
            $schedule->job(new SendReminderEmailJob($invoiceId))->everyTenMinutes();
        }
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
