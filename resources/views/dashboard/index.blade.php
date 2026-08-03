@extends('layouts.admin')
@section('title', 'Kurum Özeti')
@section('content')
<div class="space-y-6">
    <!-- Header Area -->
    <div class="bg-gradient-to-r from-indigo-900 to-slate-900 p-8 rounded-3xl text-white shadow-premium flex flex-col md:flex-row md:items-center md:justify-between gap-6 border border-indigo-950">
        <div>
            <span class="px-2.5 py-1 text-[10px] uppercase font-bold tracking-widest bg-indigo-500/20 text-indigo-300 rounded-full border border-indigo-500/30">Kurum Yönetim Paneli</span>
            <h1 class="text-2xl font-black mt-2">Dershane İşletme Özeti</h1>
            <p class="text-xs text-slate-300 mt-1">Öğrenci, öğretmen, ders durumu ve güncel gelişmeleri buradan takip edebilirsiniz.</p>
        </div>
        
        <!-- Quick Actions -->
        <div class="flex flex-wrap items-center gap-3">
            @can('students.create')
            <a href="{{ route('admin.students.create') ?? '#' }}" class="bg-white/10 hover:bg-white/20 border border-white/20 transition-all text-white px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 shadow-sm backdrop-blur-sm">
                <i class="fas fa-user-plus"></i> Öğrenci Ekle
            </a>
            @endcan
            @can('teachers.create')
            <a href="{{ route('admin.teachers.create') ?? '#' }}" class="bg-white/10 hover:bg-white/20 border border-white/20 transition-all text-white px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 shadow-sm backdrop-blur-sm">
                <i class="fas fa-chalkboard-teacher"></i> Eğitmen Ekle
            </a>
            @endcan
            @can('classes.create')
            <a href="{{ route('admin.classrooms.create') ?? '#' }}" class="bg-indigo-500 hover:bg-indigo-400 border border-indigo-400 transition-all text-white px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 shadow-md">
                <i class="fas fa-users-class"></i> Sınıf Oluştur
            </a>
            @endcan
        </div>
    </div>

    <!-- Subscription Widget -->
    <div class="mb-6">
        @include('admin.partials.subscription-widget')
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Students -->
        <div class="bg-white dark:bg-neutral-900 p-5 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-neutral-400 uppercase">Toplam Öğrenci</span>
                <div class="p-1.5 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded-lg">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-neutral-900 dark:text-white">{{ $statistics['students'] ?? 0 }}</div>
            <p class="text-[10px] text-emerald-500 font-semibold mt-1"><i class="fas fa-circle text-[8px] mr-1"></i>Aktif Öğrenciler</p>
        </div>

        <!-- Teachers -->
        <div class="bg-white dark:bg-neutral-900 p-5 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-neutral-400 uppercase">Eğitmen Kadrosu</span>
                <div class="p-1.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-lg">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-neutral-900 dark:text-white">{{ $statistics['teachers'] ?? 0 }}</div>
            <p class="text-[10px] text-emerald-500 font-semibold mt-1"><i class="fas fa-circle text-[8px] mr-1"></i>Aktif Eğitmenler</p>
        </div>

        <!-- Classrooms -->
        <div class="bg-white dark:bg-neutral-900 p-5 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-neutral-400 uppercase">Aktif Sınıf</span>
                <div class="p-1.5 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-lg">
                    <i class="fas fa-users-class"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-neutral-900 dark:text-white">{{ $statistics['classrooms'] ?? 0 }}</div>
            <p class="text-[10px] text-amber-500 font-semibold mt-1"><i class="fas fa-circle text-[8px] mr-1"></i>Eğitim Gören Sınıflar</p>
        </div>

        <!-- Today's Attendance -->
        <div class="bg-white dark:bg-neutral-900 p-5 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-neutral-400 uppercase">Bugünkü Yoklama</span>
                <div class="p-1.5 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 rounded-lg">
                    <i class="fas fa-clipboard-check"></i>
                </div>
            </div>
            <div class="flex items-end gap-2">
                <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $statistics['attendance']['present'] ?? 0 }}</div>
                <div class="text-sm font-black text-rose-500 dark:text-rose-400 pb-0.5">/ {{ $statistics['attendance']['absent'] ?? 0 }}</div>
            </div>
            <p class="text-[10px] text-neutral-500 font-semibold mt-1">Gelen / Gelmeyen (Bugün)</p>
        </div>
        
        <!-- Upcoming Exams -->
        <div class="bg-white dark:bg-neutral-900 p-5 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-neutral-400 uppercase">Yaklaşan Sınavlar</span>
                <div class="p-1.5 bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 rounded-lg">
                    <i class="fas fa-file-signature"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-neutral-900 dark:text-white">{{ $statistics['upcoming_exams'] ?? 0 }}</div>
            <p class="text-[10px] text-purple-500 font-semibold mt-1"><i class="fas fa-circle text-[8px] mr-1"></i>Planlanmış Sınavlar</p>
        </div>
    </div>

    <!-- Charts and Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Charts Area -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Growth Chart -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-neutral-900 dark:text-white">Aylık Öğrenci Kayıt Analizi</h3>
                    <span class="text-xs text-neutral-400 font-medium">Son 6 Ay</span>
                </div>
                <div class="relative h-[250px] w-full">
                    <canvas id="growthChart"></canvas>
                </div>
            </div>
            
            <!-- Attendance Chart -->
            <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-neutral-900 dark:text-white">Yoklama İstatistiği</h3>
                    <span class="text-xs text-neutral-400 font-medium">Son 30 Gün</span>
                </div>
                <div class="relative h-[220px] w-full flex justify-center">
                    <canvas id="attendanceChart"></canvas>
                </div>
                <div class="mt-4 flex justify-center gap-6">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                        <span class="text-xs font-semibold text-neutral-600 dark:text-neutral-300">Katılım ({{ $attendance_summary['present'] ?? 0 }})</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-rose-400"></span>
                        <span class="text-xs font-semibold text-neutral-600 dark:text-neutral-300">Devamsız ({{ $attendance_summary['absent'] ?? 0 }})</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-history text-indigo-500"></i> Son Aktiviteler
                </h3>
            </div>
            
            @if(isset($recent_activities) && count($recent_activities) > 0)
                <div class="relative border-l-2 border-neutral-100 dark:border-neutral-800 ml-3 space-y-6">
                    @foreach($recent_activities as $activity)
                    <div class="relative pl-6">
                        <span class="absolute -left-[11px] top-1 bg-white dark:bg-neutral-900 p-0.5 rounded-full">
                            <span class="flex items-center justify-center w-5 h-5 rounded-full bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 shadow-sm">
                                <i class="fas fa-{{ $activity['icon'] }} text-[10px] {{ $activity['color'] }}"></i>
                            </span>
                        </span>
                        <div>
                            <p class="text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ $activity['message'] }}</p>
                            <p class="text-[11px] text-neutral-400 mt-0.5 flex items-center gap-1">
                                <i class="far fa-clock"></i> {{ $activity['time'] }} &bull; {{ $activity['user'] }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center text-center py-10 opacity-60">
                    <div class="w-16 h-16 bg-neutral-50 dark:bg-neutral-800 rounded-full flex items-center justify-center mb-3 text-neutral-400">
                        <i class="fas fa-ghost text-2xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-neutral-500">Henüz aktivite bulunmuyor.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#9ca3af' : '#6b7280';
        const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
        
        // Growth Chart
        const growthCtx = document.getElementById('growthChart').getContext('2d');
        const rawGrowth = @json($student_growth ?? []);
        
        new Chart(growthCtx, {
            type: 'bar',
            data: {
                labels: rawGrowth.map(item => item.month),
                datasets: [{
                    label: 'Yeni Kayıt',
                    data: rawGrowth.map(item => item.count),
                    backgroundColor: 'rgba(99, 102, 241, 0.8)',
                    borderRadius: 6,
                    barPercentage: 0.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: textColor, precision: 0 },
                        grid: { color: gridColor }
                    },
                    x: {
                        ticks: { color: textColor },
                        grid: { display: false }
                    }
                }
            }
        });

        // Attendance Chart
        const attCtx = document.getElementById('attendanceChart').getContext('2d');
        const attData = @json($attendance_summary ?? ['present' => 0, 'absent' => 0]);
        
        new Chart(attCtx, {
            type: 'doughnut',
            data: {
                labels: ['Katılım', 'Devamsız'],
                datasets: [{
                    data: [attData.present, attData.absent],
                    backgroundColor: ['#34d399', '#fb7185'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>
@endsection
