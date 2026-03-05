<?php

use App\Models\Article;
use App\Models\Publisher;
use function Livewire\Volt\{state, computed, layout};

// Mengatur layout bawaan aplikasi
layout('components.layouts.app');

// State Pencarian
state(['search' => '']);

// State Modal Tambah/Edit
state(['showModal' => false, 'modalMode' => 'add', 'formId' => null, 'formName' => '']);

// State Modal Merge (Penggabungan)
state(['showMergeModal' => false, 'mergeTargetId' => null, 'mergeTargetName' => '']);

// Computed: Ambil daftar media + jumlah artikelnya
$publishers = computed(function () {
    return Publisher::where('user_id', auth()->id())
        ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
        ->withCount('articles')
        ->orderBy('name', 'asc')
        ->get();
});

// Aksi: Buka modal tambah
$openAddModal = function () {
    $this->modalMode = 'add';
    $this->formId = null;
    $this->formName = '';
    $this->showModal = true;
};

// Aksi: Buka modal edit
$openEditModal = function ($id) {
    $pub = Publisher::where('user_id', auth()->id())->findOrFail($id);
    $this->modalMode = 'edit';
    $this->formId = $pub->id;
    $this->formName = $pub->name;
    $this->showModal = true;
};

// Aksi: Tutup semua modal
$closeModal = function () {
    $this->showModal = false;
    $this->showMergeModal = false;
};

// Aksi: Simpan Data (Dengan Cek Duplikat & Merge)
$save = function () {
    $this->formName = strtoupper(trim($this->formName));

    if (empty($this->formName)) {
        $this->dispatch('notify', ['type' => 'danger', 'message' => 'Nama media tidak boleh kosong.']);
        return;
    }

    // Cek apakah nama yang diinput sudah ada di database
    $existingPub = Publisher::where('user_id', auth()->id())->where('name', $this->formName)->first();

    if ($this->modalMode === 'add') {
        if ($existingPub) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Media dengan nama ini sudah ada!']);
            return;
        }

        Publisher::create(['user_id' => auth()->id(), 'name' => $this->formName]);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Media berhasil ditambahkan.']);
        $this->closeModal();

    } else { // Mode Edit
        if ($existingPub && $existingPub->id !== $this->formId) {
            // Jika nama sudah ada dan bukan ID yang sedang diedit -> Picu Konfirmasi Merge!
            $this->mergeTargetId = $existingPub->id;
            $this->mergeTargetName = $existingPub->name;
            $this->showModal = false;
            $this->showMergeModal = true;
            return;
        }

        // Jika aman, update nama saja
        Publisher::where('id', $this->formId)->update(['name' => $this->formName]);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Nama media berhasil diperbarui.']);
        $this->closeModal();
    }
};

// Aksi: Eksekusi Merge (Gabungkan)
$executeMerge = function () {
    // 1. Pindahkan semua artikel dari Publisher Lama ke Publisher Target
    Article::where('publisher_id', $this->formId)->update(['publisher_id' => $this->mergeTargetId]);

    // 2. Hapus Publisher Lama
    Publisher::where('id', $this->formId)->delete();

    $this->dispatch('notify', ['type' => 'success', 'message' => 'Data media berhasil digabungkan!']);
    $this->closeModal();
};

// Aksi: Hapus Data beserta artikelnya
$delete = function ($id) {
    $pub = Publisher::where('user_id', auth()->id())->find($id);
    if ($pub) {
        // Hapus semua artikel miliknya terlebih dahulu
        Article::where('publisher_id', $pub->id)->delete();
        // Baru hapus publisher-nya
        $pub->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Media dan seluruh artikelnya berhasil dihapus.']);
    }
};

?>

<div>
    <x-slot name="header">
        {{ __('Manajemen Media') }}
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
            <h3 class="font-serif fw-bold mb-0 text-dark">Manajemen Media</h3>
            <p class="text-muted small mb-0">Kelola daftar penerbit berita dan penggabungan data.</p>
        </div>
        <button wire:click="openAddModal" class="btn btn-brand fw-medium" style="border-radius: 0.75rem;">
            <i class="bi bi-plus-lg me-1"></i> Tambah Media
        </button>
    </div>

    {{-- Tabel Card --}}
    <div class="card position-relative">
        <div
            class="card-header bg-transparent border-bottom-0 p-4 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="font-serif fw-bold mb-0 text-dark">Daftar Media</h5>

            <div class="input-group" style="width: 250px;">
                <span class="input-group-text bg-transparent border-end-0 text-muted"><i
                        class="bi bi-search"></i></span>
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-start-0 ps-0"
                       placeholder="Cari media...">
            </div>
        </div>

        <div class="card-body p-0 mt-3">
            @if($this->publishers->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-hdd-network text-muted" style="font-size: 3rem;"></i>
                    <h6 class="font-serif fw-bold mt-3 mb-1 text-dark">Belum ada media</h6>
                    <p class="text-muted small">Mulai tambahkan media baru atau biarkan sistem mencatatnya otomatis dari
                        rekap.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table mb-0 align-middle text-center">
                        <thead
                            style="background-color: var(--ezmenu-bg-sidebar); border-bottom: 2px solid var(--ezmenu-border-color);">
                        <tr>
                            <th class="text-start px-4 font-serif text-muted small fw-bold">NAMA MEDIA</th>
                            <th class="font-serif text-muted small fw-bold">TOTAL ARTIKEL</th>
                            <th class="text-end px-4 font-serif text-muted small fw-bold">AKSI</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($this->publishers as $pub)
                            <tr style="border-bottom: 1px solid var(--ezmenu-border-color);">
                                <td class="text-start px-4 fw-medium text-dark">{{ $pub->name }}</td>
                                <td>
                                        <span class="badge text-dark bg-light px-3 py-2"
                                              style="border: 1px solid var(--ezmenu-border-color);">
                                            {{ $pub->articles_count }} Artikel
                                        </span>
                                </td>
                                <td class="text-end px-4">
                                    <button wire:click="openEditModal({{ $pub->id }})"
                                            class="btn btn-sm btn-light text-primary me-1"
                                            style="border: 1px solid var(--ezmenu-border-color); border-radius: 0.5rem;"
                                            title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    {{-- Alpine JS untuk konfirmasi Hapus --}}
                                    <button x-data
                                            @click="if(confirm('Yakin ingin menghapus media ini beserta {{ $pub->articles_count }} artikelnya?')) { $wire.delete({{ $pub->id }}) }"
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
    </div>

    {{-- MODAL TAMBAH / EDIT --}}
    @if($showModal)
        <div class="modal show d-block" tabindex="-1"
             style="background-color: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px); z-index: 9999;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-lg"
                     style="background-color: var(--ezmenu-bg-card); border: 1px solid var(--ezmenu-border-color); border-radius: 1.25rem;">

                    <div class="modal-header border-bottom-0 px-4 py-4">
                        <h5 class="modal-title font-serif fw-bold text-dark">
                            {{ $modalMode === 'add' ? 'Tambah Media Baru' : 'Edit Media' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>

                    <div class="modal-body p-4 pt-0">
                        <form wire:submit.prevent="save">
                            <div class="mb-3">
                                <label class="form-label font-serif text-muted small fw-bold">Nama Media <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control text-uppercase fw-bold" wire:model="formName"
                                       placeholder="Cth: DETIKRIAU65">
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" class="btn btn-light"
                                        style="background-color: var(--ezmenu-border-color); color: var(--ezmenu-text-main); border: none; border-radius: 0.75rem;"
                                        wire:click="closeModal">Batal
                                </button>
                                <button type="submit" class="btn btn-brand px-4" style="border-radius: 0.75rem;">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL KONFIRMASI MERGE (PENGGABUNGAN) --}}
    @if($showMergeModal)
        <div class="modal show d-block" tabindex="-1"
             style="background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px); z-index: 9999;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-lg"
                     style="background-color: var(--ezmenu-bg-card); border: 2px solid var(--bs-warning); border-radius: 1.25rem;">

                    <div class="modal-header border-bottom-0 px-4 py-4 d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
                        <h5 class="modal-title font-serif fw-bold text-dark mb-0">Konfirmasi Penggabungan</h5>
                        <button type="button" class="btn-close ms-auto" wire:click="closeModal"></button>
                    </div>

                    <div class="modal-body px-4 pb-4 pt-0">
                        <p class="text-muted" style="font-size: 0.95rem;">
                            Media dengan nama <strong class="text-dark">{{ $mergeTargetName }}</strong> sudah ada di
                            database.
                        </p>
                        <div class="alert alert-warning border-0"
                             style="background-color: rgba(255, 193, 7, 0.1); border-radius: 0.75rem;">
                            Apakah Anda ingin memindahkan <strong>semua artikel</strong> dari media ini ke
                            <strong>{{ $mergeTargetName }}</strong>, dan menghapus nama yang lama?
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-light"
                                    style="background-color: var(--ezmenu-border-color); color: var(--ezmenu-text-main); border: none; border-radius: 0.75rem;"
                                    wire:click="closeModal">Batal
                            </button>
                            <button type="button" class="btn btn-warning fw-bold px-4" style="border-radius: 0.75rem;"
                                    wire:click="executeMerge">
                                Ya, Gabungkan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
