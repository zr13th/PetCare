<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductVariant;
use Livewire\Component;
use Livewire\Attributes\Computed;

class ProductDetail extends Component
{
    public $product;
    public $selectedAttributes = [];
    public $quantity = 1;
    public function mount($slug)
    {
        $this->product = Product::with([
            'variants.attributeValues.attribute', 
            'variants.inventories'
        ])->where('slug', $slug)->firstOrFail();

        // Khởi tạo: Chọn các giá trị của variant đầu tiên
        $firstVariant = $this->product->variants->first();
        if ($firstVariant) {
            foreach ($firstVariant->attributeValues as $av) {
                $this->selectedAttributes[$av->attribute_id] = $av->id;
            }
        }
    }

    #[Computed]
    public function currentVariant()
    {
        // Tìm variant có tập hợp attribute_value_id khớp hoàn toàn với selectedAttributes
        return $this->product->variants->first(function ($variant) {
            $variantValueIds = $variant->attributeValues->pluck('id')->toArray();
            return empty(array_diff($this->selectedAttributes, $variantValueIds)) && 
                   empty(array_diff($variantValueIds, $this->selectedAttributes));
        });
    }

    public function selectValue($attributeId, $valueId)
    {
        $this->selectedAttributes[$attributeId] = $valueId;
        $this->quantity = 1;
    }

    #[Computed]
    public function stock()
    {
        // Nếu chưa tìm thấy biến thể phù hợp, coi như không có hàng
        if (!$this->currentVariant) {
            return 0;
        }

        // Truy cập vào quan hệ inventories và sum cột quantity
        // Giả sử ProductVariant hasMany Inventory
        return $this->currentVariant->inventories->sum('quantity');
    }

    public function increment()
    {
        // Kiểm tra nếu quantity nhỏ hơn tổng tồn kho trong inventory
        if ($this->quantity < $this->stock) {
            $this->quantity++;
        }
    }

    public function decrement()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function render()
    {
        // Lấy danh sách các thuộc tính độc nhất của sản phẩm này
        // Ví dụ: Màu sắc (Đỏ, Xanh), Kích thước (S, M)
        $attributes = collect();
        foreach ($this->product->variants as $variant) {
            foreach ($variant->attributeValues as $av) {
                $attributes->put($av->attribute->id, [
                    'name' => $av->attribute->name,
                    'values' => collect($attributes->get($av->attribute->id)['values'] ?? [])
                                ->put($av->id, $av->value)
                ]);
            }
        }

        return view('livewire.product-detail', ['attributes' => $attributes]);
    }

    public function addToCart()
    {
        // 1. Kiểm tra xem đã tìm thấy biến thể khớp với các thuộc tính đã chọn chưa
        $variant = $this->currentVariant;

        if (!$variant) {
            $this->dispatch('notify', message: 'Vui lòng chọn đầy đủ phân loại sản phẩm!');
            return;
        }

        // 2. Kiểm tra tồn kho thực tế
        if ($this->stock <= 0) {
            $this->dispatch('notify', message: 'Sản phẩm này hiện đang hết hàng!');
            return;
        }

        if ($this->quantity > $this->stock) {
            $this->dispatch('notify', message: 'Số lượng trong kho không đủ!');
            return;
        }

        // 3. Gửi sự kiện "addToCart" kèm data sang CartComponent
        // Chúng ta gửi variant_id và số lượng quantity hiện tại
        $this->dispatch('addToCart', 
            variantId: $variant->id, 
            quantity: $this->quantity
        )->to(CartComponent::class);

        // 4. Phát sự kiện mở Sidebar giỏ hàng (để Alpine ở Layout bắt được)
        $this->dispatch('open-cart-sidebar');

        // 5. Thông báo thành công
        $this->dispatch('notify', message: 'Đã thêm sản phẩm vào giỏ hàng!');
    }
}