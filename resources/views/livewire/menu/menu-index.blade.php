<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold font-serif text-dark mb-1">Daftar Menu</h2>
            <p class="text-muted small mb-0">Kelola kategori dan produk menu restoran anda.</p>
        </div>

        <button wire:click="$dispatch('open-menu-modal', { type: 'category', mode: 'create' })" class="btn btn-brand rounded-pill px-4 shadow-sm">
            <i class="bi bi-folder-plus me-2"></i> Tambah Kategori
        </button>
    </div>

    <!-- Categories & Products List -->
    <div class="row">
        <div class="col-12">
            @if($categories->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-journal-x text-muted" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="text-muted">Belum ada kategori menu.</h5>
                    <p class="text-muted small">Buat kategori pertama anda untuk mulai menambahkan produk.</p>
                </div>
            @else
                <div class="accordion" id="menuAccordion">
                    @foreach($categories as $category)
                        <div class="accordion-item border-0 shadow-sm rounded-4 mb-3 overflow-hidden">
                            <h2 class="accordion-header" id="heading{{ $category->id }}">
                                <button class="accordion-button {{ $activeCategoryId == $category->id ? '' : 'collapsed' }} bg-white px-4 py-3 fw-bold text-dark shadow-none"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $category->id }}"
                                        aria-expanded="{{ $activeCategoryId == $category->id ? 'true' : 'false' }}"
                                        aria-controls="collapse{{ $category->id }}">
                                    <span class="me-auto">{{ $category->name }} <span class="badge bg-light text-muted ms-2 rounded-pill">{{ $category->products->count() }} Item</span></span>
                                </button>
                            </h2>
                            <div id="collapse{{ $category->id }}"
                                 class="accordion-collapse collapse {{ $activeCategoryId == $category->id ? 'show' : '' }}"
                                 aria-labelledby="heading{{ $category->id }}"
                                 data-bs-parent="#menuAccordion">
                                <div class="accordion-body bg-light p-4">

                                    <!-- Category Actions -->
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div class="btn-group">
                                            <button wire:click="$dispatch('open-menu-modal', { type: 'category', mode: 'edit', id: {{ $category->id }} })" class="btn btn-sm btn-outline-secondary rounded-pill me-2 px-3">
                                                <i class="bi bi-pencil me-1"></i> Edit Kategori
                                            </button>
                                            @if($category->products->count() == 0)
                                            <button wire:click="deleteCategory({{ $category->id }})"
                                                    wire:confirm="Yakin ingin menghapus kategori ini?"
                                                    class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                <i class="bi bi-trash me-1"></i> Hapus
                                            </button>
                                            @endif
                                        </div>
                                        <button wire:click="$dispatch('open-menu-modal', { type: 'product', mode: 'create', categoryId: {{ $category->id }} })" class="btn btn-sm btn-dark rounded-pill px-3 shadow-sm">
                                            <i class="bi bi-plus-lg me-1"></i> Tambah Produk
                                        </button>
                                    </div>

                                    <!-- Products Grid -->
                                    <div class="row g-3">
                                        @forelse($category->products as $product)
                                            <div class="col-md-6 col-lg-4 col-xl-3">
                                                <div class="card h-100 border-0 shadow-sm rounded-4 position-relative overflow-hidden {{ !$product->is_available ? 'opacity-75' : '' }}">
                                                    <!-- Image -->
                                                    <div class="ratio ratio-4x3 bg-secondary bg-opacity-10">
                                                        @if($product->image)
                                                            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top object-fit-cover" alt="{{ $product->name }}">
                                                        @else
                                                            <div class="d-flex align-items-center justify-content-center text-muted">
                                                                <i class="bi bi-image fs-1"></i>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Status Badge -->
                                                    <div class="position-absolute top-0 end-0 p-2">
                                                        <span class="badge {{ $product->is_available ? 'bg-success' : 'bg-secondary' }} rounded-pill shadow-sm">
                                                            {{ $product->is_available ? 'Tersedia' : 'Habis' }}
                                                        </span>
                                                    </div>

                                                    <div class="card-body p-3 d-flex flex-column">
                                                        <h6 class="fw-bold text-dark mb-1 text-truncate">{{ $product->name }}</h6>
                                                        <p class="text-brand fw-bold mb-2">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                                        <p class="small text-muted mb-2">{{ $product->type }}</p>

                                                        <div class="mt-auto d-flex gap-2">
                                                            <button wire:click="$dispatch('open-menu-modal', { type: 'product', mode: 'edit', id: {{ $product->id }} })" class="btn btn-sm btn-light flex-grow-1 rounded-pill border">
                                                                Edit
                                                            </button>
                                                            <button wire:click="toggleAvailability({{ $product->id }})" class="btn btn-sm btn-light rounded-circle border" title="Toggle Status">
                                                                <i class="bi {{ $product->is_available ? 'bi-toggle-on text-success' : 'bi-toggle-off text-muted' }} fs-5"></i>
                                                            </button>
                                                            <button wire:click="deleteProduct({{ $product->id }})"
                                                                    wire:confirm="Hapus produk ini?"
                                                                    class="btn btn-sm btn-light rounded-circle border text-danger" title="Hapus">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12 text-center py-4">
                                                <p class="text-muted small mb-0">Belum ada produk di kategori ini.</p>
                                            </div>
                                        @endforelse
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <livewire:menu.menu-modal />
</div>
