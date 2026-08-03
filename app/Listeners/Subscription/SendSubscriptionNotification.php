<?php

namespace App\Listeners\Subscription;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Notification;
use App\Models\User;

class SendSubscriptionNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(object $event): void
    {
        $subscription = $event->subscription;
        $branch = $subscription->branch;
        
        if (!$branch) return;

        $eventName = class_basename($event);
        
        $title = "Abonelik Güncellemesi";
        $message = "Aboneliğiniz güncellendi.";

        switch ($eventName) {
            case 'SubscriptionCreated':
                $message = "Yeni aboneliğiniz oluşturuldu.";
                break;
            case 'SubscriptionActivated':
                $message = "Aboneliğiniz aktif edildi.";
                break;
            case 'SubscriptionRenewed':
                $message = "Aboneliğiniz yenilendi.";
                break;
            case 'SubscriptionExpired':
                $title = "Abonelik Süresi Doldu";
                $message = "Abonelik süreniz dolmuştur. Lütfen yenileyiniz.";
                break;
            case 'SubscriptionSuspended':
                $title = "Hesap Askıya Alındı";
                $message = "Abonelik yenilenmediği için hesabınız askıya alınmıştır.";
                break;
        }

        // Send to branch managers or super admins
        $managers = User::where('branch_id', $branch->id)->whereHas('roles', function($q) {
            $q->where('name', 'Admin');
        })->get();

        foreach ($managers as $manager) {
            Notification::create([
                'user_id' => $manager->id,
                'title' => $title,
                'message' => $message,
                'type' => 'system',
                'is_read' => false,
            ]);
        }
    }
}
