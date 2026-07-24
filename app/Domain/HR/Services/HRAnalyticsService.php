<?php

namespace App\Domain\HR\Services;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\EmployeeAttendance;
use App\Models\Expense;
use App\Models\Advance;
use App\Models\Payroll;
use App\Models\PerformanceReview;
use App\Models\Department;

class HRAnalyticsService
{
    public function getDashboardStats()
    {
        $today = now()->toDateString();

        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('employment_status', 'Active')->count();
        $checkedInToday = EmployeeAttendance::where('date', $today)->count();

        // On leave today (Approved leaves that cover today)
        $onLeaveToday = LeaveRequest::where('status', 'Approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->count();

        $monthlySalarySum = Employee::where('employment_status', 'Active')->sum('salary');

        $pendingLeaves = LeaveRequest::where('status', 'Pending')->count();
        $pendingExpenses = Expense::where('status', 'Pending')->count();
        $pendingAdvances = Advance::where('status', 'Pending')->count();

        // Performance average
        $performanceAvg = PerformanceReview::avg('score') ?? 0.0;

        // Department distribution
        $departments = Department::withCount('employees')->get();
        $deptDistribution = [];
        foreach ($departments as $dept) {
            $deptDistribution[] = [
                'name' => $dept->name,
                'count' => $dept->employees_count
            ];
        }

        // Salary history chart values (sum of payrolls per month for the last 6 months)
        $salaryChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;

            $totalNet = Payroll::where('month', $month)->where('year', $year)->sum('net_salary');
            $totalGross = Payroll::where('month', $month)->where('year', $year)->sum('gross_salary');

            $salaryChart[] = [
                'label' => $date->format('M Y'),
                'net' => (float)$totalNet,
                'gross' => (float)$totalGross
            ];
        }

        return [
            'total_employees' => $totalEmployees,
            'active_employees' => $activeEmployees,
            'checked_in_today' => $checkedInToday,
            'on_leave_today' => $onLeaveToday,
            'monthly_salary_sum' => $monthlySalarySum,
            'pending_leaves' => $pendingLeaves,
            'pending_expenses' => $pendingExpenses,
            'pending_advances' => $pendingAdvances,
            'performance_avg' => round($performanceAvg, 2),
            'department_distribution' => $deptDistribution,
            'salary_chart' => $salaryChart,
        ];
    }

    public function getAnalyticsReport()
    {
        $stats = $this->getDashboardStats();

        // Absences calculation
        $totalWorkingDays = 22; // assume standard 22 days per month per active employee
        $expectedPresences = $stats['active_employees'] * $totalWorkingDays;
        $actualPresences = EmployeeAttendance::where('date', '>=', now()->startOfMonth()->toDateString())->count();
        $absencesRate = $expectedPresences > 0 ? round((($expectedPresences - $actualPresences) / $expectedPresences) * 100, 2) : 0;

        // Worked and Overtime sums (Current Month)
        $startOfMonth = now()->startOfMonth()->toDateString();
        $totalWorkedMinutes = EmployeeAttendance::where('date', '>=', $startOfMonth)->sum('worked_minutes');
        $totalOvertimeMinutes = EmployeeAttendance::where('date', '>=', $startOfMonth)->sum('overtime_minutes');

        // Leave ratios
        $totalLeaveDays = LeaveRequest::where('status', 'Approved')
            ->where('start_date', '>=', $startOfMonth)
            ->sum('days');

        // Expenses total
        $totalExpensesCollected = Expense::where('status', 'Approved')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('amount');

        return array_merge($stats, [
            'absences_rate' => max(0, $absencesRate),
            'total_worked_hours' => round($totalWorkedMinutes / 60, 1),
            'total_overtime_hours' => round($totalOvertimeMinutes / 60, 1),
            'total_leave_days' => $totalLeaveDays,
            'total_expenses_collected' => $totalExpensesCollected,
        ]);
    }
}
