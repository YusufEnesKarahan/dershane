<?php
namespace App\Events;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

use App\Models\User; class UserAvatarChanged { public function __construct(public User $user) {} }
