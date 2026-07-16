<?php
namespace App\Enums;

enum BlogStatus: string
{
    case Draft = 'Draft';
    case Review = 'Review';
    case Scheduled = 'Scheduled';
    case Published = 'Published';
    case Archived = 'Archived';
    case Rejected = 'Rejected';
}
