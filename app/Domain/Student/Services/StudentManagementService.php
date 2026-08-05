<?php

namespace App\Domain\Student\Services;

use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\StudentContact;
use App\Models\StudentAddress;
use App\Models\PlatformAuditLog;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class StudentManagementService
{
    /**
     * Get paginated students list with search and filter
     */
    public function getStudents(int $branchId, array $filters = []): LengthAwarePaginator
    {
        $query = Student::where('branch_id', $branchId)->with(['classroom', 'user', 'primaryGuardian']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('student_number', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    /**
     * Create a new student with relations, optional User account and Parent account
     */
    public function createStudent(array $data, int $branchId, int $userId = null): Student
    {
        return DB::transaction(function () use ($data, $branchId, $userId) {
            // 1. Optional Student User Account Creation
            $studentUser = null;
            if (!empty($data['create_user_account']) && !empty($data['user_email']) && !empty($data['user_password'])) {
                $studentUser = User::create([
                    'name' => trim($data['first_name'] . ' ' . $data['last_name']),
                    'email' => $data['user_email'],
                    'password' => bcrypt($data['user_password']),
                    'branch_id' => $branchId,
                    'status' => \App\Enums\UserStatus::ACTIVE ?? 'active',
                ]);
                $studentRole = Role::firstOrCreate(['name' => 'Student']);
                $studentUser->roles()->syncWithoutDetaching([$studentRole->id]);
            }

            $phoneCleaned = $data['phone_cleaned'] ?? (isset($data['phone']) ? preg_replace('/[^0-9]/', '', $data['phone']) : null);
            $guardianPhoneCleaned = $data['guardian_phone_cleaned'] ?? (isset($data['guardian_phone']) ? preg_replace('/[^0-9]/', '', $data['guardian_phone']) : null);

            // 2. Create Student Record
            $student = Student::create([
                'branch_id' => $branchId,
                'user_id' => $studentUser?->id,
                'student_number' => $data['student_number'],
                'identity_number' => $data['identity_number'] ?? null,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'birth_date' => $data['birth_date'] ?? null,
                'gender' => $data['gender'] ?? null,
                'classroom_id' => $data['classroom_id'] ?? null,
                'status' => $data['status'] ?? 'Active',
            ]);

            // 3. Optional Parent User Account Creation
            $parentUser = null;
            if (!empty($data['create_parent_account']) && !empty($data['guardian_email']) && !empty($data['guardian_password'])) {
                $parentUser = User::create([
                    'name' => $data['guardian_name'] ?? ($data['first_name'] . ' Velisi'),
                    'email' => $data['guardian_email'],
                    'password' => bcrypt($data['guardian_password']),
                    'branch_id' => $branchId,
                    'status' => \App\Enums\UserStatus::ACTIVE ?? 'active',
                ]);
                $parentRole = Role::firstOrCreate(['name' => 'Parent']);
                $parentUser->roles()->syncWithoutDetaching([$parentRole->id]);
            }

            // 4. Guardian Record Creation
            if (!empty($data['guardian_name']) || !empty($data['guardian_phone']) || $parentUser) {
                StudentGuardian::create([
                    'student_id' => $student->id,
                    'user_id' => $parentUser?->id,
                    'guardian_name' => $data['guardian_name'] ?? 'Belirtilmedi',
                    'relation' => $data['guardian_relation'] ?? 'Veli',
                    'phone' => $guardianPhoneCleaned ?? $data['guardian_phone'] ?? '',
                    'email' => $data['guardian_email'] ?? null,
                    'is_primary' => true,
                ]);
            }

            // 5. Contact Record
            if (!empty($phoneCleaned) || !empty($data['email'])) {
                StudentContact::create([
                    'student_id' => $student->id,
                    'phone' => $phoneCleaned ?? $data['phone'] ?? null,
                    'email' => $data['email'] ?? null,
                ]);
            }

            // 6. Address Record
            if (!empty($data['city']) || !empty($data['address_text'])) {
                StudentAddress::create([
                    'student_id' => $student->id,
                    'city' => $data['city'] ?? null,
                    'district' => $data['district'] ?? null,
                    'address_text' => $data['address_text'] ?? null,
                ]);
            }

            if ($userId) {
                $this->logActivity('created', Student::class, $student->id, $userId, 'Öğrenci kaydı ve ilişkili hesaplar oluşturuldu.');
            }

            return $student;
        });
    }

    /**
     * Update an existing student
     */
    public function updateStudent(Student $student, array $data, int $userId = null): Student
    {
        return DB::transaction(function () use ($student, $data, $userId) {
            $phoneCleaned = $data['phone_cleaned'] ?? (isset($data['phone']) ? preg_replace('/[^0-9]/', '', $data['phone']) : null);
            $guardianPhoneCleaned = $data['guardian_phone_cleaned'] ?? (isset($data['guardian_phone']) ? preg_replace('/[^0-9]/', '', $data['guardian_phone']) : null);

            $student->update([
                'student_number' => $data['student_number'] ?? $student->student_number,
                'identity_number' => $data['identity_number'] ?? $student->identity_number,
                'first_name' => $data['first_name'] ?? $student->first_name,
                'last_name' => $data['last_name'] ?? $student->last_name,
                'birth_date' => $data['birth_date'] ?? $student->birth_date,
                'gender' => $data['gender'] ?? $student->gender,
                'classroom_id' => $data['classroom_id'] ?? $student->classroom_id,
                'status' => $data['status'] ?? $student->status,
            ]);

            if (isset($data['guardian_name']) || isset($data['guardian_phone'])) {
                $guardian = $student->primaryGuardian ?: new StudentGuardian(['student_id' => $student->id, 'is_primary' => true]);
                $guardian->guardian_name = $data['guardian_name'] ?? $guardian->guardian_name ?? 'Belirtilmedi';
                $guardian->relation = $data['guardian_relation'] ?? $guardian->relation ?? 'Veli';
                $guardian->phone = $guardianPhoneCleaned ?? $data['guardian_phone'] ?? $guardian->phone ?? '';
                $guardian->email = $data['guardian_email'] ?? $guardian->email;
                $guardian->save();
            }

            if (isset($data['phone']) || isset($data['email'])) {
                $contact = $student->contact ?: new StudentContact(['student_id' => $student->id]);
                $contact->phone = $phoneCleaned ?? $data['phone'] ?? $contact->phone;
                $contact->email = $data['email'] ?? $contact->email;
                $contact->save();
            }

            if (isset($data['city']) || isset($data['address_text'])) {
                $address = $student->address ?: new StudentAddress(['student_id' => $student->id]);
                $address->city = $data['city'] ?? $address->city;
                $address->district = $data['district'] ?? $address->district;
                $address->address_text = $data['address_text'] ?? $address->address_text;
                $address->save();
            }

            if ($userId) {
                $this->logActivity('updated', Student::class, $student->id, $userId, 'Öğrenci profili güncellendi.');
            }

            return $student;
        });
    }

    /**
     * Soft delete a student
     */
    public function deleteStudent(Student $student, int $userId = null): void
    {
        $studentId = $student->id;
        $student->delete();

        if ($userId) {
            $this->logActivity('deleted', Student::class, $studentId, $userId, 'Öğrenci kaydı silindi.');
        }
    }

    /**
     * Restore a soft deleted student
     */
    public function restoreStudent(int $id, int $userId = null): void
    {
        $student = Student::withTrashed()->findOrFail($id);
        $student->restore();

        if ($userId) {
            $this->logActivity('restored', Student::class, $student->id, $userId, 'Öğrenci kaydı geri yüklendi.');
        }
    }

    /**
     * Get complete detail of a student for the profile page
     */
    public function getStudentDetail(Student $student): array
    {
        $student->load(['primaryGuardian.user', 'contact', 'address', 'classroom', 'attendances.session', 'statusHistories', 'user']);

        $userAccount = $student->user;
        $primaryGuardian = $student->primaryGuardian;
        $guardianUserAccount = $primaryGuardian?->user;

        $loginStatus = [
            'has_account' => $userAccount !== null,
            'email' => $userAccount?->email ?? '-',
            'user_id' => $userAccount?->id,
            'last_login_at' => $userAccount?->updated_at ?? null,
            'is_active' => $userAccount ? true : false,
        ];

        $parentLoginStatus = [
            'has_account' => $guardianUserAccount !== null,
            'email' => $guardianUserAccount?->email ?? '-',
            'user_id' => $guardianUserAccount?->id,
            'name' => $primaryGuardian?->guardian_name ?? '-',
            'phone' => $primaryGuardian?->phone ?? '-',
            'relation' => $primaryGuardian?->relation ?? 'Veli',
        ];

        $attendances = $student->attendances;
        $present = 0;
        $absent = 0;

        foreach ($attendances as $attendance) {
            if (isset($attendance->status) && $attendance->status->is_absence) {
                $absent++;
            } else {
                $present++;
            }
        }

        $activities = PlatformAuditLog::where('target_type', Student::class)
            ->where('target_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return [
            'student' => $student,
            'user_account' => $userAccount,
            'primary_guardian' => $primaryGuardian,
            'guardian_user_account' => $guardianUserAccount,
            'login_status' => $loginStatus,
            'parent_login_status' => $parentLoginStatus,
            'attendance_summary' => [
                'present' => $present,
                'absent' => $absent,
                'total' => $present + $absent
            ],
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
