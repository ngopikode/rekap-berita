<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold font-serif text-dark mb-1">Pesanan Masuk</h2>
            <p class="text-muted small mb-0">Kelola dan proses pesanan dari pelanggan.</p>
        </div>
    </div>

    <!-- Status Filter Tabs -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-0">
            <ul class="nav nav-pills gap-2 p-3">
                <li class="nav-item">
                    <button wire:click="filterByStatus('all')" class="nav-link rounded-pill px-4 {{ $statusFilter == 'all' ? 'active bg-dark' : 'text-muted' }}">
                        Semua <span class="badge bg-light text-dark ms-1 rounded-pill">{{ $allCount }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button wire:click="filterByStatus('pending')" class="nav-link rounded-pill px-4 {{ $statusFilter == 'pending' ? 'active bg-warning text-dark' : 'text-muted' }}">
                        <i class="bi bi-clock me-1"></i> Menunggu <span class="badge bg-warning text-dark ms-1 rounded-pill">{{ $pendingCount }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button wire:click="filterByStatus('processing')" class="nav-link rounded-pill px-4 {{ $statusFilter == 'processing' ? 'active bg-info text-white' : 'text-muted' }}">
                        <i class="bi bi-arrow-repeat me-1"></i> Diproses <span class="badge bg-info text-white ms-1 rounded-pill">{{ $processingCount }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button wire:click="filterByStatus('completed')" class="nav-link rounded-pill px-4 {{ $statusFilter == 'completed' ? 'active bg-success text-white' : 'text-muted' }}">
                        <i class="bi bi-check-circle me-1"></i> Selesai <span class="badge bg-success text-white ms-1 rounded-pill">{{ $completedCount }}</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-4">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0 rounded-start-pill"><i class="bi bi-search text-muted"></i></span>
            <input type="text" class="form-control border-start-0 rounded-end-pill" 
                   wire:model.live.debounce.300ms="search" placeholder="Cari berdasarkan nama pelanggan atau ID...">
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card border-0 shadow-sm rounded-4" wire:poll.15s>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="border-0 ps-4 py-3">ID</th>
                            <th class="border-0 py-3">Pelanggan</th>
                            <th class="border-0 py-3">Item</th>
                            <th class="border-0 py-3">Total</th>
                            <th class="border-0 py-3">Status</th>
                            <th class="border-0 py-3">Waktu</th>
                            <th class="border-0 pe-4 py-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td class="ps-4 fw-bold">#{{ $order->id }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">{{ $order->customer_name }}</span>
                                    <span class="small text-muted">{{ $order->order_type }} {{ $order->order_info ? '• ' . $order->order_info : '' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark rounded-pill">{{ $order->items->count() }} item</span>
                            </td>
                            <td class="fw-bold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            <td>
                                @if($order->status == 'pending')
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                        <i class="bi bi-clock me-1"></i> Menunggu
                                    </span>
                                @elseif($order->status == 'processing')
                                    <span class="badge bg-info text-white rounded-pill px-3 py-2">
                                        <i class="bi bi-arrow-repeat me-1"></i> Diproses
                                    </span>
                                @elseif($order->status == 'completed')
                                    <span class="badge bg-success text-white rounded-pill px-3 py-2">
                                        <i class="bi bi-check-circle me-1"></i> Selesai
                                    </span>
                                @elseif($order->status == 'cancelled')
                                    <span class="badge bg-danger text-white rounded-pill px-3 py-2">
                                        <i class="bi bi-x-circle me-1"></i> Dibatalkan
                                    </span>
                                @else
                                    <span class="badge bg-secondary text-white rounded-pill px-3 py-2">{{ $order->status }}</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $order->created_at->diffForHumans() }}</td>
                            <td class="pe-4 text-end">
                                <div class="btn-group">
                                    <button wire:click="viewOrder({{ $order->id }})" class="btn btn-sm btn-light rounded-pill border px-3">
                                        <i class="bi bi-eye me-1"></i> Detail
                                    </button>
                                    @if($order->status == 'pending')
                                        <button wire:click="updateStatus({{ $order->id }}, 'processing')" class="btn btn-sm btn-info text-white rounded-pill px-3 ms-1">
                                            <i class="bi bi-play-fill me-1"></i> Proses
                                        </button>
                                    @elseif($order->status == 'processing')
                                        <button wire:click="updateStatus({{ $order->id }}, 'completed')" class="btn btn-sm btn-success text-white rounded-pill px-3 ms-1">
                                            <i class="bi bi-check2 me-1"></i> Selesai
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="mb-3">
                                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                </div>
                                <p class="text-muted mb-0">Belum ada pesanan{{ $statusFilter != 'all' ? ' dengan status ini' : '' }}.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $orders->links() }}
    </div>

    <!-- Order Detail Modal -->
    @if($showDetailModal && $selectedOrder)
    <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        Detail Pesanan #{{ $selectedOrder->id }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showDetailModal', false)"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Customer Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small text-uppercase fw-bold d-block">Pelanggan</label>
                                <span class="fw-bold text-dark fs-5">{{ $selectedOrder->customer_name }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="text-muted small text-uppercase fw-bold d-block">Tipe</label>
                                <span class="fw-bold text-dark">{{ $selectedOrder->order_type }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="text-muted small text-uppercase fw-bold d-block">Status</label>
                                @if($selectedOrder->status == 'pending')
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Menunggu</span>
                                @elseif($selectedOrder->status == 'processing')
                                    <span class="badge bg-info text-white rounded-pill px-3 py-2">Diproses</span>
                                @elseif($selectedOrder->status == 'completed')
                                    <span class="badge bg-success text-white rounded-pill px-3 py-2">Selesai</span>
                                @elseif($selectedOrder->status == 'cancelled')
                                    <span class="badge bg-danger text-white rounded-pill px-3 py-2">Dibatalkan</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($selectedOrder->order_info)
                    <div class="alert alert-light border rounded-4 mb-4">
                        <i class="bi bi-info-circle me-2 text-primary"></i>
                        <strong>Info:</strong> {{ $selectedOrder->order_info }}
                    </div>
                    @endif

                    <!-- Order Items -->
                    <h6 class="fw-bold text-dark mb-3">Item Pesanan</h6>
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="border-0 rounded-start ps-3 py-3">Produk</th>
                                    <th class="border-0 py-3 text-center">Qty</th>
                                    <th class="border-0 py-3 text-end">Harga</th>
                                    <th class="border-0 rounded-end pe-3 py-3 text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($selectedOrder->items as $item)
                                <tr>
                                    <td class="ps-3">
                                        <span class="fw-bold text-dark">{{ $item->product_name }}</span>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}x</td>
                                    <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="text-end pe-3 fw-bold">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-top">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold ps-3 pt-3">Total</td>
                                    <td class="text-end fw-bold pe-3 pt-3 text-brand fs-5">Rp {{ number_format($selectedOrder->total_price, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="text-muted small mt-2">
                        <i class="bi bi-clock me-1"></i> Dipesan pada {{ $selectedOrder->created_at->format('d M Y, H:i') }}
                        @if($selectedOrder->source)
                        • <i class="bi bi-globe me-1"></i> Sumber: {{ $selectedOrder->source }}
                        @endif
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    @if($selectedOrder->status == 'pending')
                        <button wire:click="updateStatus({{ $selectedOrder->id }}, 'cancelled')" class="btn btn-outline-danger rounded-pill px-4">
                            <i class="bi bi-x-circle me-1"></i> Tolak
                        </button>
                        <button wire:click="updateStatus({{ $selectedOrder->id }}, 'processing')" class="btn btn-info text-white rounded-pill px-4">
                            <i class="bi bi-play-fill me-1"></i> Proses Pesanan
                        </button>
                    @elseif($selectedOrder->status == 'processing')
                        <button wire:click="updateStatus({{ $selectedOrder->id }}, 'completed')" class="btn btn-success text-white rounded-pill px-4">
                            <i class="bi bi-check2 me-1"></i> Tandai Selesai
                        </button>
                    @endif
                    <button type="button" class="btn btn-light rounded-pill px-4" wire:click="$set('showDetailModal', false)">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
