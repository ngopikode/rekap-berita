<?php

use App\Models\Article;
use App\Models\Publisher;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;
use function Livewire\Volt\{state, computed, layout, uses};

// Mengaktifkan fitur Pagination & Layout
uses([WithPagination::class]);
layout('components.layouts.app');

// State Pencarian
state(['search' => '']);

// State Modal
state(['showModal' => false, 'modalMode' => 'add', 'formId' => null, 'formUrl' => '', 'formPublisherId' => '', 'formPublishedAt' => '']);

// OPTIMASI: Ambil data publisher cukup kolom 'id' dan 'name' saja
$publishers = computed(function () {
    return Publisher::where('user_id', auth()->id())
        ->select('id', 'name')
        ->orderBy('name', 'asc')
        ->get();
});

// OPTIMASI: Performa Query Artikel
$articles = computed(function () {
    $userId = auth()->id();
    $publisherIds = Publisher::where('user_id', $userId)->pluck('id');

    return Article::query()
        ->select('id', 'publisher_id', 'url', 'published_at')
        ->with('publisher:id,name')
        ->whereIn('publisher_id', $publisherIds)
        ->when($this->search, function ($q) {
            $search = '%' . trim($this->search) . '%';
            $q->where(function ($query) use ($search) {
                $query->where('url', 'like', $search)
                    ->orWhereHas('publisher', function ($sub) use ($search) {
                        $sub->where('name', 'like', $search);
                    });
            });
        })
        ->orderBy('published_at', 'desc')
        ->paginate(15);
});

// Reset ke halaman 1 saat mengetik pencarian
$updatingSearch = function () {
    $this->resetPage();
};

// Aksi: Buka modal tambah manual
$openAddModal = function () {
    $this->modalMode = 'add';
    $this->formId = null;
    $this->formUrl = '';
    $this->formPublisherId = '';
    $this->formPublishedAt = now()->toDateString();
    $this->showModal = true;
};

// Aksi: Buka modal edit
$openEditModal = function ($id) {
    $publisherIds = Publisher::where('user_id', auth()->id())->pluck('id');
    $article = Article::whereIn('publisher_id', $publisherIds)->findOrFail($id);

    $this->modalMode = 'edit';
    $this->formId = $article->id;
    $this->formUrl = $article->url;
    $this->formPublisherId = $article->publisher_id;
    $this->formPublishedAt = Carbon::parse($article->published_at)->toDateString();

    $this->showModal = true;
};

// Aksi: Tutup modal
$closeModal = function () {
    $this->showModal = false;
};

// Aksi: Simpan Data (Add / Edit)
$save = function () {
    $this->formUrl = trim($this->formUrl);

    // Validasi kosong
    if (empty($this->formUrl) || empty($this->formPublisherId) || empty($this->formPublishedAt)) {
        $this->dispatch('notify', ['type' => 'danger', 'message' => 'Semua kolom wajib diisi.']);
        return;
    }

    $urlHash = md5($this->formUrl);
    $existingArticle = Article::where('url_hash', $urlHash)->select('id')->first();

    if ($this->modalMode === 'add') {
        if ($existingArticle) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'URL berita ini sudah pernah disimpan!']);
            return;
        }

        Article::create([
            'publisher_id' => $this->formPublisherId,
            'url' => $this->formUrl,
            'url_hash' => $urlHash,
            'published_at' => $this->formPublishedAt,
        ]);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Berita berhasil ditambahkan manual.']);

    } else { // Mode Edit
        if ($existingArticle && $existingArticle->id !== $this->formId) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'URL ini sudah digunakan di data berita lain!']);
            return;
        }

        Article::where('id', $this->formId)->update([
            'publisher_id' => $this->formPublisherId,
            'url' => $this->formUrl,
            'url_hash' => $urlHash,
            'published_at' => $this->formPublishedAt,
        ]);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Data berita berhasil diperbarui.']);
    }

    $this->closeModal();
};

// Aksi: Crawl Ulang Data Spesifik
$recrawl = function ($id) {
    $publisherIds = Publisher::where('user_id', auth()->id())->pluck('id');
    $article = Article::whereIn('publisher_id', $publisherIds)->find($id);

    if (!$article) {
        $this->dispatch('notify', ['type' => 'danger', 'message' => 'Berita tidak ditemukan.']);
        return;
    }

    try {
        $response = Http::timeout(5)
            ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'])
            ->get($article->url);

        if ($response->successful()) {
            $html = $response->body();
            $publishedDate = null;

            // PENINGKATAN: Pola pencarian tanggal super fleksibel
            // Mendukung kutip tunggal/ganda dan atribut terbalik (content di awal atau akhir)
            // PENINGKATAN: Pola Regex "Sapu Jagat"
            $patterns = [
                // 1. Standar Open Graph & Meta Tags
                '/<meta[^>]*property=[\'"]article:published_time[\'"][^>]*content=[\'"]([^\'"]+)[\'"]/i',
                '/<meta[^>]*content=[\'"]([^\'"]+)[\'"][^>]*property=[\'"]article:published_time[\'"]/i',
                '/<meta[^>]*itemprop=[\'"]datePublished[\'"][^>]*content=[\'"]([^\'"]+)[\'"]/i',
                '/<meta[^>]*content=[\'"]([^\'"]+)[\'"][^>]*itemprop=[\'"]datePublished[\'"]/i',
                '/<meta[^>]*name=[\'"]pubdate[\'"][^>]*content=[\'"]([^\'"]+)[\'"]/i',
                '/<meta[^>]*content=[\'"]([^\'"]+)[\'"][^>]*name=[\'"]pubdate[\'"]/i',
                '/<meta[^>]*name=[\'"]date[\'"][^>]*content=[\'"]([^\'"]+)[\'"]/i',

                // 2. JSON-LD Schema
                '/"datePublished"\s*:\s*[\'"]([^\'"]+)[\'"]/i',

                // 3. POLA BARU UNTUK KASUSMU: Menangkap atribut 'datetime' di tag APA PUN (<time>, <span>, <div>, dll)
                '/<[^>]*datetime=[\'"]([^\'"]+)[\'"]/i',

                // 4. Menangkap teks langsung di dalam tag yang punya class/itemprop khusus tanggal
                '/<[^>]*itemprop=[\'"][^\'"]*datePublished[^\'"]*[\'"][^>]*>\s*([^<]+)\s*<\/[^>]+>/i',
                '/<[^>]*class=[\'"][^\'"]*(?:published|post-date|updated|entry-date)[^\'"]*[\'"][^>]*>\s*([0-9]{4}-[0-9]{2}-[0-9]{2}[^<]*)\s*<\/[^>]+>/i'
            ];

            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $html, $matches)) {
                    try {
                        $parsedDate = Carbon::parse(trim($matches[1]));
                        if ($parsedDate->year > 2000) {
                            $publishedDate = $parsedDate->toDateString();
                            break;
                        }
                    } catch (\Exception $e) {
                        // abaikan dan cari pola lain
                    }
                }
            }

            if ($publishedDate) {
                if ($publishedDate !== $article->published_at) {
                    $article->update(['published_at' => $publishedDate]);
                    $this->dispatch('notify', ['type' => 'success', 'message' => 'Tanggal berhasil diperbarui menjadi ' . Carbon::parse($publishedDate)->translatedFormat('d M Y')]);
                } else {
                    $this->dispatch('notify', ['type' => 'success', 'message' => 'Tanggal sudah benar / up-to-date.']);
                }
            } else {
                $this->dispatch('notify', ['type' => 'warning', 'message' => 'Gagal menemukan tag tanggal di dalam halaman web tersebut.']);
            }
        } else {
            $this->dispatch('notify', ['type' => 'danger', 'message' => 'Web tujuan tidak bisa diakses saat ini (Mungkin error atau memblokir bot).']);
        }
    } catch (\Exception $e) {
        Log::error("Gagal recrawl URL: {$article->url} - " . $e->getMessage());
        $this->dispatch('notify', ['type' => 'danger', 'message' => 'Koneksi ke web tersebut gagal (Timeout).']);
    }
};

// Aksi: Hapus Data
$delete = function ($id) {
    $publisherIds = Publisher::where('user_id', auth()->id())->pluck('id');
    $article = Article::whereIn('publisher_id', $publisherIds)->find($id);

    if ($article) {
        $article->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Berita berhasil dihapus.']);
    }
};

?>

<div>
    <x-slot name="header">
        {{ __('Manajemen Berita') }}
    </x-slot>

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
    <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h3 class="font-serif fw-bold mb-0 text-dark">Daftar Berita</h3>
            <p class="text-muted small mb-0">Kelola semua tautan berita yang telah terekap.</p>
        </div>
        <button wire:click="openAddModal" class="btn btn-brand fw-medium" style="border-radius: 0.75rem;"
                wire:loading.attr="disabled" wire:target="openAddModal">
            <span wire:loading.remove wire:target="openAddModal"><i class="bi bi-plus-lg me-1"></i> Tambah Manual</span>
            <span wire:loading wire:target="openAddModal"><span class="spinner-border spinner-border-sm me-1"></span> Membuka...</span>
        </button>
    </div>

    {{-- Tabel Data --}}
    <div class="card position-relative mb-4">
        <div
            class="card-header bg-transparent border-bottom-0 p-4 pb-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="font-serif fw-bold mb-0 text-dark">Semua Tautan</h5>

            <div class="input-group" style="width: 100%; max-width: 300px;">
                <span class="input-group-text bg-transparent border-end-0 text-muted">
                    <div wire:loading wire:target="search" class="spinner-border spinner-border-sm text-primary"
                         role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <i wire:loading.remove wire:target="search" class="bi bi-search"></i>
                </span>
                <input type="text" wire:model.live.debounce.500ms="search" class="form-control border-start-0 ps-0"
                       placeholder="Cari link atau nama media...">
            </div>
        </div>

        <div class="card-body p-0 mt-3 position-relative">
            {{-- Overlay Loading Tabel --}}
            <div wire:loading.flex wire:target="gotoPage, nextPage, previousPage"
                 class="position-absolute w-100 h-100 justify-content-center align-items-center"
                 style="background-color: rgba(255, 255, 255, 0.6); z-index: 10; backdrop-filter: blur(2px);">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            @if($this->articles->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-link-45deg text-muted" style="font-size: 3rem;"></i>
                    <h6 class="font-serif fw-bold mt-3 mb-1 text-dark">Data Tidak Ditemukan</h6>
                    <p class="text-muted small">Belum ada tautan berita tersimpan atau pencarian tidak cocok.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead
                            style="background-color: var(--ezmenu-bg-sidebar); border-bottom: 2px solid var(--ezmenu-border-color);">
                        <tr>
                            <th class="text-start px-4 font-serif text-muted small fw-bold" style="width: 20%;">MEDIA
                            </th>
                            <th class="text-start font-serif text-muted small fw-bold" style="width: 45%;">URL BERITA
                            </th>
                            <th class="text-center font-serif text-muted small fw-bold" style="width: 15%;">TANGGAL</th>
                            <th class="text-end px-4 font-serif text-muted small fw-bold" style="width: 20%;">AKSI</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($this->articles as $article)
                            <tr wire:key="article-row-{{ $article->id }}"
                                style="border-bottom: 1px solid var(--ezmenu-border-color);">
                                <td class="text-start px-4 fw-bold text-dark" style="font-size: 0.9rem;">
                                    {{ $article->publisher->name }}
                                </td>
                                <td class="text-start">
                                    <a href="{{ $article->url }}" target="_blank"
                                       class="text-decoration-none text-brand d-inline-block text-truncate"
                                       style="max-width: 350px;" title="{{ $article->url }}">
                                        {{ $article->url }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    <span class="badge text-dark bg-light px-3 py-2 fw-medium"
                                          style="border: 1px solid var(--ezmenu-border-color);">
                                        {{ Carbon::parse($article->published_at)->translatedFormat('d M Y') }}
                                    </span>
                                </td>
                                <td class="text-end px-4">

                                    {{-- TOMBOL CRAWL ULANG --}}
                                    <button wire:click="recrawl({{ $article->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="recrawl({{ $article->id }})"
                                            class="btn btn-sm btn-light text-success me-1"
                                            style="border: 1px solid var(--ezmenu-border-color); border-radius: 0.5rem; width: 32px; height: 32px;"
                                            title="Crawl Tanggal Ulang dari Web">
                                        <span wire:loading.remove wire:target="recrawl({{ $article->id }})">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </span>
                                        <span wire:loading wire:target="recrawl({{ $article->id }})">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                  aria-hidden="true"></span>
                                        </span>
                                    </button>

                                    {{-- TOMBOL EDIT dengan Loading --}}
                                    <button wire:click="openEditModal({{ $article->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="openEditModal({{ $article->id }})"
                                            class="btn btn-sm btn-light text-primary me-1"
                                            style="border: 1px solid var(--ezmenu-border-color); border-radius: 0.5rem; width: 32px; height: 32px;"
                                            title="Edit">
                                        <span wire:loading.remove wire:target="openEditModal({{ $article->id }})">
                                            <i class="bi bi-pencil-square"></i>
                                        </span>
                                        <span wire:loading wire:target="openEditModal({{ $article->id }})">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                  aria-hidden="true"></span>
                                        </span>
                                    </button>

                                    {{-- TOMBOL HAPUS dengan SWEETALERT2 & Loading --}}
                                    <button x-data
                                            @click="
                                                Swal.fire({
                                                    title: 'Yakin ingin menghapus?',
                                                    text: 'Tautan berita yang dihapus tidak bisa dikembalikan!',
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#dc3545',
                                                    cancelButtonColor: '#6c757d',
                                                    confirmButtonText: 'Ya, Hapus!',
                                                    cancelButtonText: 'Batal',
                                                    reverseButtons: true,
                                                    customClass: {
                                                        popup: 'rounded-4 shadow-lg border-0'
                                                    }
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        $wire.delete({{ $article->id }});
                                                    }
                                                });
                                            "
                                            wire:loading.attr="disabled"
                                            wire:target="delete({{ $article->id }})"
                                            class="btn btn-sm btn-light text-danger"
                                            style="border: 1px solid var(--ezmenu-border-color); border-radius: 0.5rem; width: 32px; height: 32px;"
                                            title="Hapus">

                                        {{-- Ikon Tampil Saat Normal --}}
                                        <span wire:loading.remove wire:target="delete({{ $article->id }})">
                                            <i class="bi bi-trash"></i>
                                        </span>

                                        {{-- Spinner Tampil Saat Livewire Memproses Hapus Data --}}
                                        <span wire:loading wire:target="delete({{ $article->id }})">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                  aria-hidden="true"></span>
                                        </span>
                                    </button>

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if($this->articles->hasPages())
            <div class="card-footer bg-transparent border-top-0 px-4 py-3">
                {{ $this->articles->links('livewire::bootstrap') }}
            </div>
        @endif
    </div>

    {{-- MODAL TAMBAH / EDIT --}}
    @if($showModal)
        <div class="modal show d-block" tabindex="-1"
             style="background-color: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px); z-index: 9999;">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content shadow-lg"
                     style="background-color: var(--ezmenu-bg-card); border: 1px solid var(--ezmenu-border-color); border-radius: 1.25rem;">

                    <div class="modal-header border-bottom-0 px-4 py-4">
                        <h5 class="modal-title font-serif fw-bold text-dark">
                            {{ $modalMode === 'add' ? 'Tambah Berita Manual' : 'Edit Tautan Berita' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>

                    <div class="modal-body p-4 pt-0">
                        <form wire:submit.prevent="save">
                            <div class="mb-3">
                                <label class="form-label font-serif text-muted small fw-bold">URL Tautan <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" rows="2" wire:model="formUrl"
                                          placeholder="https://media.com/berita-xyz..." required></textarea>
                            </div>

                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <label class="form-label font-serif text-muted small fw-bold">Pilih Media <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" wire:model="formPublisherId" required>
                                        <option value="">-- Pilih Nama Media --</option>
                                        @foreach($this->publishers as $pub)
                                            <option wire:key="pub-{{ $pub->id }}"
                                                    value="{{ $pub->id }}">{{ $pub->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label class="form-label font-serif text-muted small fw-bold">Tanggal Rilis <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control" wire:model="formPublishedAt" required>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <button type="button" class="btn btn-light"
                                        style="background-color: var(--ezmenu-border-color); color: var(--ezmenu-text-main); border: none; border-radius: 0.75rem;"
                                        wire:click="closeModal">Batal
                                </button>

                                <button type="submit" class="btn btn-brand px-4" style="border-radius: 0.75rem;"
                                        wire:loading.attr="disabled" wire:target="save">
                                    <span wire:loading.remove wire:target="save"><i class="bi bi-save me-1"></i> Simpan</span>
                                    <span wire:loading wire:target="save"><span
                                            class="spinner-border spinner-border-sm me-1" role="status"
                                            aria-hidden="true"></span> Menyimpan...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Script CDN SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</div>
