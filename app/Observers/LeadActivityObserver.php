<?php
namespace App\Observers;
use App\Models\LeadActivity;
class LeadActivityObserver { public function created(LeadActivity $activity): void { if (str_contains(strtolower($activity->action_type), 'follow')) event(new \App\Events\Notifications\CrmFollowupDue($activity)); } }
