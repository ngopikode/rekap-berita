<?php

namespace App\Livewire\Menu;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

class MenuIndex extends Component
{
    public $categories;
    public $activeCategoryId = null;

    public function mount()
    {
        $this->refreshData();
    }

    #[On('menu-updated')]
    public function refreshData()
    {
        $user = Auth::user();
        $this->categories = Category::where('restaurant_id', $user->restaurant->id)
            ->with(['products' => function ($q) {
                $q->with('options')->orderBy('order_column');
            }])
            ->orderBy('order_column')
            ->get();

        if (!$this->activeCategoryId && $this->categories->isNotEmpty()) {
            $this->activeCategoryId = $this->categories->first()->id;
        }
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.menu.menu-index');
    }

    public function deleteCategory($id)
    {
        $category = Category::find($id);
        if ($category && $category->products()->count() == 0) {
            $category->delete();
            $this->refreshData();
        }
    }

    public function toggleAvailability($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->is_available = !$product->is_available;
            $product->save();
            $this->refreshData();
        }
    }

    public function deleteProduct($id)
    {
        $product = Product::find($id);
        if ($product) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->delete();
            $this->refreshData();
        }
    }
}
