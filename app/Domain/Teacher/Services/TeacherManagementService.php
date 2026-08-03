<?php

namespace App\Domain\Teacher\Services;

use App\Models\Teacher;
use App\Models\User;
use App\Models\Role;
use App\Models\PlatformAuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Enums\UserStatus;

class TeacherManagementService
{
    /**
     * Get paginated teachers list with search and filter
     */
    public function getTeachers(int $branchId, array $filters = []): LengthAwarePaginator
    {
        $query = Teacher::where('branch_id', $branchId)->with(['user']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('title', 'like', "%{$search}%");
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    /**
     * Create a new teacher (creates both User and Teacher profile)
     */
    public function createTeacher(array $data, int $branchId, int $adminId = null): Teacher
    {
        return DB::transaction(function () use ($data, $branchId, $adminId) {
            // Create user
            $fullName = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
            
            $user = User::create([
                'name' => $fullName,
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password'] ?? Str::random(12)),
                'status' => ($data['status'] ?? 'Active') === 'Active' ? UserStatus::ACTIVE : UserStatus::INACTIVE,
                'branch_id' => $branchId,
            ]);

            // Assign teacher role
            $teacherRole = Role::where('name', 'teacher')->first();
            if ($teacherRole) {
                $user->roles()->attach($teacherRole->id);
            }

            // Create teacher profile
            $teacher = Teacher::create([
                'user_id' => $user->id,
                'branch_id' => $branchId,
                'title' => $data['title'] ?? null,
                'specialties' => $data['specialties'] ?? null,
                'bio' => $data['bio'] ?? null,
                'education' => $data['education'] ?? null,
                'experience_years' => $data['experience_years'] ?? 0,
                'birth_date' => $data['birth_date'] ?? null,
                'gender' => $data['gender'] ?? null,
                'status' => $data['status'] ?? 'Active',
            ]);

            if ($adminId) {
                $this->logActivity('created', Teacher::class, $teacher->id, $adminId, 'Öğretmen kaydı ve kullanıcı hesabı oluşturuldu.');
            }

            return $teacher;
        });
    }

    /**
     * Update an existing teacher
     */
    public function updateTeacher(Teacher $teacher, array $data, int $adminId = null): Teacher
    {
        return DB::transaction(function () use ($teacher, $data, $adminId) {
            // Update User
            $user = $teacher->user;
            if ($user) {
                $fullName = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
                $user->name = $fullName ?: $user->name;
                if (isset($data['email'])) {
                    $user->email = $data['email'];
                }
                if (isset($data['phone'])) {
                    $user->phone = $data['phone'];
                }
                if (isset($data['status'])) {
                    $user->status = $data['status'] === 'Active' ? UserStatus::ACTIVE : UserStatus::INACTIVE;
                }
                if (!empty($data['password'])) {
                    $user->password = Hash::make($data['password']);
                }
                $user->save();
            }

            // Update Teacher Profile
            $teacher->update([
                'title' => $data['title'] ?? $teacher->title,
                'specialties' => $data['specialties'] ?? $teacher->specialties,
                'bio' => $data['bio'] ?? $teacher->bio,
                'education' => $data['education'] ?? $teacher->education,
                'experience_years' => $data['experience_years'] ?? $teacher->experience_years,
                'birth_date' => $data['birth_date'] ?? $teacher->birth_date,
                'gender' => $data['gender'] ?? $teacher->gender,
                'status' => $data['status'] ?? $teacher->status,
            ]);

            if ($adminId) {
                $this->logActivity('updated', Teacher::class, $teacher->id, $adminId, 'Öğretmen profili güncellendi.');
            }

            return $teacher;
        });
    }

    /**
     * Soft delete a teacher
     */
    public function deleteTeacher(Teacher $teacher, int $adminId = null): void
    {
        DB::transaction(function () use ($teacher, $adminId) {
            $teacherId = $teacher->id;
            
            // Soft delete user as well
            if ($teacher->user) {
                $teacher->user->delete();
            }
            
            $teacher->delete();

            if ($adminId) {
                $this->logActivity('deleted', Teacher::class, $teacherId, $adminId, 'Öğretmen kaydı ve hesabı silindi.');
            }
        });
    }

    /**
     * Restore a soft deleted teacher
     */
    public function restoreTeacher(int $id, int $adminId = null): void
    {
        DB::transaction(function () use ($id, $adminId) {
            $teacher = Teacher::withTrashed()->findOrFail($id);
            
            if ($teacher->user()->withTrashed()->first()) {
                $teacher->user()->withTrashed()->first()->restore();
            }
            
            $teacher->restore();

            if ($adminId) {
                $this->logActivity('restored', Teacher::class, $teacher->id, $adminId, 'Öğretmen kaydı geri yüklendi.');
            }
        });
    }

    /**
     * Get complete detail of a teacher for the profile page
     */
    public function getTeacherDetail(Teacher $teacher): array
    {
        $teacher->load(['user', 'branch']);

        $activities = PlatformAuditLog::where('target_type', Teacher::class)
            ->where('target_id', $teacher->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return [
            'teacher' => $teacher,
            'recent_activities' => $activities,
        ];
    }

    private function logActivity(string $action, string $targetType, int $targetId, int $userId, string $details): void
    {
        try {
            PlatformAuditLog::create([
                'user_id' => $userId,
                'action' => $action,
                'target_type' => $targetType,
                'target_id' => $targetId,
                'details' => $details,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Ignore audit log failures
        }
    }
}
