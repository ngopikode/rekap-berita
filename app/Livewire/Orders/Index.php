<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $statusFilter = 'all';
    public $search = '';

    // Detail Modal
    public $showDetailModal = false;
    public $selectedOrder = null;

    protected $queryString = ['statusFilter', 'search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function filterByStatus($status)
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function viewOrder($orderId)
    {
        $this->selectedOrder = Order::with('items.product')->find($orderId);
        $this->showDetailModal = true;
    }

    public function updateStatus($orderId, $newStatus)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->status = $newStatus;
            $order->save();
        }

        // Close modal if open
        if ($this->selectedOrder && $this->selectedOrder->id == $orderId) {
            $this->selectedOrder = Order::with('items.product')->find($orderId);
        }
    }

    public function render()
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        $query = Order::where('restaurant_id', $restaurant->id ?? 0)
            ->with('items');

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Apply search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('customer_name', 'like', '%' . $this->search . '%')
                    ->orWhere('id', 'like', '%' . $this->search . '%');
            });
        }

        $orders = $query->latest()->paginate(15);

        // Stats for tabs
        $allCount = Order::where('restaurant_id', $restaurant->id ?? 0)->count();
        $pendingCount = Order::where('restaurant_id', $restaurant->id ?? 0)->where('status', 'pending')->count();
        $processingCount = Order::where('restaurant_id', $restaurant->id ?? 0)->where('status', 'processing')->count();
        $completedCount = Order::where('restaurant_id', $restaurant->id ?? 0)->where('status', 'completed')->count();

        return view('livewire.orders.index', [
            'orders' => $orders,
            'allCount' => $allCount,
            'pendingCount' => $pendingCount,
            'processingCount' => $processingCount,
            'completedCount' => $completedCount,
        ]);
    }
}
