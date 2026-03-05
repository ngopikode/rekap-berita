<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Dashboard extends Component
{
    #[Layout('components.layouts.app')]
    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        $stats = [
            'orders_today' => 0,
            'revenue_today' => 0,
            'active_products' => 0,
            'pending_orders' => 0,
        ];

        $recentOrders = [];

        if ($restaurant) {
            // Optimize: Use a single query for today's stats if possible, or keep separate for clarity.
            // Separate is fine for now, but let's ensure we don't over-fetch.

            $stats['orders_today'] = Order::where('restaurant_id', $restaurant->id)
                ->whereDate('created_at', today())
                ->count();

            $stats['revenue_today'] = Order::where('restaurant_id', $restaurant->id)
                ->whereDate('created_at', today())
                ->where('status', 'completed')
                ->sum('total_price');

            $stats['active_products'] = Product::where('restaurant_id', $restaurant->id)
                ->where('is_available', true)
                ->count();

            $stats['pending_orders'] = Order::where('restaurant_id', $restaurant->id)
                ->where('status', 'pending')
                ->count();

            // Optimize: Removed 'with('items')' as items are not shown in the dashboard list
            $recentOrders = Order::where('restaurant_id', $restaurant->id)
                ->latest()
                ->take(5)
                ->get();
        }

        return view('livewire.dashboard', [
            'user' => $user,
            'restaurant' => $restaurant,
            'stats' => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }
}
