<div>
    @if($showModal)
    <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered {{ $modalType === 'product' ? 'modal-lg' : '' }}">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">
                        @if($modalType === 'category')
                            {{ $isEditing ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
                        @elseif($modalType === 'product')
                            {{ $isEditing ? 'Edit Produk' : 'Tambah Produk Baru' }}
                        @endif
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body {{ $modalType === 'product' ? 'p-4' : '' }}">
                    <form wire:submit.prevent="save">

                        @if($modalType === 'category')
                            <div class="mb-3">
                                <label class="form-label small text-muted fw-bold">Nama Kategori</label>
                                <input type="text" class="form-control rounded-pill {{ $errors->has('categoryName') ? 'is-invalid' : '' }}"
                                       wire:model="categoryName" placeholder="Contoh: Makanan Utama">
                                @error('categoryName') <span class="invalid-feedback ps-2">{{ $message }}</span> @enderror
                            </div>
                        @elseif($modalType === 'product')
                            <div class="row g-4">
                                <!-- Left: Image -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label small text-muted fw-bold">Foto Produk</label>
                                        <div class="ratio ratio-1x1 bg-light rounded-4 overflow-hidden position-relative border border-dashed text-center d-flex align-items-center justify-content-center">
                                            @if ($productImage)
                                                <img src="{{ $productImage->temporaryUrl() }}" class="object-fit-cover w-100 h-100">
                                            @elseif($existingProductImage)
                                                <img src="{{ asset('storage/' . $existingProductImage) }}" class="object-fit-cover w-100 h-100">
                                            @else
                                                <div class="d-flex flex-column align-items-center justify-content-center h-100 w-100 text-muted">
                                                    <i class="bi bi-cloud-arrow-up fs-1 mb-2"></i>
                                                    <small>Upload Foto</small>
                                                </div>
                                            @endif
                                            <input type="file" wire:model="productImage" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer">
                                        </div>
                                        @error('productImage') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <!-- Right: Details -->
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label small text-muted fw-bold">Nama Produk</label>
                                        <input type="text" class="form-control rounded-pill {{ $errors->has('productName') ? 'is-invalid' : '' }}"
                                               wire:model="productName" placeholder="Contoh: Nasi Goreng Spesial">
                                        @error('productName') <span class="invalid-feedback ps-2">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted fw-bold">Harga (Rp)</label>
                                            <input type="number" class="form-control rounded-pill {{ $errors->has('productPrice') ? 'is-invalid' : '' }}"
                                                   wire:model="productPrice" placeholder="0">
                                            @error('productPrice') <span class="invalid-feedback ps-2">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted fw-bold">Kategori</label>
                                            <select class="form-select rounded-pill" wire:model="productCategoryId">
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('productCategoryId') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-12">
                                            <label class="form-label small text-muted fw-bold">Tipe Produk</label>
                                            <select class="form-select rounded-pill" wire:model="productType">
                                                <option value="single">Single (Satu Pilihan)</option>
                                                <option value="multi">Multi (Banyak Pilihan)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small text-muted fw-bold">Deskripsi</label>
                                        <textarea class="form-control rounded-4" rows="3" wire:model="productDescription" placeholder="Jelaskan detail produk..."></textarea>
                                    </div>

                                    <!-- Options Section -->
                                    <div class="mb-3">
                                        <label class="form-label small text-muted fw-bold">Varian / Opsi</label>
                                        @foreach($productOptions as $index => $option)
                                            <div class="input-group mb-2">
                                                <input type="text" class="form-control rounded-start-pill" wire:model="productOptions.{{ $index }}.name" placeholder="Nama Varian">
                                                <button class="btn btn-outline-danger rounded-end-pill" type="button" wire:click="removeOption({{ $index }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                            @error('productOptions.'.$index.'.name') <span class="text-danger small">{{ $message }}</span> @enderror
                                        @endforeach
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" wire:click="addOption">
                                            <i class="bi bi-plus-lg me-1"></i> Tambah Varian
                                        </button>
                                    </div>

                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" id="availabilitySwitch" wire:model="productIsAvailable">
                                        <label class="form-check-label" for="availabilitySwitch">Produk Tersedia</label>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-light rounded-pill px-4" wire:click="closeModal">Batal</button>
                            <button type="submit" class="btn btn-brand rounded-pill px-4">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
