<?php
namespace App\Enums;

enum UserStatus: string
{
    case ACTIVE = 'ACTIVE';
    case PASSIVE = 'PASSIVE';
    case SUSPENDED = 'SUSPENDED';
}
