<?php

namespace App\Livewire\Menu;

use App\Models\Category;
use App\Models\Product;
use App\Traits\CompressesImages;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class MenuModal extends Component
{
    use WithFileUploads, CompressesImages;

    public $showModal = false;
    public $modalType = ''; // 'category' or 'product'
    public $isEditing = false;

    // Category Form
    public $categoryId;
    public $categoryName;

    // Product Form
    public $productId;
    public $productName;
    public $productDescription;
    public $productPrice;
    public $productImage;
    public $existingProductImage;
    public $productCategoryId;
    public $productIsAvailable = true;
    public $productType = 'single';
    public $productOptions = [];

    // Data for dropdowns
    public $categories = [];

    #[On('open-menu-modal')]
    public function openModal($type, $mode, $id = null, $categoryId = null)
    {
        $this->resetValidation();
        $this->resetExcept('categories'); // Keep categories if loaded? No, better reload to be fresh.
        $this->reset();

        $this->modalType = $type;
        $this->isEditing = ($mode === 'edit');
        $this->showModal = true;

        // Load categories for product dropdown
        if ($type === 'product') {
             $this->categories = Category::where('restaurant_id', Auth::user()->restaurant->id)
                ->orderBy('order_column')
                ->get();
        }

        if ($type === 'category') {
            if ($this->isEditing && $id) {
                $category = Category::find($id);
                if ($category) {
                    $this->categoryId = $id;
                    $this->categoryName = $category->name;
                }
            }
        } elseif ($type === 'product') {
            if ($this->isEditing && $id) {
                $product = Product::with('options')->find($id);
                if ($product) {
                    $this->productId = $id;
                    $this->productName = $product->name;
                    $this->productDescription = $product->description;
                    $this->productPrice = $product->price;
                    $this->existingProductImage = $product->image;
                    $this->productCategoryId = $product->category_id;
                    $this->productIsAvailable = $product->is_available;
                    $this->productType = $product->type;
                    $this->productOptions = $product->options->toArray();
                }
            } else {
                $this->productCategoryId = $categoryId;
                // Set default values for new product
                $this->productIsAvailable = true;
                $this->productType = 'single';
                $this->productOptions = [];
            }
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset();
    }

    public function save()
    {
        if ($this->modalType === 'category') {
            $this->saveCategory();
        } elseif ($this->modalType === 'product') {
            $this->saveProduct();
        }
    }

    public function saveCategory()
    {
        $this->validate([
            'categoryName' => 'required|string|max:255'
        ]);

        $user = Auth::user();

        if ($this->isEditing) {
            $category = Category::find($this->categoryId);
            $category->update(['name' => $this->categoryName]);
        } else {
            $user->restaurant->categories()->create([
                'name' => $this->categoryName,
                'order_column' => Category::where('restaurant_id', $user->restaurant->id)->max('order_column') + 1
            ]);
        }

        $this->closeModal();
        $this->dispatch('menu-updated');
    }

    public function saveProduct()
    {
        $this->validate([
            'productName' => 'required|string|max:255',
            'productPrice' => 'required|numeric|min:0',
            'productCategoryId' => 'required|exists:categories,id',
            'productType' => 'required|in:single,multi',
            'productOptions.*.name' => 'required|string|max:255',
            'productImage' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        $imagePath = $this->existingProductImage;

        if ($this->productImage) {
            if ($this->existingProductImage) {
                Storage::disk('public')->delete($this->existingProductImage);
            }
            $imagePath = $this->compressAndStore($this->productImage, 'products/' . $user->restaurant->id);
        }

        $data = [
            'name' => $this->productName,
            'description' => $this->productDescription,
            'price' => $this->productPrice,
            'category_id' => $this->productCategoryId,
            'image' => $imagePath,
            'is_available' => $this->productIsAvailable,
            'type' => $this->productType,
        ];

        if ($this->isEditing) {
            $product = Product::find($this->productId);
            $product->update($data);
        } else {
            $data['restaurant_id'] = $user->restaurant->id;
            $data['order_column'] = Product::where('category_id', $this->productCategoryId)->max('order_column') + 1;
            $product = Product::create($data);
        }

        $product->options()->delete();
        if (!empty($this->productOptions)) {
            foreach ($this->productOptions as $option) {
                $product->options()->create(['name' => $option['name']]);
            }
        }

        $this->closeModal();
        $this->dispatch('menu-updated');
    }

    public function addOption()
    {
        $this->productOptions[] = ['name' => ''];
    }

    public function removeOption($index)
    {
        unset($this->productOptions[$index]);
        $this->productOptions = array_values($this->productOptions);
    }

    public function render()
    {
        return view('livewire.menu.menu-modal');
    }
}
