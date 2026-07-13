@extends('layouts.templates.landing')

@php
    $seo = [
        'title' => 'Online Ön Kayıt | Erken Kayıt Avantajları',
        'description' => 'Eğitim programlarımıza online ön kayıt yaptırarak erken kayıt indirimlerinden faydalanın.',
        'keywords' => 'ön kayıt, erken kayıt, kayıt formu, eğitim, dershane kayıt'
    ];
@endphp

@section('template-content')
    <!-- PAGE HEADER -->
    <x-section bg="dark" py="20" class="relative overflow-hidden bg-gradient-to-br from-neutral-950 via-neutral-900 to-primary/20">
        <x-container class="relative z-10 text-center">
            <h1 class="text-4xl sm:text-5xl font-display font-bold text-white tracking-tight mb-4">Online Ön Kayıt</h1>
            <p class="text-neutral-400 max-w-2xl mx-auto">Sınırlı kontenjanlı butik sınıflarımızda yerinizi ayırtmak için formu doldurun, eğitim danışmanlarımız size ulaşsın.</p>
        </x-container>
    </x-section>

    <!-- FORM SECTION -->
    <x-section bg="gray" py="16">
        <x-container>
            <div class="max-w-2xl mx-auto bg-white p-8 sm:p-12 rounded-premium-2xl border border-neutral-100 shadow-premium-sm">
                
                <div class="flex items-center space-x-4 mb-8 p-4 bg-primary/5 rounded-xl border border-primary/10">
                    <svg class="h-8 w-8 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <div>
                        <h4 class="font-bold text-neutral">Erken Kayıt Avantajı</h4>
                        <p class="text-sm text-neutral/70">Ön kayıt formunu dolduran ilk 50 öğrenciye özel %15 erken kayıt indirimi uygulanacaktır.</p>
                    </div>
                </div>

                <form action="#" method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="student_name" class="block text-sm font-medium text-neutral mb-2">Öğrenci Adı Soyadı</label>
                            <input type="text" id="student_name" name="student_name" class="w-full px-4 py-3 rounded-xl border border-neutral-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" required>
                        </div>
                        <div>
                            <label for="student_phone" class="block text-sm font-medium text-neutral mb-2">Öğrenci Telefon</label>
                            <input type="tel" id="student_phone" name="student_phone" class="w-full px-4 py-3 rounded-xl border border-neutral-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="parent_name" class="block text-sm font-medium text-neutral mb-2">Veli Adı Soyadı</label>
                            <input type="text" id="parent_name" name="parent_name" class="w-full px-4 py-3 rounded-xl border border-neutral-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" required>
                        </div>
                        <div>
                            <label for="parent_phone" class="block text-sm font-medium text-neutral mb-2">Veli Telefon</label>
                            <input type="tel" id="parent_phone" name="parent_phone" class="w-full px-4 py-3 rounded-xl border border-neutral-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="grade" class="block text-sm font-medium text-neutral mb-2">Sınıf</label>
                            <select id="grade" name="grade" class="w-full px-4 py-3 rounded-xl border border-neutral-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white" required>
                                <option value="">Seçiniz</option>
                                <option value="8">8. Sınıf (LGS)</option>
                                <option value="9">9. Sınıf</option>
                                <option value="10">10. Sınıf</option>
                                <option value="11">11. Sınıf</option>
                                <option value="12">12. Sınıf (YKS)</option>
                                <option value="mezun">Mezun (YKS)</option>
                            </select>
                        </div>
                        <div>
                            <label for="program" class="block text-sm font-medium text-neutral mb-2">İlgilendiğiniz Program</label>
                            <select id="program" name="program" class="w-full px-4 py-3 rounded-xl border border-neutral-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white" required>
                                <option value="">Seçiniz</option>
                                <option value="vip">VIP Butik (Maks. 12 Kişi)</option>
                                <option value="standart">Standart (Maks. 16 Kişi)</option>
                                <option value="ozel">Birebir Özel Ders</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-4">
                        <label class="flex items-start gap-3">
                            <input type="checkbox" name="kvkk" class="mt-1 shrink-0 rounded border-neutral-300 text-primary focus:ring-primary h-4 w-4" required>
                            <span class="text-sm text-neutral/70">
                                <a href="{{ route('legal.kvkk') }}" class="text-primary hover:underline" target="_blank">KVKK Aydınlatma Metni</a>'ni okudum ve kişisel verilerimin işlenmesini kabul ediyorum.
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="w-full inline-flex justify-center items-center px-8 py-4 border border-transparent text-base font-medium rounded-xl shadow-premium-sm text-white bg-primary hover:bg-primary-dark transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary mt-6">
                        Ön Kayıt Başvurusunu Tamamla
                    </button>
                </form>
            </div>
        </x-container>
    </x-section>
@endsection
