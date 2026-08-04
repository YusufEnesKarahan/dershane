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
        return $user->hasPermission(PermissionDictionary::EXAMS_VIEW);
    }

    public function view(User $user, Exam $exam)
    {
        return $user->hasPermission(PermissionDictionary::EXAMS_VIEW) && $user->branch_id === $exam->branch_id;
    }

    public function create(User $user)
    {
        \Illuminate\Support\Facades\Log::info('Policy create method called', [
            'user' => $user->id, 
            'has_permission' => $user->hasPermission(PermissionDictionary::EXAMS_CREATE)
        ]);
        return $user->hasPermission(PermissionDictionary::EXAMS_CREATE);
    }

    public function update(User $user, Exam $exam)
    {
        return $user->hasPermission(PermissionDictionary::EXAMS_UPDATE) && $user->branch_id === $exam->branch_id;
    }

    public function delete(User $user, Exam $exam)
    {
        return $user->hasPermission(PermissionDictionary::EXAMS_DELETE) && $user->branch_id === $exam->branch_id;
    }

    public function results(User $user, Exam $exam)
    {
        return $user->hasPermission(PermissionDictionary::EXAMS_RESULTS) && $user->branch_id === $exam->branch_id;
    }
}
