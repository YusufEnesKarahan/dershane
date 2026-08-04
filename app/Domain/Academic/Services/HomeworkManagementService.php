<?php

namespace App\Domain\Academic\Services;

use App\Models\Homework;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Domain\Notification\Services\NotificationService;
use App\Domain\Notification\Enums\NotificationType;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class HomeworkManagementService
{
    public function __construct(protected NotificationService $notificationService)
    {
    }

    public function createHomework(array $data): Homework
    {
        return DB::transaction(function () use ($data) {
            $homework = Homework::create($data);
            
            if (isset($data['status']) && $data['status'] === 'published') {
                $this->publishHomework($homework);
            }
            
            return $homework;
        });
    }

    public function updateHomework(Homework $homework, array $data): Homework
    {
        return DB::transaction(function () use ($homework, $data) {
            $homework->update($data);
            
            if (isset($data['status']) && $data['status'] === 'published' && $homework->getOriginal('status') !== 'published') {
                $this->publishHomework($homework);
            }
            
            return $homework;
        });
    }

    public function deleteHomework(Homework $homework): bool
    {
        return DB::transaction(function () use ($homework) {
            return $homework->delete();
        });
    }

    public function publishHomework(Homework $homework): Homework
    {
        return DB::transaction(function () use ($homework) {
            $homework->update([
                'status' => 'published',
                'publish_at' => now(),
            ]);

            // Notify students and parents
            $students = Student::where('classroom_id', $homework->classroom_id)->get();
            
            foreach ($students as $student) {
                $this->notificationService->sendToStudent(
                    $student,
                    "Yeni Ödev: {$homework->title}",
                    "{$homework->course->name} dersi için yeni bir ödev eklendi. Son teslim tarihi: {$homework->due_date->format('d.m.Y H:i')}",
                    NotificationType::SYSTEM
                );
                
                foreach ($student->guardians as $guardian) {
                    $this->notificationService->sendToParent(
                        $guardian,
                        "Çocuğunuz için Yeni Ödev: {$homework->title}",
                        "{$student->user->name} adlı öğrenci için {$homework->course->name} dersinden yeni bir ödev verildi.",
                        NotificationType::SYSTEM
                    );
                }
            }

            return $homework;
        });
    }

    public function closeHomework(Homework $homework): Homework
    {
        return DB::transaction(function () use ($homework) {
            $homework->update(['status' => 'closed']);
            return $homework;
        });
    }

    public function duplicateHomework(Homework $homework, array $overrides = []): Homework
    {
        return DB::transaction(function () use ($homework, $overrides) {
            $newHomework = $homework->replicate();
            $newHomework->title = $homework->title . ' (Kopya)';
            $newHomework->status = 'draft';
            $newHomework->publish_at = null;
            
            foreach ($overrides as $key => $value) {
                $newHomework->$key = $value;
            }
            
            $newHomework->save();
            return $newHomework;
        });
    }

    public function attachFiles(Homework $homework, array $fileDataList): void
    {
        DB::transaction(function () use ($homework, $fileDataList) {
            foreach ($fileDataList as $fileData) {
                $homework->files()->create([
                    'branch_id' => $homework->branch_id,
                    'disk' => $fileData['disk'] ?? 'public',
                    'path' => $fileData['path'],
                    'original_name' => $fileData['original_name'],
                    'mime_type' => $fileData['mime_type'],
                    'size' => $fileData['size'],
                ]);
            }
        });
    }

    public function removeFile(int $fileId): bool
    {
        return DB::transaction(function () use ($fileId) {
            $file = \App\Models\HomeworkFile::findOrFail($fileId);
            // Storage logic would delete from disk here
            // \Illuminate\Support\Facades\Storage::disk($file->disk)->delete($file->path);
            return $file->delete();
        });
    }

    public function assignClassroom(Homework $homework, int $classroomId): Homework
    {
        return DB::transaction(function () use ($homework, $classroomId) {
            $homework->update(['classroom_id' => $classroomId]);
            return $homework;
        });
    }
}
