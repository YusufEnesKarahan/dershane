<?php
namespace App\Policies;
use App\Models\User;
class JobHistoryPolicy { public function manage(User $user): bool { return $user->hasPermission('system.jobs.manage'); } }
