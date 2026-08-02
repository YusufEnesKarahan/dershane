@extends('layouts.admin')
@section('title', 'Personel Yönetimi')
@section('content')
    <x-admin.crud.index-layout title="Personel Ana Kayıtları" description="Kurum personel listesini, özlük detaylarını, maaş ve departman bilgilerini yönetin.">
        <x-slot name="actions">
            <button onclick="toggleModal('employee-modal')" class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-500 text-white text-xs font-bold rounded-xl transition-colors shadow-lg shadow-violet-900/20 border border-violet-500/50">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Yeni Personel Ekle
            </button>
        </x-slot>

        <!-- Personel Tablosu -->
        <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm overflow-hidden flex flex-col h-full">
            <div class="p-0 flex-1">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
                        <thead class="bg-neutral-50/80 dark:bg-neutral-900/80 backdrop-blur-sm">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider bg-neutral-50/50 dark:bg-neutral-800/30">Personel No</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider bg-neutral-50/50 dark:bg-neutral-800/30">Ad Soyad</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider bg-neutral-50/50 dark:bg-neutral-800/30">Departman / Pozisyon</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider bg-neutral-50/50 dark:bg-neutral-800/30">Maaş / Sözleşme</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider bg-neutral-50/50 dark:bg-neutral-800/30">Durum</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider bg-neutral-50/50 dark:bg-neutral-800/30 w-32">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800/50 bg-white dark:bg-neutral-900">
                            @forelse($employees as $emp)
                                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40 transition-colors border-b border-neutral-100 dark:border-neutral-800/50 last:border-0 group">
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-neutral-100 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 text-xs font-bold text-neutral-700 dark:text-neutral-300 font-mono">
                                            {{ $emp->employee_no }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-neutral-900 dark:text-white">{{ $emp->first_name }} {{ $emp->last_name }}</div>
                                        <div class="text-[11px] font-medium text-neutral-500 dark:text-neutral-400 mt-1 flex items-center gap-2">
                                            <span class="flex items-center gap-1"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg> {{ $emp->email }}</span>
                                            <span>&bull;</span>
                                            <span class="flex items-center gap-1"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg> {{ $emp->phone }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-bold text-neutral-900 dark:text-white">{{ $emp->department->name ?? 'Yok' }}</span>
                                        <div class="text-[11px] text-neutral-500 dark:text-neutral-400 mt-1">{{ $emp->position->name ?? 'Yok' }} ({{ $emp->position->level ?? '-' }})</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-bold text-neutral-900 dark:text-white font-mono">₺{{ number_format($emp->salary, 2) }}</span>
                                        <div class="text-[11px] text-neutral-500 dark:text-neutral-400 mt-1">{{ $emp->contract_type }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($emp->employment_status === 'Active')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                                Aktif Çalışan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-400">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                                                Ayrıldı / Fesih
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button onclick="editEmployee({{ json_encode($emp) }})" class="p-2 text-violet-600 hover:bg-violet-50 dark:hover:bg-violet-500/10 rounded-lg transition-colors" title="Düzenle">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </button>
                                            @if($emp->employment_status === 'Active')
                                                <form method="POST" action="{{ route('admin.employees.destroy', $emp->id) }}" class="inline-block" onsubmit="return confirm('Sözleşmeyi feshetmek istediğinize emin misiniz?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors" title="Sözleşmeyi Feshet">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <x-admin.empty-state
                                            icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                            title="Personel Bulunamadı"
                                            description="Sistemde henüz kayıtlı bir personel bulunmuyor. Yeni bir personel ekleyerek başlayabilirsiniz."
                                        />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </x-admin.crud.index-layout>

        <!-- Yeni Personel Modal -->
        <div id="employee-modal" class="fixed inset-0 z-50 hidden bg-neutral-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 max-w-2xl w-full shadow-premium space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center">
                    <h3 id="modal-title" class="text-sm font-bold text-neutral-900 dark:text-white">Yeni Personel Kaydı</h3>
                    <button onclick="toggleModal('employee-modal')" class="text-neutral-400 hover:text-neutral-600">&times;</button>
                </div>
                
                <form id="employee-form" method="POST" action="{{ route('admin.employees.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    @csrf
                    <input type="hidden" id="form-method" name="_method" value="POST">

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Ad</label>
                        <input type="text" name="first_name" id="emp-first_name" required class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Soyad</label>
                        <input type="text" name="last_name" id="emp-last_name" required class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">TC Kimlik No</label>
                        <input type="text" name="tc_no" id="emp-tc_no" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">E-Posta</label>
                        <input type="email" name="email" id="emp-email" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Telefon</label>
                        <input type="text" name="phone" id="emp-phone" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Cinsiyet</label>
                        <select name="gender" id="emp-gender" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                            <option value="Erkek">Erkek</option>
                            <option value="Kadın">Kadın</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Departman</label>
                        <select name="department_id" id="emp-department_id" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Pozisyon</label>
                        <select name="position_id" id="emp-position_id" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                            @foreach($positions as $pos)
                                <option value="{{ $pos->id }}">{{ $pos->name }} ({{ $pos->level }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Kullanıcı Hesabı Bağla</label>
                        <select name="user_id" id="emp-user_id" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                            <option value="">Bağlama</option>
                            @foreach($users as $usr)
                                <option value="{{ $usr->id }}">{{ $usr->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Maaş (Net)</label>
                        <input type="number" name="salary" id="emp-salary" required step="0.01" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Sözleşme Türü</label>
                        <select name="contract_type" id="emp-contract_type" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                            <option value="Full-time">Tam Zamanlı (Full-time)</option>
                            <option value="Part-time">Kısmi Zamanlı (Part-time)</option>
                            <option value="Contract">Sözleşmeli (Contract)</option>
                            <option value="Internship">Stajyer (Internship)</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400 font-mono">IBAN</label>
                        <input type="text" name="iban" id="emp-iban" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">İşe Giriş Tarihi</label>
                        <input type="date" name="hire_date" id="emp-hire_date" required class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Doğum Tarihi</label>
                        <input type="date" name="birth_date" id="emp-birth_date" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="md:col-span-2 space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Adres</label>
                        <textarea name="address" id="emp-address" rows="2" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl"></textarea>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Acil Durum Yakını</label>
                        <input type="text" name="emergency_contact" id="emp-emergency_contact" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-neutral-600 dark:text-neutral-400">Acil Durum Telefon</label>
                        <input type="text" name="emergency_phone" id="emp-emergency_phone" class="w-full p-2.5 bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl">
                    </div>

                    <div class="md:col-span-2 flex justify-end gap-2 pt-4">
                        <button type="button" onclick="toggleModal('employee-modal')" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 font-bold rounded-xl transition">Vazgeç</button>
                        <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-xl transition">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function toggleModal(id) {
            const el = document.getElementById(id);
            el.classList.toggle('hidden');
        }

        function editEmployee(emp) {
            document.getElementById('modal-title').innerText = 'Personel Bilgilerini Düzenle';
            document.getElementById('employee-form').action = `/admin/employees/${emp.id}`;
            document.getElementById('form-method').value = 'PUT';

            document.getElementById('emp-first_name').value = emp.first_name;
            document.getElementById('emp-last_name').value = emp.last_name;
            document.getElementById('emp-tc_no').value = emp.tc_no || '';
            document.getElementById('emp-email').value = emp.email || '';
            document.getElementById('emp-phone').value = emp.phone || '';
            document.getElementById('emp-gender').value = emp.gender || 'Erkek';
            document.getElementById('emp-department_id').value = emp.department_id || '';
            document.getElementById('emp-position_id').value = emp.position_id || '';
            document.getElementById('emp-user_id').value = emp.user_id || '';
            document.getElementById('emp-salary').value = emp.salary;
            document.getElementById('emp-contract_type').value = emp.contract_type || 'Full-time';
            document.getElementById('emp-iban').value = emp.iban || '';
            document.getElementById('emp-hire_date').value = emp.hire_date || '';
            document.getElementById('emp-birth_date').value = emp.birth_date || '';
            document.getElementById('emp-address').value = emp.address || '';
            document.getElementById('emp-emergency_contact').value = emp.emergency_contact || '';
            document.getElementById('emp-emergency_phone').value = emp.emergency_phone || '';

            toggleModal('employee-modal');
        }
    </script>
@endsection
