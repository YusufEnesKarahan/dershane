<?php

namespace App\DTOs\Parent;

use App\Models\Student;
use Illuminate\Support\Collection;

class ParentDashboardDTO
{
    public function __construct(
        public Student $student,
        public Collection $attendance,
        public Collection $examResults,
        public Collection $homeworks,
        public Collection $invoices,
        public Collection $announcements
    ) {}
}
