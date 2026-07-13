<?php

declare(strict_types=1);

namespace App\Core\Enums;

enum UserType: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case TEACHER = 'teacher';
    case STUDENT = 'student';
    case PARENT = 'parent';
}
