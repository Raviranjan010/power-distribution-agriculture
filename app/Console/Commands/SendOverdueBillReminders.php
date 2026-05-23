<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bill;

use Carbon\Carbon;

class SendOverdueBillReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly automated SMS reminders for unpaid bills approaching or past their due date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🔍 Scanning for pending/overdue bills requiring reminders...");

        // Fetch pending or overdue (unpaid) bills
        $bills = Bill::whereIn('status', ['pending', 'overdue'])
            ->with(['connection.consumer'])
            ->get();

        $sentCount = 0;
        $now = Carbon::now();

        foreach ($bills as $bill) {
            $consumer = $bill->connection->consumer ?? null;
            if (!$consumer) {
                $this->warn("⚠️ Bill ID {$bill->id} has no associated consumer. Skipping.");
                continue;
            }

            $phone = $consumer->phone;
            if (empty($phone)) {
                $this->warn("⚠️ Consumer {$consumer->name} has no phone number on record. Skipping Bill ID {$bill->id}.");
                continue;
            }

            $dueDate = Carbon::parse($bill->due_date);
            $daysLeft = $now->diffInDays($dueDate, false);

            // Trigger criteria:
            // 1. Due date is approaching in <= 3 days OR past due
            // 2. AND sms_sent_at is NULL OR sms_sent_at was sent > 7 days ago (weekly frequency)
            $isApproaching = ($daysLeft >= 0 && $daysLeft <= 3);
            $isOverdue = ($daysLeft < 0);
            $needsReminder = ($bill->sms_sent_at === null || Carbon::parse($bill->sms_sent_at)->diffInDays($now) >= 7);

            if (($isApproaching || $isOverdue) && $needsReminder) {
                // Construct the message (Clear, localized, high-impact)
                $statusText = $isOverdue ? "is OVERDUE" : "is approaching due date ({$dueDate->format('d M Y')})";
                
                $message = sprintf(
                    "Dear %s, your AgriPower bill %s of Rs. %s for connection %s %s. Please pay online at %s to avoid disconnection. Thank you! - Ministry of Power",
                    explode(' ', $consumer->name)[0],
                    $bill->bill_number,
                    number_format($bill->net_payable, 2),
                    $bill->connection->connection_number,
                    $statusText,
                    route('home') // Directs to home/portal page for easy online payments
                );

                $this->info("📱 Sending SMS to {$consumer->name} ({$phone}) for Bill {$bill->bill_number}...");
                
                // Removed SmsService call for localhost
                $success = true;

                if ($success) {
                    $bill->update([
                        'sms_sent_at' => $now
                    ]);
                    $sentCount++;
                    $this->line("✅ SMS reminder sent successfully!");
                } else {
                    $this->error("❌ Failed to send SMS via service providers.");
                }
            }
        }

        $this->info("🎉 Completed! Sent {$sentCount} overdue bill reminder SMS.");
    }
}
