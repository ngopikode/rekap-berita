<?php

use App\Models\Article;
use App\Models\Publisher;
use Illuminate\Support\Carbon;
use Livewire\WithPagination;
use function Livewire\Volt\{state, computed, layout, uses};

// Mengaktifkan fitur Pagination & Layout
uses([WithPagination::class]);
layout('components.layouts.app');

// State Pencarian
state(['search' => '']);

// State Modal
state(['showModal' => false, 'modalMode' => 'add', 'formId' => null, 'formUrl' => '', 'formPublisherId' => '', 'formPublishedAt' => '']);

// Computed: Ambil list publisher untuk dropdown di form
$publishers = computed(function () {
    return Publisher::where('user_id', auth()->id())->orderBy('name', 'asc')->get();
});

// Computed: Ambil daftar artikel dengan pagination
$articles = computed(function () {
    $userId = auth()->id();
    return Article::whereHas('publisher', function ($q) use ($userId) {
        $q->where('user_id', $userId);
    })
        ->when($this->search, function ($q) {
            $q->where('url', 'like', '%' . $this->search . '%')
                ->orWhereHas('publisher', function ($q2) {
                    $q2->where('name', 'like', '%' . $this->search . '%');
                });
        })
        ->with('publisher')
        ->orderBy('published_at', 'desc')
        ->paginate(15); // Menampilkan 15 data per halaman
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
    $article = Article::whereHas('publisher', function ($q) {
        $q->where('user_id', auth()->id());
    })->findOrFail($id);

    $this->modalMode = 'edit';
    $this->formId = $article->id;
    $this->formUrl = $article->url;
    $this->formPublisherId = $article->publisher_id;

    // Perbaikan di baris ini: pastikan diformat ke YYYY-MM-DD
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
    $existingArticle = Article::where('url_hash', $urlHash)->first();

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

// Aksi: Hapus Data
$delete = function ($id) {
    $article = Article::whereHas('publisher', function ($q) {
        $q->where('user_id', auth()->id());
    })->find($id);

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
        <button wire:click="openAddModal" class="btn btn-brand fw-medium" style="border-radius: 0.75rem;">
            <i class="bi bi-plus-lg me-1"></i> Tambah Manual
        </button>
    </div>

    {{-- Tabel Data --}}
    <div class="card position-relative mb-4">
        <div
            class="card-header bg-transparent border-bottom-0 p-4 pb-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="font-serif fw-bold mb-0 text-dark">Semua Tautan</h5>

            <div class="input-group" style="width: 100%; max-width: 300px;">
                <span class="input-group-text bg-transparent border-end-0 text-muted"><i
                        class="bi bi-search"></i></span>
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-start-0 ps-0"
                       placeholder="Cari link atau nama media...">
            </div>
        </div>

        <div class="card-body p-0 mt-3">
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
                            <tr style="border-bottom: 1px solid var(--ezmenu-border-color);">
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
                                    <button wire:click="openEditModal({{ $article->id }})"
                                            class="btn btn-sm btn-light text-primary me-1"
                                            style="border: 1px solid var(--ezmenu-border-color); border-radius: 0.5rem;"
                                            title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <button x-data
                                            @click="if(confirm('Yakin ingin menghapus tautan berita ini?')) { $wire.delete({{ $article->id }}) }"
                                            class="btn btn-sm btn-light text-danger"
                                            style="border: 1px solid var(--ezmenu-border-color); border-radius: 0.5rem;"
                                            title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Pagination (jika data lebih dari 15) --}}
        @if($this->articles->hasPages())
            <div class="card-footer bg-transparent border-top-0 px-4 py-3">
                {{ $this->articles->links('pagination::bootstrap-5') }}
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
                                            <option value="{{ $pub->id }}">{{ $pub->name }}</option>
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
                                <button type="submit" class="btn btn-brand px-4" style="border-radius: 0.75rem;">
                                    <i class="bi bi-save me-1"></i> Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
