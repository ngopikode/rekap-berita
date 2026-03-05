<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold font-serif text-dark mb-1">Pengaturan Restoran</h2>
            <p class="text-muted small mb-0">Konfigurasi informasi dan tampilan restoran anda.</p>
        </div>
    </div>

    @if($saved)
        <div class="alert alert-success border-0 rounded-4 shadow-sm alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Pengaturan berhasil disimpan!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Left: Tabs -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-2">
                    <nav class="nav nav-pills flex-column gap-1">
                        <button wire:click="$set('activeTab', 'general')" class="nav-link rounded-pill text-start px-4 py-3 {{ $activeTab == 'general' ? 'active bg-dark' : 'text-muted' }}">
                            <i class="bi bi-shop me-2"></i> Informasi Umum
                        </button>
                        <button wire:click="$set('activeTab', 'hero')" class="nav-link rounded-pill text-start px-4 py-3 {{ $activeTab == 'hero' ? 'active bg-dark' : 'text-muted' }}">
                            <i class="bi bi-image me-2"></i> Hero Section
                        </button>
                        <button wire:click="$set('activeTab', 'navbar')" class="nav-link rounded-pill text-start px-4 py-3 {{ $activeTab == 'navbar' ? 'active bg-dark' : 'text-muted' }}">
                            <i class="bi bi-layout-text-sidebar me-2"></i> Navbar
                        </button>
                        <button wire:click="$set('activeTab', 'seo')" class="nav-link rounded-pill text-start px-4 py-3 {{ $activeTab == 'seo' ? 'active bg-dark' : 'text-muted' }}">
                            <i class="bi bi-search me-2"></i> SEO
                        </button>
                        <button wire:click="$set('activeTab', 'qrcode')" class="nav-link rounded-pill text-start px-4 py-3 {{ $activeTab == 'qrcode' ? 'active bg-dark' : 'text-muted' }}">
                            <i class="bi bi-qr-code me-2"></i> QR Code
                        </button>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Right: Content -->
        <div class="col-md-9">
            <form wire:submit.prevent="save">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">

                        {{-- General Tab --}}
                        @if($activeTab == 'general')
                            <h5 class="fw-bold mb-4">Informasi Umum</h5>

                            <div class="row g-3">
                                <!-- Logo -->
                                <div class="col-12 mb-3">
                                    <label class="form-label small text-muted fw-bold">Logo Restoran</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle bg-light shadow-sm overflow-hidden d-flex align-items-center justify-content-center position-relative" style="width: 80px; height: 80px;">
                                            @if($logo)
                                                <img src="{{ $logo->temporaryUrl() }}" class="w-100 h-100 object-fit-cover">
                                            @elseif($existingLogo)
                                                <img src="{{ asset('storage/' . $existingLogo) }}" class="w-100 h-100 object-fit-cover">
                                            @else
                                                <i class="bi bi-camera text-muted fs-4"></i>
                                            @endif
                                            <input type="file" wire:model="logo" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer;">
                                        </div>
                                        <div>
                                            <p class="mb-0 small text-muted">Klik untuk upload logo. Max 1MB.</p>
                                        </div>
                                    </div>
                                    @error('logo') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small text-muted fw-bold">Nama Restoran</label>
                                    <input type="text" class="form-control rounded-pill" wire:model="restaurantName" placeholder="Nama restoran anda">
                                    @error('restaurantName') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small text-muted fw-bold">Subdomain</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control rounded-start-pill" wire:model="subdomain" placeholder="nama-resto">
                                        <span class="input-group-text rounded-end-pill bg-light text-muted small">.ngopikode.my.id</span>
                                    </div>
                                    @error('subdomain') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small text-muted fw-bold">Nomor WhatsApp</label>
                                    <input type="text" class="form-control rounded-pill" wire:model="whatsappNumber" placeholder="628xxxxxxxxx">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small text-muted fw-bold">Warna Tema</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" class="form-control form-control-color rounded-circle border-0 shadow-sm" wire:model="themeColor" style="width: 40px; height: 40px;">
                                        <input type="text" class="form-control rounded-pill" wire:model="themeColor" placeholder="#F50057" style="max-width: 120px;">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label small text-muted fw-bold">Alamat</label>
                                    <textarea class="form-control rounded-4" rows="2" wire:model="address" placeholder="Alamat lengkap restoran..."></textarea>
                                </div>

                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="activeSwitch" wire:model="isActive">
                                        <label class="form-check-label" for="activeSwitch">Restoran Aktif (terlihat di client)</label>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Hero Tab --}}
                        @if($activeTab == 'hero')
                            <h5 class="fw-bold mb-4">Hero Section</h5>
                            <p class="text-muted small mb-4">Konfigurasi tampilan bagian utama halaman menu client anda.</p>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small text-muted fw-bold">Promo Text</label>
                                    <input type="text" class="form-control rounded-pill" wire:model="heroPromoText" placeholder="Contoh: Diskon 20%!">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted fw-bold">Status Text</label>
                                    <input type="text" class="form-control rounded-pill" wire:model="heroStatusText" placeholder="Contoh: Buka Sekarang">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small text-muted fw-bold">Headline</label>
                                    <input type="text" class="form-control rounded-pill" wire:model="heroHeadline" placeholder="Contoh: Lezatnya Masakan Nusantara">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small text-muted fw-bold">Tagline</label>
                                    <input type="text" class="form-control rounded-pill" wire:model="heroTagline" placeholder="Contoh: Cita rasa autentik untuk semua">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small text-muted fw-bold">Instagram URL</label>
                                    <input type="url" class="form-control rounded-pill" wire:model="heroInstagramUrl" placeholder="https://instagram.com/namaresto">
                                </div>
                            </div>
                        @endif

                        {{-- Navbar Tab --}}
                        @if($activeTab == 'navbar')
                            <h5 class="fw-bold mb-4">Pengaturan Navbar</h5>
                            <p class="text-muted small mb-4">Atur teks yang muncul di navigasi halaman client.</p>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small text-muted fw-bold">Brand Text</label>
                                    <input type="text" class="form-control rounded-pill" wire:model="navbarBrandText" placeholder="Contoh: EZ">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted fw-bold">Title</label>
                                    <input type="text" class="form-control rounded-pill" wire:model="navbarTitle" placeholder="Contoh: Warung Makan XYZ">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted fw-bold">Subtitle</label>
                                    <input type="text" class="form-control rounded-pill" wire:model="navbarSubtitle" placeholder="Contoh: Delivery & Dine-In">
                                </div>
                            </div>
                        @endif

                        {{-- SEO Tab --}}
                        @if($activeTab == 'seo')
                            <h5 class="fw-bold mb-4">SEO & Open Graph</h5>
                            <p class="text-muted small mb-4">Optimasi tampilan link saat dibagikan di media sosial.</p>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small text-muted fw-bold">SEO Title</label>
                                    <input type="text" class="form-control rounded-pill" wire:model="seoTitle" placeholder="Judul untuk mesin pencari">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted fw-bold">Keywords</label>
                                    <input type="text" class="form-control rounded-pill" wire:model="seoKeywords" placeholder="restoran, makanan, delivery">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small text-muted fw-bold">SEO Description</label>
                                    <textarea class="form-control rounded-4" rows="2" wire:model="seoDescription" placeholder="Deskripsi singkat restoran anda..."></textarea>
                                </div>

                                <hr class="my-3">

                                <div class="col-md-6">
                                    <label class="form-label small text-muted fw-bold">OG Title</label>
                                    <input type="text" class="form-control rounded-pill" wire:model="ogTitle" placeholder="Judul untuk social media share">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted fw-bold">OG Image</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-4 bg-light shadow-sm overflow-hidden position-relative" style="width: 100px; height: 60px;">
                                            @if($ogImage)
                                                <img src="{{ $ogImage->temporaryUrl() }}" class="w-100 h-100 object-fit-cover">
                                            @elseif($existingOgImage)
                                                <img src="{{ asset('storage/' . $existingOgImage) }}" class="w-100 h-100 object-fit-cover">
                                            @else
                                                <div class="d-flex align-items-center justify-content-center h-100 text-muted"><i class="bi bi-image"></i></div>
                                            @endif
                                            <input type="file" wire:model="ogImage" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer;">
                                        </div>
                                        <small class="text-muted">Max 1MB. Rekomendasi 1200x630px.</small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small text-muted fw-bold">OG Description</label>
                                    <textarea class="form-control rounded-4" rows="2" wire:model="ogDescription" placeholder="Deskripsi untuk preview di social media..."></textarea>
                                </div>
                            </div>
                        @endif

                        {{-- QR Code Tab --}}
                        @if($activeTab == 'qrcode')
                            <h5 class="fw-bold mb-4">QR Code Menu</h5>
                            <p class="text-muted small mb-4">Cetak QR Code ini dan letakkan di meja restoran anda.</p>

                            @if($hasRestaurant && $subdomain)
                                <div class="text-center py-4">
                                    <div id="qrcode-container" class="d-inline-block p-4 bg-white rounded-4 shadow-sm border">
                                        {{-- QR Code will be generated here --}}
                                    </div>
                                    <p class="text-muted small mt-3">
                                        <i class="bi bi-link-45deg me-1"></i>
                                        <a href="https://{{ $subdomain }}.ngopikode.my.id" target="_blank" class="text-decoration-none">
                                            https://{{ $subdomain }}.ngopikode.my.id
                                        </a>
                                    </p>

                                    <button onclick="downloadQR()" class="btn btn-brand rounded-pill px-4 mt-2 shadow-sm">
                                        <i class="bi bi-download me-2"></i> Download QR Code
                                    </button>
                                </div>

                                {{-- Load QR Code Library --}}
                                <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
                                <script>
                                    function generateQR() {
                                        const container = document.getElementById('qrcode-container');
                                        if (container) {
                                            container.innerHTML = ''; // Clear previous
                                            new QRCode(container, {
                                                text: "https://{{ $subdomain }}.ngopikode.my.id",
                                                width: 250,
                                                height: 250,
                                                colorDark : "#000000",
                                                colorLight : "#ffffff",
                                                correctLevel : QRCode.CorrectLevel.H
                                            });
                                        }
                                    }

                                    function downloadQR() {
                                        const img = document.querySelector('#qrcode-container img');
                                        if (img) {
                                            const link = document.createElement('a');
                                            link.download = 'qrcode-{{ $subdomain }}.png';
                                            link.href = img.src;
                                            document.body.appendChild(link);
                                            link.click();
                                            document.body.removeChild(link);
                                        }
                                    }

                                    // Run immediately
                                    setTimeout(generateQR, 100);
                                </script>
                            @else
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-qr-code" style="font-size: 3rem;"></i>
                                    <p class="mt-3">Simpan pengaturan restoran terlebih dahulu untuk generate QR Code.</p>
                                </div>
                            @endif
                        @endif

                    </div>

                    @if($activeTab != 'qrcode')
                    <div class="card-footer bg-transparent border-0 px-4 pb-4 pt-2">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-brand rounded-pill px-5 shadow-sm">
                                <i class="bi bi-check2 me-2"></i> Simpan Pengaturan
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
