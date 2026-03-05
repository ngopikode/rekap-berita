<div>
    <!-- 1. Header & Welcome -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4 mb-md-5 gap-3">
        <div>
            <h6 class="text-brand text-uppercase small fw-bold mb-2" style="letter-spacing: 1.5px; opacity: 0.9;">
                Selamat Datang, {{ $user->name }}
            </h6>
            <h2 class="fw-bold font-serif text-dark mb-1 tracking-tight">{{ $restaurant->name ?? 'Nama Restoran' }}</h2>
            <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                @if($restaurant)
                    <span
                        class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-3">
                        <i class="bi bi-circle-fill me-1" style="font-size: 6px; vertical-align: middle;"></i> Aktif
                    </span>
                    <a href="https://{{ $restaurant->subdomain }}.{{ config('app.frontend_url_base') }}" target="_blank"
                       class="text-muted text-decoration-none small hover-text-brand transition-all text-truncate"
                       style="max-width: 200px;">
                        <i class="bi bi-box-arrow-up-right me-1"></i>
                        {{ $restaurant->subdomain }}.{{ config('app.frontend_url_base') }}
                    </a>
                @else
                    <span
                        class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle rounded-pill px-3">Perlu Setup</span>
                @endif
            </div>
        </div>
        <div class="w-100 w-md-auto">
            <a href="{{ route('orders.index', ['subdomain' => request()->route('subdomain')]) }}" wire:navigate
               class="btn btn-brand rounded-pill px-4 py-2 shadow-sm w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-receipt"></i>
                <span>Lihat Pesanan</span>
            </a>
        </div>
    </div>

    @if(!$restaurant)
        <div class="alert alert-warning border border-warning-subtle shadow-sm rounded-4 p-4 mb-5" role="alert"
             style="background: #fffbeb;">
            <div class="d-flex flex-column flex-sm-row align-items-start gap-3">
                <div
                    class="bg-warning text-white rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0">
                    <i class="bi bi-exclamation-lg fs-5"></i>
                </div>
                <div class="w-100">
                    <h5 class="alert-heading fw-bold mb-1 font-serif">Setup Restoran Belum Lengkap</h5>
                    <p class="mb-3 small opacity-75">Informasi restoran Anda belum lengkap. Sistem tidak dapat menerima
                        pesanan sampai profil diatur.</p>
                    <a href="{{ route('settings.index', ['subdomain' => request()->route('subdomain')]) }}"
                       class="btn btn-dark btn-sm rounded-pill px-4 w-100 w-sm-auto">
                        Atur Restoran Sekarang
                    </a>
                </div>
            </div>
        </div>
    @else

        <!-- 2. Stats Cards (Responsive Grid: 1 col mobile, 2 col tablet, 3 col desktop) -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-3 g-md-4 mb-5">
            <!-- Orders Today -->
            <div class="col">
                <div class="card h-100 shadow-sm overflow-hidden hover-lift transition-all">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <!-- Menggunakan bg-primary (Brand Color) dengan opacity rendah -->
                            <div
                                class="bg-primary bg-opacity-10 text-brand rounded-3 p-3 d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="bi bi-bag-check-fill fs-5"></i>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill">Hari Ini</span>
                        </div>
                        <div>
                            <h3 class="fw-bold text-dark mb-1 font-serif">{{ $stats['orders_today'] }}</h3>
                            <p class="text-muted small fw-medium mb-0">Total Pesanan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Today -->
            <div class="col">
                <div class="card h-100 shadow-sm overflow-hidden hover-lift transition-all">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div
                                class="bg-success bg-opacity-10 text-success rounded-3 p-3 d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="bi bi-currency-dollar fs-5"></i>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill">Hari Ini</span>
                        </div>
                        <div>
                            <h3 class="fw-bold text-dark mb-1 font-serif">
                                Rp {{ number_format($stats['revenue_today'], 0, ',', '.') }}
                            </h3>
                            <p class="text-muted small fw-medium mb-0">Pendapatan Bersih</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Orders -->
            <div class="col d-sm-block d-xl-block"> <!-- Adjust visibility logic if needed, currently shows on all -->
                <div class="card h-100 shadow-sm overflow-hidden hover-lift transition-all position-relative">
                    @if($stats['pending_orders'] > 0)
                        <div class="position-absolute top-0 end-0 p-3">
                            <span class="d-flex h-3 w-3">
                              <span
                                  class="animate-ping absolute inline-flex h-full w-full rounded-full bg-danger opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-danger"></span>
                            </span>
                        </div>
                    @endif
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div
                                class="bg-warning bg-opacity-10 text-warning rounded-3 p-3 d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="bi bi-hourglass-split fs-5"></i>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill">Perlu Tindakan</span>
                        </div>
                        <div>
                            <h3 class="fw-bold text-{{ $stats['pending_orders'] > 0 ? 'dark' : 'muted' }} mb-1 font-serif">
                                {{ $stats['pending_orders'] }}
                            </h3>
                            <p class="text-muted small fw-medium mb-0">Pesanan Menunggu</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Recent Orders & Quick Actions -->
        <div class="row g-4">
            <!-- Recent Orders -->
            <div class="col-lg-8">
                <div class="card shadow-sm h-100">
                    <div
                        class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold text-dark mb-0 font-serif">Pesanan Terbaru</h6>
                        <a href="{{ route('orders.index', ['subdomain' => request()->route('subdomain')]) }}"
                           class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            Lihat Semua
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($recentOrders as $order)
                                <div class="list-group-item px-4 py-3 hover-bg-light transition-all cursor-pointer">
                                    <!-- Mobile: Stack vertically, Desktop: Horizontal -->
                                    <div
                                        class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">

                                        <!-- Left Side: Icon & Name -->
                                        <div class="d-flex align-items-center gap-3 w-100">
                                            <div
                                                class="bg-light text-muted rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                                style="width: 40px; height: 40px;">
                                                <i class="bi bi-receipt"></i>
                                            </div>
                                            <div class="overflow-hidden">
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <h6 class="fw-bold text-dark mb-0 text-truncate">
                                                        {{ $order->customer_name }}
                                                    </h6>
                                                    <span
                                                        class="badge bg-light text-secondary border rounded-pill small flex-shrink-0">#{{ $order->id }}</span>
                                                </div>
                                                <div
                                                    class="text-muted small d-flex align-items-center gap-2 text-nowrap">
                                                    <span>{{ $order->order_type }}</span>
                                                    <span class="text-light-emphasis">&bull;</span>
                                                    <span>{{ $order->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Right Side: Price & Status -->
                                        <div class="text-start text-sm-end w-100 w-sm-auto ps-5 ps-sm-0">
                                            <div class="fw-bold text-brand mb-1">
                                                Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                                            <div>
                                                @if($order->status == 'pending')
                                                    <span
                                                        class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle rounded-pill">Menunggu</span>
                                                @elseif($order->status == 'processing')
                                                    <span
                                                        class="badge bg-info bg-opacity-10 text-info border border-info-subtle rounded-pill">Diproses</span>
                                                @elseif($order->status == 'completed')
                                                    <span
                                                        class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill">Selesai</span>
                                                @else
                                                    <span
                                                        class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle rounded-pill">{{ $order->status }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <div class="bg-light rounded-circle d-inline-flex p-3 mb-3 text-muted">
                                        <i class="bi bi-inbox fs-3"></i>
                                    </div>
                                    <p class="text-muted mb-0">Belum ada pesanan terbaru.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Active Menu -->
            <div class="col-lg-4">
                <div class="d-flex flex-column gap-4 h-100">

                    <!-- Active Menu Card -->
                    <div class="card shadow-sm bg-primary text-white border-0 position-relative overflow-hidden">
                        <!-- Abstract bg shape -->
                        <div class="position-absolute top-0 end-0 p-4 opacity-25">
                            <i class="bi bi-journal-album" style="font-size: 5rem; transform: rotate(15deg);"></i>
                        </div>

                        <div class="card-body p-4 position-relative z-1">
                            <p class="text-white text-opacity-75 small text-uppercase fw-bold mb-1">Katalog Menu</p>
                            <h2 class="fw-bold mb-0 font-serif">{{ $stats['active_products'] }}</h2>
                            <p class="text-white text-opacity-75 mb-4">Item Aktif</p>

                            <a href="{{ route('menu.index', ['subdomain' => request()->route('subdomain')]) }}"
                               class="btn btn-sm btn-light text-brand fw-bold rounded-pill px-3 shadow-sm stretched-link">
                                Kelola Menu <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Actions List -->
                    <div class="card shadow-sm flex-grow-1">
                        <div class="card-header bg-white border-bottom py-3 px-4">
                            <h6 class="fw-bold text-dark mb-0 font-serif">Aksi Cepat</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                <a href="{{ route('menu.index', ['subdomain' => request()->route('subdomain')]) }}"
                                   class="list-group-item list-group-item-action px-4 py-3 d-flex align-items-center gap-3 border-0">
                                    <div class="bg-primary bg-opacity-10 text-brand rounded p-2 flex-shrink-0">
                                        <i class="bi bi-plus-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0 text-sm">Tambah Menu Baru</h6>
                                        <small class="text-muted">Buat item produk baru</small>
                                    </div>
                                </a>
                                <a href="{{ route('settings.index', ['subdomain' => request()->route('subdomain')]) }}"
                                   class="list-group-item list-group-item-action px-4 py-3 d-flex align-items-center gap-3 border-0">
                                    <div class="bg-info bg-opacity-10 text-info rounded p-2 flex-shrink-0">
                                        <i class="bi bi-qr-code"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0 text-sm">Kode QR</h6>
                                        <small class="text-muted">Cetak atau unduh QR</small>
                                    </div>
                                </a>
                                <a href="{{ route('settings.index', ['subdomain' => request()->route('subdomain')]) }}"
                                   class="list-group-item list-group-item-action px-4 py-3 d-flex align-items-center gap-3 border-0">
                                    <div class="bg-secondary bg-opacity-10 text-secondary rounded p-2 flex-shrink-0">
                                        <i class="bi bi-gear-fill"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0 text-sm">Pengaturan</h6>
                                        <small class="text-muted">Perbarui info restoran</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('custom-styles')
    <style>
        .tracking-tight {
            letter-spacing: -0.025em;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
            border-color: var(--brand-color) !important;
        }

        .hover-bg-light:hover {
            background-color: var(--ezmenu-hover-bg) !important;
        }

        .hover-text-brand:hover {
            color: var(--brand-color) !important;
        }

        .transition-all {
            transition: all 0.2s ease-in-out;
        }

        .cursor-pointer {
            cursor: pointer;
        }
    </style>
@endpush
