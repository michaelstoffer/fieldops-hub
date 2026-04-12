<?php

namespace App\Console\Commands;

use App\Mail\InvoiceReminderMail;
use App\Models\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendInvoiceReminders extends Command
{
    protected $signature = 'invoices:send-reminders
                            {--days=7 : Send reminder if invoice has been unpaid for this many days}';

    protected $description = 'Send payment reminder emails for unpaid invoices';

    public function handle(): int
    {
        $days = (int) $this->option('days');

        $invoices = Invoice::with(['customer'])
            ->whereIn('status', [Invoice::STATUS_SENT, Invoice::STATUS_PARTIAL, Invoice::STATUS_OVERDUE])
            ->whereNotNull('sent_at')
            ->whereDate('sent_at', '<=', now()->subDays($days))
            ->whereHas('customer', fn ($q) => $q->whereNotNull('email'))
            ->get();

        $count = 0;

        foreach ($invoices as $invoice) {
            Mail::to($invoice->customer->email)->queue(new InvoiceReminderMail($invoice));
            $count++;
        }

        $this->info("Queued {$count} invoice reminder(s).");

        return self::SUCCESS;
    }
}
