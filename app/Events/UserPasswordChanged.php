<?php
namespace App\Events;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

use App\Models\User; class UserPasswordChanged { public function __construct(public User $user) {} }
