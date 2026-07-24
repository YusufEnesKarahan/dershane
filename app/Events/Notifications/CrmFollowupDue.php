<?php
namespace App\Events\Notifications;
use App\Models\LeadActivity;
class CrmFollowupDue { public function __construct(public readonly LeadActivity $activity) {} }
