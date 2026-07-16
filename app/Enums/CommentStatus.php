<?php
namespace App\Enums;

enum CommentStatus: string
{
    case Pending = 'Pending';
    case Approved = 'Approved';
    case Spam = 'Spam';
    case Rejected = 'Rejected';
}
