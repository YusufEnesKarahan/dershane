<?php

namespace App\Models\Traits;

use App\Models\Teacher;
use App\Models\Student;
use App\Models\StudentGuardian;

trait HasProfiles
{
    /**
     * Get the teacher profile associated with the user.
     */
    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    /**
     * Get the student profile associated with the user.
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Get the guardian profile associated with the user.
     */
    public function guardian()
    {
        return $this->hasOne(StudentGuardian::class);
    }

    /**
     * Retrieve the active profile instance based on the user's role.
     * Helpful for dynamic profile resolution.
     */
    public function getActiveProfile()
    {
        if ($this->hasRole('Teacher')) {
            return $this->teacher;
        }

        if ($this->hasRole('Student')) {
            return $this->student;
        }

        if ($this->hasRole('Parent')) {
            return $this->guardian;
        }

        return null;
    }
}
