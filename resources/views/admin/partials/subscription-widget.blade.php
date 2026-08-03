@php
    use App\Support\SaaS;
    $subscription = SaaS::currentSubscription();
    $plan = $subscription?->plan;
    $limits = [
        'users' => ['used' => \App\Models\User::withoutGlobalScopes()->where('branch_id', SaaS::currentBranch()?->id)->count(), 'max' => SaaS::limit('users')],
        'students' => ['used' => \App\Models\Student::withoutGlobalScopes()->where('branch_id', SaaS::currentBranch()?->id)->count(), 'max' => SaaS::limit('students')],
        'teachers' => ['used' => \App\Models\Teacher::withoutGlobalScopes()->where('branch_id', SaaS::currentBranch()?->id)->count(), 'max' => SaaS::limit('teachers')],
    ];
@endphp

@if($subscription && $plan)
<x-card class="bg-gradient-to-br from-indigo-900 to-purple-900 text-white overflow-hidden relative">
    <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white opacity-5 blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-48 h-48 rounded-full bg-indigo-500 opacity-10 blur-2xl pointer-events-none"></div>
    
    <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <span class="bg-indigo-500/30 text-indigo-200 text-xs font-bold px-3 py-1 rounded-full border border-indigo-400/30">
                    {{ ucfirst($subscription->status) }}
                </span>
                @if($subscription->isTrialing())
                    <span class="bg-amber-500/30 text-amber-200 text-xs font-bold px-3 py-1 rounded-full border border-amber-400/30">
                        Deneme Sürümü
                    </span>
                @endif
            </div>
            <h2 class="text-2xl font-black mb-1">{{ $plan->name }} Planı</h2>
            <p class="text-indigo-200 text-sm">
                Bitiş: <span class="font-semibold text-white">{{ $subscription->expires_at?->format('d M Y') ?? $subscription->ends_at?->format('d M Y') ?? 'Süresiz' }}</span>
                @if($subscription->trial_ends_at && $subscription->isTrialing())
                    (Deneme Bitiş: <span class="font-semibold text-white">{{ $subscription->trial_ends_at->format('d M Y') }}</span>)
                @endif
            </p>
        </div>
        
        <div class="grid grid-cols-3 gap-4 w-full md:w-auto">
            @foreach($limits as $key => $data)
            <div class="bg-white/10 p-3 rounded-xl backdrop-blur-sm border border-white/10 text-center min-w-[90px]">
                <span class="text-[10px] uppercase font-bold text-indigo-200 block mb-1">
                    {{ $key === 'users' ? 'Kullanıcı' : ($key === 'students' ? 'Öğrenci' : 'Öğretmen') }}
                </span>
                <div class="text-lg font-black">
                    {{ $data['used'] }}<span class="text-indigo-300 text-xs">/{{ $data['max'] === PHP_INT_MAX || $data['max'] === null ? '∞' : $data['max'] }}</span>
                </div>
                @if($data['max'] !== null && $data['max'] !== PHP_INT_MAX && $data['max'] > 0)
                    @php $percent = min(100, round(($data['used'] / $data['max']) * 100)); @endphp
                    <div class="w-full bg-white/20 rounded-full h-1 mt-2">
                        <div class="bg-{{ $percent > 90 ? 'red' : ($percent > 75 ? 'amber' : 'green') }}-400 h-1 rounded-full" style="width: {{ $percent }}%"></div>
                    </div>
                @endif
            </div>
            @endforeach
        </div>
        
        <div class="flex-shrink-0">
            <a href="#" class="inline-flex items-center justify-center bg-white text-indigo-900 hover:bg-indigo-50 font-bold py-2.5 px-6 rounded-xl transition-colors shadow-lg">
                Paketi Yükselt
            </a>
        </div>
    </div>
</x-card>
@endif
