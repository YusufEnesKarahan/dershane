<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ExamResult;
use App\Models\Exam;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Domain\Auth\Dictionaries\PermissionDictionary;

class ExamResultPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission(PermissionDictionary::EXAM_RESULT_VIEW);
    }

    public function view(User $user, ExamResult $result)
    {
        if ($user->hasPermission(PermissionDictionary::EXAM_RESULT_VIEW) && $user->branch_id === $result->branch_id) {
            return true;
        }

        // Student can view their own result
        if ($user->student && $user->student->id === $result->student_id) {
            return true;
        }
        
        // Parent can view their children's results
        if ($user->guardian) {
            $studentIds = $user->guardian->students()->pluck('students.id')->toArray();
            if (in_array($result->student_id, $studentIds)) {
                return true;
            }
        }
        
        return false;
    }

    public function create(User $user, Exam $exam)
    {
        // Teachers can create results if they are permitted and the branch matches
        return $user->hasPermission(PermissionDictionary::EXAM_RESULT_CREATE) && $user->branch_id === $exam->branch_id;
    }
}
