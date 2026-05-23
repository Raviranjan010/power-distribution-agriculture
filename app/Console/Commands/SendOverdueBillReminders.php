<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bill;
use App\Notifications\RealTimeNotification;
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
    protected $description = 'Send in-app notification reminders for unpaid bills approaching or past their due date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning for pending/overdue bills requiring reminders...');

        $bills = Bill::whereIn('status', ['pending', 'overdue'])
            ->with(['connection.consumer'])
            ->get();

        $sentCount = 0;
        $now = Carbon::now();

        foreach ($bills as $bill) {
            $consumer = $bill->connection->consumer ?? null;
            if (!$consumer) {
                $this->warn("Bill ID {$bill->id} has no associated consumer. Skipping.");
                continue;
            }

            $dueDate = Carbon::parse($bill->due_date);
            $daysLeft = $now->diffInDays($dueDate, false);

            $isApproaching = ($daysLeft >= 0 && $daysLeft <= 3);
            $isOverdue = ($daysLeft < 0);
            $needsReminder = ($bill->sms_sent_at === null || Carbon::parse($bill->sms_sent_at)->diffInDays($now) >= 7);

            if (($isApproaching || $isOverdue) && $needsReminder) {
                $statusText = $isOverdue
                    ? 'is OVERDUE'
                    : 'is due on ' . $dueDate->format('d M Y');

                $message = sprintf(
                    'Your bill %s of Rs. %s for connection %s %s. Please pay to avoid disconnection.',
                    $bill->bill_number,
                    number_format($bill->net_payable, 2),
                    $bill->connection->connection_number,
                    $statusText
                );

                $consumer->notify(new RealTimeNotification(
                    $isOverdue ? 'Overdue Bill Reminder' : 'Bill Payment Reminder',
                    $message,
                    route('farmer.bills'),
                    'fa-solid fa-triangle-exclamation'
                ));

                $bill->update(['sms_sent_at' => $now]);
                $sentCount++;
                $this->line("Notification sent to {$consumer->name} for Bill {$bill->bill_number}");
            }
        }

        $this->info("Completed! Sent {$sentCount} bill reminder notifications.");
    }
}
