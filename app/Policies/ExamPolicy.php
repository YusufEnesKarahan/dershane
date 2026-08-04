<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Exam;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Domain\Auth\Dictionaries\PermissionDictionary;

class ExamPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission(PermissionDictionary::EXAM_VIEW);
    }

    public function view(User $user, Exam $exam)
    {
        return $user->hasPermission(PermissionDictionary::EXAM_VIEW) && $user->branch_id === $exam->branch_id;
    }

    public function create(User $user)
    {
        return $user->hasPermission(PermissionDictionary::EXAM_CREATE);
    }

    public function update(User $user, Exam $exam)
    {
        return $user->hasPermission(PermissionDictionary::EXAM_UPDATE) && $user->branch_id === $exam->branch_id;
    }

    public function delete(User $user, Exam $exam)
    {
        return $user->hasPermission(PermissionDictionary::EXAM_DELETE) && $user->branch_id === $exam->branch_id;
    }

    public function publish(User $user, Exam $exam)
    {
        return $user->hasPermission(PermissionDictionary::EXAM_PUBLISH) && $user->branch_id === $exam->branch_id;
    }
    
    public function report(User $user, Exam $exam)
    {
        return $user->hasPermission(PermissionDictionary::EXAM_REPORT) && $user->branch_id === $exam->branch_id;
    }
}
