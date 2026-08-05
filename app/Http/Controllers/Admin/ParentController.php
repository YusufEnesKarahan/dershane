<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentGuardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class ParentController extends Controller
{
    public function show($id)
    {
        $guardian = StudentGuardian::with(['student.classroom', 'user'])->find($id);

        if (!$guardian) {
            $user = User::findOrFail($id);
            $guardian = StudentGuardian::where('user_id', $user->id)->first()
                ?? new StudentGuardian([
                    'guardian_name' => $user->name,
                    'email' => $user->email,
                    'user_id' => $user->id,
                ]);
        }

        $linkedStudents = Student::where(function ($query) use ($guardian) {
            if ($guardian->id) {
                $query->whereHas('guardians', function ($q) use ($guardian) {
                    $q->where('id', $guardian->id);
                });
            }
            if ($guardian->user_id) {
                $query->orWhereHas('guardians', function ($q) use ($guardian) {
                    $q->where('user_id', $guardian->user_id);
                });
            }
            if ($guardian->student_id) {
                $query->orWhere('id', $guardian->student_id);
            }
            if ($guardian->phone) {
                $query->orWhereHas('guardians', function ($q) use ($guardian) {
                    $q->where('phone', $guardian->phone);
                });
            }
            if ($guardian->email) {
                $query->orWhereHas('guardians', function ($q) use ($guardian) {
                    $q->where('email', $guardian->email);
                });
            }
        })->with(['classroom', 'branch'])->get();

        return view('admin.parents.show', compact('guardian', 'linkedStudents'));
    }
}
