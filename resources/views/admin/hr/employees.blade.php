@extends('layouts.admin')
@section('title', 'Personel Yönetimi')
@section('content')
    <div class="space-y-6">
        
        <!-- Header -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm flex justify-between items-center">
            <div>
                <h1 class="text-lg font-bold text-neutral-900 dark:text-white">Personel Ana Kayıtları</h1>
                <p class="text-xs text-neutral-500 mt-1">Kurum personel listesini, özlük detaylarını, maaş ve departman bilgilerini yönetin.</p>
            </div>
            
            <button onclick="toggleModal('employee-modal')" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-xs font-bold text-white rounded-xl transition shadow-lg shadow-violet-950">
                Yeni Personel Ekle
            </button>
        </div>

        <!-- Personel Tablosu -->
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-premium-sm">
            <x-admin.table.layout>
                <x-slot name="head">
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Personel No</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Ad Soyad</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Departman / Pozisyon</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Maaş / Sözleşme</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">Durum</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-neutral-500 uppercase">İşlem</th>
                </x-slot>
                <x-slot name="body">
                    @forelse($employees as $emp)
                        <tr>
                            <td class="px-4 py-3 text-xs font-bold font-mono text-neutral-900 dark:text-white">{{ $emp->employee_no }}</td>
                            <td class="px-4 py-3 text-xs font-bold text-neutral-800 dark:text-neutral-200">
                                {{ $emp->first_name }} {{ $emp->last_name }}
                                <div class="text-[10px] font-normal text-neutral-400 mt-0.5">{{ $emp->email }} | {{ $emp->phone }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $emp->department->name ?? 'Yok' }}</span>
                                <div class="text-[10px] text-neutral-400 mt-0.5">{{ $emp->position->name ?? 'Yok' }} ({{ $emp->position->level ?? '-' }})</div>
                            </td>
                            <td class="px-4 py-3 text-xs font-mono">
                                ₺{{ number_format($emp->salary, 2) }}
                                <div class="text-[10px] font-sans text-neutral-400 mt-0.5">{{ $emp->contract_type }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold {{ $emp->employment_status === 'Active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $emp->employment_status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs space-x-2">
                                <button onclick="editEmployee({{ json_encode($emp) }})" class="text-violet-600 hover:underline font-bold">Düzenle</button>
                                @if($emp->employment_status === 'Active')
                                    <form method="POST" action="{{ route('admin.employees.destroy', $emp->id) }}" class="inline-block" onsubmit="return confirm('Sözleşmeyi feshetmek istediğinize emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline font-bold">Feshet</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-xs text-neutral-400">Kayıtlı personel bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-admin.table.layout>
        </div>

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
