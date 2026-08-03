@extends('layouts.app')
    
@section('title', 'Persyaratan Layanan')

@section('content')
@php
    $allFormulirs = \App\Models\Formulir::where('aktif', 'Y')->get();
    
    $getDownloadLink = function($syarat, $keterangan) use ($allFormulirs) {
        // Normalisasi teks pencarian
        $syaratClean = strtolower($syarat);
        $groupClean = strtolower($keterangan);
        
        // 1. Pemetaan Khusus untuk Formulir F.2-01 berdasarkan kategori layanan
        if (str_contains($syaratClean, 'f.2-01') || str_contains($syaratClean, 'f-2.01') || str_contains($syaratClean, 'f2.01')) {
            if ($groupClean === 'akw' || str_contains($groupClean, 'perkawinan')) {
                return route('formulir.download', '558798102271853.pdf');
            }
            if ($groupClean === 'amt' || str_contains($groupClean, 'kematian')) {
                return route('formulir.download', '794514332173911.pdf');
            }
            if ($groupClean === 'acr' || str_contains($groupClean, 'perceraian')) {
                return route('formulir.download', 'f-201-akta-perceraian_1784778952.pdf');
            }
        }
        
        // 2. Cari pola kode formulir umum seperti F-1.02, F.1-02, F.1.02, dll.
        if (preg_match('/F[-.\s]*\d+[-.\s]*\d+/i', $syarat, $matches)) {
            $code = strtolower(str_replace(['.', '-', ' '], '', $matches[0])); // misal: f102
            foreach ($allFormulirs as $form) {
                $formCode = strtolower(str_replace(['.', '-', ' '], '', $form->jenis_formulir));
                if (str_contains($formCode, $code)) {
                    return route('formulir.download', $form->dokumen);
                }
            }
        }
        
        // 3. Fallback pencarian manual berdasarkan kemiripan teks
        if (str_contains($syaratClean, 'f.1-02') || str_contains($syaratClean, 'f-1.02') || str_contains($syaratClean, 'f.1.02')) {
            $f = $allFormulirs->first(fn($x) => str_contains(strtolower($x->jenis_formulir), 'f1.02') || str_contains(strtolower($x->jenis_formulir), 'f-1.02'));
            if ($f) return route('formulir.download', $f->dokumen);
        }
        if (str_contains($syaratClean, 'f.1-03') || str_contains($syaratClean, 'f-1.03') || str_contains($syaratClean, 'f.1.03')) {
            $f = $allFormulirs->first(fn($x) => str_contains(strtolower($x->jenis_formulir), 'f1.03') || str_contains(strtolower($x->jenis_formulir), 'f-1.03'));
            if ($f) return route('formulir.download', $f->dokumen);
        }
        if (str_contains($syaratClean, 'f.1-06') || str_contains($syaratClean, 'f-1.06') || str_contains($syaratClean, 'f.1.06')) {
            $f = $allFormulirs->first(fn($x) => str_contains(strtolower($x->jenis_formulir), 'f1.06') || str_contains(strtolower($x->jenis_formulir), 'f-1.06'));
            if ($f) return route('formulir.download', $f->dokumen);
        }
        
        return null;
    };
@endphp

<div class="min-h-screen bg-transparent pt-6 pb-20 flex items-start justify-center">
    <div class="w-full max-w-4xl mx-auto p-4">

        <div class="bg-gray-100/10 backdrop-blur-md rounded-2xl shadow-xl border border-white/20 p-5 md:p-8 relative">
            <!-- Tombol Kembali -->
            <a href="{{ url('/') }}" class="absolute top-5 left-5 text-gray-600 hover:text-blue-600 hover:bg-white/20 p-2 rounded-full transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>

            <!-- Logo dan Judul -->
            <div class="flex flex-col items-center mt-6 mb-8">
                <div class="w-20 h-20 md:w-24 md:h-24 mb-4 drop-shadow-md hover:scale-105 transition duration-300">
                    <img src="{{ asset('icon/syarat1.png') }}" alt="Persyaratan" class="w-full h-full object-contain">
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800 tracking-tight">Persyaratan Layanan</h1>
                <p class="text-xs md:text-sm text-gray-600 font-semibold mt-1">Daftar berkas persyaratan administrasi kependudukan Anda</p>
            </div>

            <!-- Alpine.js: Kelola state semua grup dan akordion -->
            <div
                x-data="{
                    openGroup: null,
                    openAccordion: {},
                    toggleGroup(slug) {
                        this.openGroup = this.openGroup === slug ? null : slug;
                    },
                    toggleAccordion(slug) {
                        this.openAccordion[slug] = !this.openAccordion[slug];
                    },
                    isGroupOpen(slug) {
                        return this.openGroup === slug;
                    },
                    isAccordionOpen(slug) {
                        return this.openAccordion[slug] === true;
                    }
                }"
                class="space-y-4"
            >
                @foreach($grouped as $keterangan => $items)
                    @php
                        $groupSlug = Str::slug($keterangan);
                    @endphp

                    <div class="border border-white/30 rounded-2xl overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 bg-white/40 backdrop-blur-md">
                        <!-- Header Grup Utama -->
                        <button
                            class="w-full flex justify-between items-center p-4.5 md:p-5 text-left hover:bg-white/20 focus:outline-none transition-all duration-200"
                            @click="toggleGroup('{{ $groupSlug }}')"
                            :class="isGroupOpen('{{ $groupSlug }}') ? 'bg-white/30 border-b border-white/10' : ''"
                            :aria-expanded="isGroupOpen('{{ $groupSlug }}')"
                        >
                            <span class="font-extrabold text-gray-800 text-sm md:text-base flex items-center gap-3">
                                <span class="p-2 bg-white/60 rounded-xl shadow-sm inline-flex items-center justify-center">
                                    {!! $items->icon_svg !!}
                                </span>
                                {{ $items->nama_lengkap }}
                            </span>
                            <svg
                                class="w-5 h-5 text-gray-500 transition-transform duration-300 mr-1"
                                :class="{ 'rotate-180 text-blue-600': isGroupOpen('{{ $groupSlug }}') }"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Daftar Sub-Akordion Anak -->
                        <div
                            x-show="isGroupOpen('{{ $groupSlug }}')"
                            x-collapse
                            class="p-3 md:p-4 space-y-2.5 bg-white/10"
                        >
                            @foreach($items as $index => $item)
                                @php
                                    $accordionSlug = $groupSlug . '-' . $index;
                                @endphp

                                <div class="border border-white/20 rounded-xl overflow-hidden bg-white/30 transition-all duration-200">
                                    <!-- Header Sub-Akordion -->
                                    <button
                                        class="w-full flex justify-between items-center p-3 text-left hover:bg-white/40 focus:outline-none transition-all duration-150"
                                        @click="toggleAccordion('{{ $accordionSlug }}')"
                                        :class="isAccordionOpen('{{ $accordionSlug }}') ? 'bg-white/40 border-b border-white/10' : ''"
                                        :aria-expanded="isAccordionOpen('{{ $accordionSlug }}')"
                                    >
                                        <span class="flex items-center gap-2.5 text-xs md:text-sm font-bold text-gray-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3.5 h-3.5 text-blue-600">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                            </svg>
                                            {{ $item->nama }}
                                        </span>
                                        <svg
                                            class="w-4 h-4 text-gray-500 transition-transform duration-200"
                                            :class="{ 'rotate-180 text-blue-600': isAccordionOpen('{{ $accordionSlug }}') }"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                    <!-- Konten Persyaratan -->
                                    <div
                                        x-show="isAccordionOpen('{{ $accordionSlug }}')"
                                        x-collapse
                                    >
                                        <div class="p-3 bg-white/50 backdrop-blur-sm rounded-b-xl">
                                            <ul class="space-y-2">
                                                @foreach($item->persyaratan_items as $syarat)
                                                    @if(trim($syarat))
                                                        <li class="flex items-start gap-3 py-2 border-b border-white/20 last:border-b-0">
                                                            <div class="bg-green-100 text-green-600 p-0.5 rounded-full mt-0.5 flex-shrink-0 inline-flex items-center justify-center">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            </div>
                                                            <span class="text-gray-700 font-semibold text-xs md:text-sm leading-relaxed">
                                                                {{ $syarat }}
                                                                @if($url = $getDownloadLink($syarat, $keterangan))
                                                                    <a href="{{ $url }}" class="ml-1.5 px-2 py-0.5 text-[10px] bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 rounded hover:shadow-sm transition inline-flex items-center gap-0.5" target="_blank">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-width="2" stroke-linecap="round"/></svg> Unduh
                                                                    </a>
                                                                @endif
                                                            </span>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>                                        
</div>
@endsection
