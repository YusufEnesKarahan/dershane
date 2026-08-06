@extends('layouts.admin')

@section('title', 'Yeni Fatura Oluştur')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-black text-neutral-900 dark:text-white">Yeni Fatura Oluştur</h1>
            <p class="text-sm text-neutral-500">Canlı öğrenci arama ile fatura ve tahsilat kalemlerini tanımlayın.</p>
        </div>
        <a href="{{ route('admin.invoices.index') }}" class="px-4 py-2 bg-white border border-neutral-200 text-neutral-700 rounded-xl text-sm font-semibold hover:bg-neutral-50 transition-colors">
            Listeye Dön
        </a>
    </div>

    <form action="{{ route('admin.invoices.store') }}" method="POST" class="bg-white dark:bg-neutral-900 p-6 sm:p-8 rounded-2xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-6">
        @csrf

        <!-- 1. Öğrenci Arama (Ajax Live Search) -->
        <div class="p-6 bg-neutral-50 dark:bg-neutral-800/40 rounded-2xl border border-neutral-200 dark:border-neutral-700 space-y-4">
            <h3 class="text-sm font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-search text-emerald-600"></i> 1. Öğrenci Seçimi (Canlı Arama - Min 3 Karakter)
            </h3>

            <div class="relative">
                <input type="text" id="student_search_input" placeholder="Öğrenci No, Ad, Soyad, Telefon veya TC No ile arayın (Örn: 12345 veya Ahmet)..." class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500" autocomplete="off">
                <div id="student_results_dropdown" class="absolute left-0 right-0 top-full mt-2 bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-xl z-30 hidden max-h-60 overflow-y-auto divide-y divide-neutral-100 dark:divide-neutral-800"></div>
            </div>

            <!-- Selected Student Card -->
            <input type="hidden" name="student_id" id="selected_student_id" required>
            <input type="hidden" name="guardian_id" id="selected_guardian_id">
            <input type="hidden" name="branch_id" id="selected_branch_id">

            <div id="selected_student_card" class="hidden p-4 bg-emerald-50 dark:bg-emerald-950/30 rounded-xl border border-emerald-200 dark:border-emerald-800 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white font-black flex items-center justify-center text-lg">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div>
                        <div class="text-base font-black text-emerald-900 dark:text-emerald-200" id="card_full_name"></div>
                        <div class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 mt-0.5" id="card_details"></div>
                    </div>
                </div>
                <button type="button" onclick="clearSelectedStudent()" class="px-3 py-1.5 bg-rose-100 text-rose-700 hover:bg-rose-200 rounded-lg text-xs font-bold transition-colors">
                    Değiştir
                </button>
            </div>
            @error('student_id') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror
        </div>

        <!-- 2. Fatura Genel Bilgileri -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Veli Adı (Otomatik Gelir / İsteğe Bağlı)</label>
                <input type="text" id="guardian_name_display" readonly placeholder="Öğrenci seçildiğinde otomatik gelir" class="w-full bg-neutral-100 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm text-neutral-600 dark:text-neutral-400">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Düzenleme Tarihi</label>
                <input type="date" name="issue_date" value="{{ old('issue_date', now()->format('Y-m-d')) }}" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Son Ödeme Vade Tarihi *</label>
                <input type="date" name="due_date" required value="{{ old('due_date', now()->format('Y-m-d')) }}" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500">
                @error('due_date') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- 3. Fatura Kalemleri (Dinamik) -->
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="text-sm font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-wider">Fatura Kalemleri</h3>
                <button type="button" onclick="addInvoiceItem()" class="px-3 py-1.5 bg-emerald-100 text-emerald-700 hover:bg-emerald-200 dark:bg-emerald-900/50 dark:text-emerald-300 rounded-xl text-xs font-bold transition-colors flex items-center gap-1">
                    <i class="fas fa-plus"></i> Kalem Ekle
                </button>
            </div>

            <div class="overflow-x-auto border border-neutral-200 dark:border-neutral-700 rounded-xl">
                <table class="w-full text-left border-collapse" id="items_table">
                    <thead>
                        <tr class="bg-neutral-50 dark:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700 text-xs font-bold uppercase text-neutral-500">
                            <th class="px-4 py-3 w-48">Kalem Türü</th>
                            <th class="px-4 py-3">Açıklama</th>
                            <th class="px-4 py-3 w-28">Adet</th>
                            <th class="px-4 py-3 w-36">Birim Fiyat (TL)</th>
                            <th class="px-4 py-3 w-36 text-right">Toplam (TL)</th>
                            <th class="px-4 py-3 w-16 text-center">İşlem</th>
                        </tr>
                    </thead>
                    <tbody id="items_tbody" class="divide-y divide-neutral-100 dark:divide-neutral-800 text-sm">
                        <!-- Default First Item -->
                        <tr class="item-row">
                            <td class="px-4 py-3">
                                <select name="items[0][item_type]" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-lg p-2 text-xs font-semibold">
                                    <option value="Kayıt Ücreti">Kayıt Ücreti</option>
                                    <option value="Kitap">Kitap</option>
                                    <option value="Deneme">Deneme</option>
                                    <option value="Servis">Servis</option>
                                    <option value="Yemek">Yemek</option>
                                    <option value="Diğer">Diğer</option>
                                </select>
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" name="items[0][description]" required value="Eğitim Öğretim Ücreti" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-lg p-2 text-xs">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" name="items[0][quantity]" value="1" min="1" oninput="calculateTotals()" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-lg p-2 text-xs item-qty">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" step="0.01" name="items[0][unit_price]" value="0" min="0" oninput="calculateTotals()" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-lg p-2 text-xs item-price">
                            </td>
                            <td class="px-4 py-3 text-right font-black text-neutral-900 dark:text-white item-line-total">
                                0.00 TL
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button type="button" onclick="removeRow(this)" class="text-neutral-400 hover:text-rose-600 p-1">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-neutral-900 text-white rounded-xl flex justify-between items-center">
                <span class="text-sm font-bold uppercase tracking-wider text-neutral-400">Fatura Genel Toplamı:</span>
                <span class="text-2xl font-black text-emerald-400" id="grand_total_display">0.00 TL</span>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-neutral-600 dark:text-neutral-300 mb-2">Açıklama / Notlar (Opsiyonel)</label>
            <textarea name="description" rows="3" placeholder="Fatura ile ilgili ek açıklamalar..." class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-xl px-4 py-2.5 text-sm">{{ old('description') }}</textarea>
        </div>

        <div class="flex justify-end pt-4 border-t border-neutral-100 dark:border-neutral-800">
            <button type="submit" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition-colors shadow-sm flex items-center gap-2">
                <i class="fas fa-check-circle"></i> Faturayı Oluştur ve Kaydet
            </button>
        </div>
    </form>
</div>

<script>
    let itemIndex = 1;

    document.getElementById('student_search_input').addEventListener('input', function() {
        let q = this.value.trim();
        let dropdown = document.getElementById('student_results_dropdown');

        if (q.length < 3) {
            dropdown.classList.add('hidden');
            return;
        }

        fetch('/admin/invoices/search-students?q=' + encodeURIComponent(q))
            .then(res => res.json())
            .then(data => {
                dropdown.innerHTML = '';
                if (data.length === 0) {
                    dropdown.innerHTML = '<div class="p-4 text-xs text-neutral-500 italic text-center">Öğrenci bulunamadı.</div>';
                } else {
                    data.forEach(s => {
                        let item = document.createElement('div');
                        item.className = 'p-3 hover:bg-neutral-50 dark:hover:bg-neutral-800 cursor-pointer transition-colors';
                        item.innerHTML = `
                            <div class="font-bold text-sm text-neutral-900 dark:text-white">${s.card_display}</div>
                            <div class="text-xs text-neutral-500 mt-0.5">Veli: ${s.guardian_name}</div>
                        `;
                        item.onclick = function() {
                            selectStudent(s);
                        };
                        dropdown.appendChild(item);
                    });
                }
                dropdown.classList.remove('hidden');
            });
    });

    function selectStudent(s) {
        document.getElementById('selected_student_id').value = s.id;
        document.getElementById('selected_guardian_id').value = s.guardian_id || '';
        document.getElementById('selected_branch_id').value = s.branch_id || '';

        document.getElementById('card_full_name').innerText = s.full_name + ' (' + s.student_number + ')';
        document.getElementById('card_details').innerText = 'Sınıf: ' + s.classroom_name + ' | Şube: ' + s.branch_name;
        document.getElementById('guardian_name_display').value = s.guardian_name;

        document.getElementById('student_search_input').classList.add('hidden');
        document.getElementById('student_results_dropdown').classList.add('hidden');
        document.getElementById('selected_student_card').classList.remove('hidden');
    }

    function clearSelectedStudent() {
        document.getElementById('selected_student_id').value = '';
        document.getElementById('selected_guardian_id').value = '';
        document.getElementById('selected_branch_id').value = '';
        document.getElementById('guardian_name_display').value = '';

        document.getElementById('student_search_input').value = '';
        document.getElementById('student_search_input').classList.remove('hidden');
        document.getElementById('selected_student_card').classList.add('hidden');
    }

    function addInvoiceItem() {
        let tbody = document.getElementById('items_tbody');
        let row = document.createElement('tr');
        row.className = 'item-row';
        row.innerHTML = `
            <td class="px-4 py-3">
                <select name="items[${itemIndex}][item_type]" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-lg p-2 text-xs font-semibold">
                    <option value="Kayıt Ücreti">Kayıt Ücreti</option>
                    <option value="Kitap">Kitap</option>
                    <option value="Deneme">Deneme</option>
                    <option value="Servis">Servis</option>
                    <option value="Yemek">Yemek</option>
                    <option value="Diğer">Diğer</option>
                </select>
            </td>
            <td class="px-4 py-3">
                <input type="text" name="items[${itemIndex}][description]" required placeholder="Kalem açıklaması" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-lg p-2 text-xs">
            </td>
            <td class="px-4 py-3">
                <input type="number" name="items[${itemIndex}][quantity]" value="1" min="1" oninput="calculateTotals()" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-lg p-2 text-xs item-qty">
            </td>
            <td class="px-4 py-3">
                <input type="number" step="0.01" name="items[${itemIndex}][unit_price]" value="0" min="0" oninput="calculateTotals()" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-lg p-2 text-xs item-price">
            </td>
            <td class="px-4 py-3 text-right font-black text-neutral-900 dark:text-white item-line-total">
                0.00 TL
            </td>
            <td class="px-4 py-3 text-center">
                <button type="button" onclick="removeRow(this)" class="text-neutral-400 hover:text-rose-600 p-1">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
        itemIndex++;
        calculateTotals();
    }

    function removeRow(btn) {
        let rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) {
            btn.closest('tr').remove();
            calculateTotals();
        }
    }

    function calculateTotals() {
        let rows = document.querySelectorAll('.item-row');
        let grandTotal = 0;

        rows.forEach(r => {
            let qty = parseFloat(r.querySelector('.item-qty').value) || 0;
            let price = parseFloat(r.querySelector('.item-price').value) || 0;
            let lineTotal = qty * price;
            r.querySelector('.item-line-total').innerText = lineTotal.toFixed(2) + ' TL';
            grandTotal += lineTotal;
        });

        document.getElementById('grand_total_display').innerText = grandTotal.toFixed(2) + ' TL';
    }
</script>
@endsection
