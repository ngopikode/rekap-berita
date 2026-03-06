<?php

use App\Models\Article;
use App\Models\Publisher;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Volt\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ArticlesExport;
use function Livewire\Volt\{state, computed, on, mount};

// State Utama & Filter
state(['bulkLinks' => '']);
state(['selectedMonth' => fn() => now()->month]);
state(['selectedYear' => fn() => now()->year]);

// State untuk Modal Preview
state(['previewData' => []]);
state(['showPreviewModal' => false]);

// Computed: Mendapatkan jumlah hari dalam bulan & tahun yang dipilih
$daysInMonth = computed(function () {
    return Carbon::create($this->selectedYear, $this->selectedMonth, 1)->daysInMonth;
});

// Computed: Mendapatkan data sesuai filter
$publishers = computed(function () {
    $startOfMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
    $endOfMonth = $startOfMonth->copy()->endOfMonth();

    return Publisher::where('user_id', auth()->id())
        ->with(['articles' => function ($query) use ($startOfMonth, $endOfMonth) {
            $query->whereBetween('published_at', [$startOfMonth, $endOfMonth]);
        }])
        ->orderBy('name', 'asc')
        ->get();
});

// Fungsi 1: Generate Preview
$generatePreview = function () {
    $urls = collect(explode("\n", $this->bulkLinks))
        ->map(fn($url) => trim($url))
        ->filter(fn($url) => !empty($url));

    if ($urls->isEmpty()) {
        $this->dispatch('notify', ['type' => 'danger', 'message' => 'Teks kosong! Tempelkan URL berita terlebih dahulu.']);
        return;
    }

    $tempData = [];
    $skippedCount = 0;
    $fallbackDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->toDateString();

    foreach ($urls as $url) {
        $urlHash = md5($url);
        if (Article::where('url_hash', $urlHash)->exists()) {
            $skippedCount++;
            continue;
        }

        $isInPreview = collect($tempData)->contains('url', $url);
        if ($isInPreview) continue;

        try {
            $host = parse_url($url, PHP_URL_HOST);
            if (!$host) continue;

            $publisherName = strtoupper(str_replace(['www.', '.com', '.co.id', '.id', '.net'], '', $host));
            $publishedDate = $fallbackDate;

            try {
                $response = Http::timeout(5)->get($url);
                if ($response->successful()) {
                    $html = $response->body();
                    if (preg_match('/<meta property="article:published_time" content="([^"]+)"/', $html, $matches)) {
                        $publishedDate = Carbon::parse($matches[1])->toDateString();
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Gagal scrape URL: $url");
            }

            $tempData[] = [
                'url' => $url,
                'publisher_name' => $publisherName,
                'published_at' => $publishedDate,
            ];

        } catch (\Exception $e) {
            continue;
        }
    }

    if (empty($tempData) && $skippedCount > 0) {
        $this->dispatch('notify', ['type' => 'warning', 'message' => "Selesai! Tapi {$skippedCount} link sudah pernah diinput sebelumnya."]);
        $this->bulkLinks = '';
        return;
    }

    if (empty($tempData)) {
        $this->dispatch('notify', ['type' => 'danger', 'message' => "Tidak ada link yang valid."]);
        return;
    }

    $this->previewData = $tempData;
    $this->showPreviewModal = true;

    if ($skippedCount > 0) {
        $this->dispatch('notify', ['type' => 'success', 'message' => "Preview siap. {$skippedCount} link duplikat otomatis dibuang."]);
    }
};

// Fungsi 2: Simpan Data
$saveData = function () {
    $count = 0;
    foreach ($this->previewData as $item) {
        try {
            $publisher = Publisher::firstOrCreate(
                ['name' => strtoupper($item['publisher_name']), 'user_id' => auth()->id()],
                ['user_id' => auth()->id()]
            );

            Article::create([
                'publisher_id' => $publisher->id,
                'url' => $item['url'],
                'url_hash' => md5($item['url']),
                'published_at' => $item['published_at'],
            ]);
            $count++;
        } catch (\Exception $e) {
            Log::error("Gagal simpan artikel: " . $e->getMessage());
        }
    }

    $this->previewData = [];
    $this->showPreviewModal = false;
    $this->bulkLinks = '';

    $this->dispatch('notify', ['type' => 'success', 'message' => "Mantap! {$count} artikel berhasil masuk rekap."]);
};

// Fungsi 3: Hapus item
$removeItem = function ($index) {
    unset($this->previewData[$index]);
    $this->previewData = array_values($this->previewData);
    if (empty($this->previewData)) $this->showPreviewModal = false;
};

// Fungsi 4: Batal
$cancelPreview = function () {
    $this->previewData = [];
    $this->showPreviewModal = false;
};

// Fungsi 5: Export
$export = function () {
    $monthName = Carbon::create(null, $this->selectedMonth)->translatedFormat('F');
    $fileName = "rekap-berita-{$monthName}-{$this->selectedYear}.xlsx";
    return Excel::download(new ArticlesExport($this->selectedMonth, $this->selectedYear), $fileName);
};

?>

<div>
    @push('custom-styles')
        <style>
            /* HANYA CSS UNTUK FUNGSI SCROLL & STICKY TABEL */
            /* Selebihnya menggunakan variabel dari app.css milikmu */

            .table-rekap-wrapper {
                position: relative;
                max-height: 65vh;
                overflow-x: auto;
                overflow-y: auto;
            }

            .table-rekap {
                border-collapse: separate;
                border-spacing: 0;
                width: 100%;
            }

            .table-rekap th, .table-rekap td {
                white-space: nowrap;
                vertical-align: middle;
                padding: 0.75rem;
                color: var(--ezmenu-text-main);
                border-bottom: 1px solid var(--ezmenu-border-color);
            }

            .table-rekap thead th {
                position: sticky;
                top: 0;
                z-index: 20;
                background-color: var(--ezmenu-bg-sidebar) !important;
                font-family: var(--font-serif);
                font-weight: 600;
                font-size: 0.8rem;
                letter-spacing: 0.5px;
                text-transform: uppercase;
                border-bottom: 2px solid var(--ezmenu-border-color);
            }

            /* Kolom Kiri Nempel (Sticky) */
            .sticky-col {
                position: sticky;
                left: 0;
                z-index: 21 !important;
                background-color: var(--ezmenu-bg-card) !important;
                border-right: 1px solid var(--ezmenu-border-color) !important;
            }

            .table-rekap thead th.sticky-col {
                z-index: 22 !important;
            }

            /* Loading Overlay menyatu dengan tema */
            .loading-overlay {
                position: absolute;
                inset: 0;
                background-color: var(--ezmenu-bg-navbar); /* Pakai warna navbar yang ada efek blur & alpha */
                z-index: 50;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                border-radius: 1.25rem;
            }

            /* Custom Scrollbar */
            ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }

            ::-webkit-scrollbar-track {
                background: transparent;
            }

            ::-webkit-scrollbar-thumb {
                background: var(--ezmenu-border-color);
                border-radius: 4px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: var(--brand-color);
            }
        </style>
    @endpush

    {{-- Toast Notification --}}
    <div x-data="{ show: false, message: '', type: 'success' }"
         @notify.window="
            let data = $event.detail[0] || $event.detail;
            message = data.message;
            type = data.type;
            show = true;
            setTimeout(() => show = false, 4000);
         "
         x-show="show" x-transition.opacity
         class="position-fixed top-0 end-0 p-4" style="z-index: 9999; display: none;">

        <div class="toast show align-items-center shadow-lg"
             style="background-color: var(--ezmenu-bg-card); border: 1px solid var(--ezmenu-border-color); border-radius: 0.75rem; border-left: 5px solid; border-left-color: var(--bs-primary);">
            <div class="d-flex p-2">
                <div class="toast-body fw-medium d-flex align-items-center gap-2"
                     style="font-size: 0.95rem; color: var(--ezmenu-text-main);">
                    <i class="bi" :class="{
                        'bi-check-circle-fill text-success': type === 'success',
                        'bi-exclamation-triangle-fill text-warning': type === 'warning' || type === 'danger'
                    }"></i>
                    <span x-text="message"></span>
                </div>
                <button type="button" class="btn-close me-2 m-auto" @click="show = false"
                        style="filter: var(--bs-body-color)"></button>
            </div>
        </div>
    </div>

    {{-- Page Header --}}
    <div class="mb-4">
        <h3 class="font-serif fw-bold mb-0 text-dark">Rekap Media</h3>
        <p class="text-muted small mb-0">Otomatisasi laporan tautan berita harian.</p>
    </div>

    {{-- 1. FORM INPUT LINK (Menggunakan class .card bawaan aplikasimu) --}}
    <div class="card mb-4 position-relative">
        <div wire:loading wire:target="generatePreview">
            <div class="loading-overlay">
                <div class="spinner-border text-brand" role="status"></div>
                <span class="mt-2 text-brand fw-medium font-serif">Memproses URL...</span>
            </div>
        </div>

        <div class="card-body p-4">
            <form wire:submit.prevent="generatePreview">
                <div class="mb-3">
                    <label class="form-label font-serif fw-bold d-flex align-items-center gap-2">
                        <i class="bi bi-link-45deg fs-4 text-brand"></i> Tempel Kumpulan Link
                    </label>
                    <textarea wire:model="bulkLinks" wire:loading.attr="disabled" rows="4"
                              class="form-control"
                              placeholder="https://derakpost.com/berita-1&#10;https://detikriau65.com/berita-2&#10;Pisahkan antar link dengan baris baru (Enter)..."></textarea>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" wire:loading.attr="disabled" class="btn btn-brand px-4">
                        <span wire:loading.remove wire:target="generatePreview">
                            <i class="bi bi-magic me-1"></i> Proses Link
                        </span>
                        <span wire:loading wire:target="generatePreview">Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 2. TABEL REKAP & FILTER --}}
    <div class="card position-relative">
        <div wire:loading wire:target="selectedMonth, selectedYear">
            <div class="loading-overlay">
                <div class="spinner-grow text-brand" role="status"></div>
            </div>
        </div>

        <div
            class="card-header bg-transparent border-bottom-0 p-4 pb-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="font-serif fw-bold mb-0 text-dark">Laporan Rekapitulasi</h5>

            <div class="d-flex flex-wrap gap-2">
                <select wire:model.live="selectedMonth" wire:loading.attr="disabled" class="form-select form-select-sm"
                        style="width: 140px;">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}">{{ Carbon::create(null, $m)->translatedFormat('F') }}</option>
                    @endforeach
                </select>

                <select wire:model.live="selectedYear" wire:loading.attr="disabled" class="form-select form-select-sm"
                        style="width: 100px;">
                    @foreach(range(now()->year - 2, now()->year + 1) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>

                <button wire:click="export" wire:loading.attr="disabled"
                        class="btn btn-sm btn-outline-success fw-medium d-flex align-items-center gap-1"
                        style="border-radius: 0.75rem;">
                    <span wire:loading.remove wire:target="export"><i
                            class="bi bi-file-earmark-excel"></i> Export</span>
                    <span wire:loading wire:target="export" class="spinner-border spinner-border-sm"></span>
                </button>
            </div>
        </div>

        <div class="card-body p-0 mt-3">
            @if($this->publishers->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-folder2-open text-muted" style="font-size: 3rem;"></i>
                    <h6 class="font-serif fw-bold mt-3 mb-1 text-dark">Data Masih Kosong</h6>
                    <p class="text-muted small">Belum ada rekap berita untuk bulan ini.</p>
                </div>
            @else
                <div class="table-rekap-wrapper pb-2">
                    <table class="table-rekap text-center">
                        <thead>
                        <tr>
                            <th class="sticky-col text-start px-4">Nama Media</th>
                            @for ($day = 1; $day <= $this->daysInMonth; $day++)
                                @php
                                    $date = Carbon::create($this->selectedYear, $this->selectedMonth, $day);
                                    $isSunday = $date->isSunday();
                                @endphp
                                <th class="{{ $isSunday ? 'text-danger' : '' }}"
                                    title="{{ $date->translatedFormat('l, d F Y') }}">
                                    {{ $day }}
                                </th>
                            @endfor
                            <th class="text-brand px-4">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($this->publishers as $index => $publisher)
                            <tr>
                                <td class="sticky-col text-start fw-medium px-4">
                                    {{ $publisher->name }}
                                </td>
                                @php
                                    $total = 0;
                                    $articlesByDay = $publisher->articles->groupBy(fn($article) => Carbon::parse($article->published_at)->day);
                                @endphp

                                @for ($day = 1; $day <= $this->daysInMonth; $day++)
                                    @php
                                        $count = $articlesByDay->get($day, collect())->count();
                                        $total += $count;
                                    @endphp
                                    <td>
                                        @if ($count > 0)
                                            <span class="text-dark fw-bold">{{ $count }}</span>
                                        @else
                                            <span class="text-muted" style="opacity: 0.3;">-</span>
                                        @endif
                                    </td>
                                @endfor
                                <td class="fw-bold text-brand px-4 fs-6">{{ $total }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td class="sticky-col text-end font-serif fw-bold px-4 text-muted"
                                style="font-size: 0.8rem;">TOTAL HARIAN
                            </td>
                            @php
                                $grandTotal = 0;
                                $userPublisherIds = \App\Models\Publisher::where('user_id', auth()->id())->pluck('id');
                                $articlesAll = \App\Models\Article::whereIn('publisher_id', $userPublisherIds)
                                                ->whereMonth('published_at', $this->selectedMonth)
                                                ->whereYear('published_at', $this->selectedYear)
                                                ->get();
                                $articlesByDayAll = $articlesAll->groupBy(fn($a) => Carbon::parse($a->published_at)->day);
                            @endphp
                            @for ($day = 1; $day <= $this->daysInMonth; $day++)
                                @php
                                    $count = $articlesByDayAll->get($day, collect())->count();
                                    $grandTotal += $count;
                                @endphp
                                <td class="fw-bold text-dark py-3">
                                    {{ $count > 0 ? $count : '' }}
                                </td>
                            @endfor
                            <td class="px-4 py-3 text-white fw-bold fs-5"
                                style="background-color: var(--brand-color);">{{ $grandTotal }}</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL PREVIEW (Menyesuaikan dengan app.css sepenuhnya) --}}
    @if($showPreviewModal)
        <div class="modal show d-block" tabindex="-1"
             style="background-color: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px); z-index: 9999;">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">

                {{-- Menggunakan background dan border dari app.css milikmu --}}
                <div class="modal-content shadow-lg"
                     style="background-color: var(--ezmenu-bg-card); border: 1px solid var(--ezmenu-border-color); border-radius: 1.25rem;">

                    <div wire:loading wire:target="saveData">
                        <div class="loading-overlay" style="border-radius: 1.25rem;">
                            <div class="spinner-border text-brand" style="width: 3rem; height: 3rem;"
                                 role="status"></div>
                            <h6 class="mt-3 fw-bold text-brand font-serif">Menyimpan {{ count($previewData) }}
                                Data...</h6>
                        </div>
                    </div>

                    <div class="modal-header border-bottom-0 px-4 py-4">
                        <div>
                            <h5 class="modal-title font-serif fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-shield-check text-brand fs-4"></i> Konfirmasi Data
                            </h5>
                            <p class="text-muted small mb-0 mt-1">Pastikan nama media dan tanggal sudah sesuai.</p>
                        </div>
                        <button type="button" class="btn-close" wire:click="cancelPreview"></button>
                    </div>

                    <div class="modal-body p-3 p-md-4 pt-0">
                        <div class="d-flex flex-column gap-3">
                            @foreach($previewData as $index => $item)
                                {{-- Tiap baris data menggunakan border dari app.css --}}
                                <div class="row align-items-center gy-3 p-3 mx-0"
                                     style="border: 1px solid var(--ezmenu-border-color); border-radius: 0.75rem;"
                                     wire:key="preview-{{ $index }}">

                                    <div class="col-12 col-lg-5">
                                        <div class="d-flex align-items-start gap-2 overflow-hidden">
                                            <div class="badge mt-1"
                                                 style="background-color: var(--ezmenu-border-color); color: var(--ezmenu-text-main);">{{ $index + 1 }}</div>
                                            <div style="width: 100%;">
                                                <span class="d-block text-muted small fw-medium mb-1">URL Berita</span>
                                                <a href="{{ $item['url'] }}" target="_blank"
                                                   class="text-decoration-none text-brand d-block text-truncate fw-medium">
                                                    {{ $item['url'] }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-6 col-lg-3">
                                        <label class="form-label small text-muted fw-medium mb-1">Nama Media</label>
                                        {{-- Class .form-control akan otomatis ditarik dari app.css milikmu --}}
                                        <input type="text" class="form-control text-uppercase"
                                               wire:model="previewData.{{ $index }}.publisher_name">
                                    </div>

                                    <div class="col-6 col-lg-3">
                                        <label class="form-label small text-muted fw-medium mb-1">Tanggal Rilis</label>
                                        <input type="date" class="form-control"
                                               wire:model="previewData.{{ $index }}.published_at">
                                    </div>

                                    <div class="col-12 col-lg-1 text-end text-lg-center">
                                        <button class="btn btn-outline-danger btn-sm" style="border-radius: 0.5rem;"
                                                wire:click="removeItem({{ $index }})" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="modal-footer border-top-0 px-4 py-3 bg-transparent">
                        <button type="button" class="btn btn-light"
                                style="background-color: var(--ezmenu-border-color); color: var(--ezmenu-text-main); border: none; border-radius: 0.75rem;"
                                wire:click="cancelPreview">Batal
                        </button>
                        <button type="button" class="btn btn-brand px-4" style="border-radius: 0.75rem;"
                                wire:click="saveData" wire:loading.attr="disabled">
                            <i class="bi bi-save me-1"></i> Simpan ({{ count($previewData) }})
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
