<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use Carbon\Carbon;
use App\Models\Notification;
use App\Models\User;

class SubscriptionNotifyCommand extends Command
{
    protected $signature = 'subscription:notify';
    protected $description = 'Send renewal reminders based on timeline (30, 15, 7, 3, 1 days)';

    public function handle(): void
    {
        $this->info('Checking subscriptions for upcoming expirations...');
        
        $intervals = [30, 15, 7, 3, 1];
        $today = Carbon::today();

        foreach ($intervals as $days) {
            $targetDate = $today->copy()->addDays($days);
            
            $subscriptions = Subscription::with('branch')
                ->whereIn('status', ['active', 'grace'])
                ->whereNotNull('expires_at')
                ->whereDate('expires_at', $targetDate)
                ->get();

            foreach ($subscriptions as $subscription) {
                if (!$subscription->branch) continue;

                $managers = User::where('branch_id', $subscription->branch_id)->whereHas('roles', function($q) {
                    $q->where('name', 'Admin');
                })->get();
        
                foreach ($managers as $manager) {
                    Notification::create([
                        'user_id' => $manager->id,
                        'title' => 'Abonelik Süresi Bitiyor',
                        'message' => "Aboneliğinizin bitmesine {$days} gün kaldı. Lütfen hizmet kesintisi yaşamamak için yenileyiniz.",
                        'type' => 'system',
                        'is_read' => false,
                    ]);
                }
            }
        }
        $this->info('Notifications sent successfully.');
    }
}
