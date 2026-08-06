<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\Classroom;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function dashboard()
    {
        $branchId = session('active_branch_id', auth()->user()->branch_id ?? null);
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $lastMonth = $now->copy()->subMonth();

        // Core stats with safe fallbacks
        try {
            $totalStudents = Student::when($branchId, fn($q) => $q->where('branch_id', $branchId))->count();
        } catch (\Throwable $e) {
            $totalStudents = 0;
        }

        try {
            $totalTeachers = User::role('Teacher')->when($branchId, fn($q) => $q->where('branch_id', $branchId))->count();
        } catch (\Throwable $e) {
            $totalTeachers = 0;
        }

        try {
            $totalClassrooms = Classroom::when($branchId, fn($q) => $q->where('branch_id', $branchId))->count();
        } catch (\Throwable $e) {
            $totalClassrooms = 0;
        }

        // Monthly revenue (safe — might not have invoices table)
        try {
            $monthlyRevenue = DB::table('invoices')
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->where('status', 'paid')
                ->whereBetween('paid_at', [$startOfMonth, $now])
                ->sum('total_amount');
        } catch (\Throwable $e) {
            $monthlyRevenue = 0;
        }

        // Recent students
        try {
            $recentStudents = Student::with('classroom')
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->latest()
                ->take(5)
                ->get();
        } catch (\Throwable $e) {
            $recentStudents = collect();
        }

        // Recent activity logs
        try {
            $recentActivities = DB::table('activity_log')
                ->when($branchId, fn($q) => $q->where('properties->branch_id', $branchId))
                ->latest()
                ->take(5)
                ->get();
        } catch (\Throwable $e) {
            $recentActivities = collect();
        }

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalTeachers',
            'totalClassrooms',
            'monthlyRevenue',
            'recentStudents',
            'recentActivities',
        ));
    }
}
