<?php

namespace App\Livewire;

use App\Models\Address;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Shipment;
use App\Models\ShipmentMethod;
use App\Services\VNPayService;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Checkout extends Component
{
    public $selectedAddressId  = null;
    public $receiverName       = '';
    public $receiverPhone      = '';
    public $addressLine        = '';
    public $province           = '';
    public $provinceCode       = '';
    public $ward               = '';
    public $wardCode           = '';
    public $note               = '';
    public $saveAddress        = false;
    public $selectedPaymentId  = null;
    public $selectedShipmentId = null;

    public function mount(): void
    {
        if (!auth()->check()) {
            $this->redirect(route('login'));
            return;
        }

        try { Cart::restore(auth()->id()); } catch (\Exception) {}

        if (Cart::count() === 0) {
            $this->redirect('/');
            return;
        }

        $default = auth()->user()->addresses()->where('is_default', true)->first();
        if ($default) {
            $this->fillAddress($default);
            $this->selectedAddressId = $default->id;
        }

        $this->selectedPaymentId  = PaymentMethod::active()->first()?->id;
        $this->selectedShipmentId = ShipmentMethod::active()->first()?->id;
    }

    public function selectAddress(int $id): void
    {
        $address = auth()->user()->addresses()->findOrFail($id);
        $this->fillAddress($address);
        $this->selectedAddressId = $id;
    }

    private function fillAddress(Address $address): void
    {
        $this->receiverName  = $address->receiver_name;
        $this->receiverPhone = $address->receiver_phone;
        $this->addressLine   = $address->address_line;
        $this->province      = $address->province;
        $this->ward          = $address->ward;
    }

    public function getShippingFeeProperty(): float
    {
        if (!$this->selectedShipmentId) return 0;
        return (float) ShipmentMethod::find($this->selectedShipmentId)?->fee ?? 0;
    }

    public function getSubtotalProperty(): float
    {
        return (float) str_replace(['.', ','], ['', '.'], Cart::subtotal(2, ',', '.'));
    }

    public function getTotalProperty(): float
    {
        return $this->subtotal + $this->shippingFee;
    }

    public function placeOrder(): void
    {
        $this->validate([
            'receiverName'       => 'required|string|max:255',
            'receiverPhone'      => 'required|string|max:20',
            'addressLine'        => 'required|string|max:255',
            'province'           => 'required|string|max:100',
            'ward'               => 'required|string|max:100',
            'selectedPaymentId'  => 'required|exists:payment_methods,id',
            'selectedShipmentId' => 'required|exists:shipment_methods,id',
        ]);

        $cartItems = Cart::content();
        if ($cartItems->isEmpty()) return;

        // ★ Transaction chỉ tạo data, KHÔNG redirect bên trong
        $invoice = DB::transaction(function () use ($cartItems) {
            $subtotal    = $this->subtotal;
            $shippingFee = $this->shippingFee;
            $total       = $this->total;

            $invoice = Invoice::create([
                'user_id'        => auth()->id(),
                'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                'receiver_name'  => $this->receiverName,
                'receiver_phone' => $this->receiverPhone,
                'address_line'   => $this->addressLine,
                'province'       => $this->province,
                'ward'           => $this->ward,
                'subtotal'       => $subtotal,
                'shipping_fee'   => $shippingFee,
                'total_amount'   => $total,
                'status'         => 'pending',
                'note'           => $this->note,
            ]);

            foreach ($cartItems as $item) {
                InvoiceItem::create([
                    'invoice_id'   => $invoice->id,
                    'variant_id'   => $item->id,
                    'product_name' => $item->name,
                    'variant_sku'  => $item->model?->sku ?? '',
                    'quantity'     => $item->qty,
                    'price'        => $item->price,
                    'subtotal'     => $item->price * $item->qty,
                ]);
            }

            Payment::create([
                'invoice_id'        => $invoice->id,
                'payment_method_id' => $this->selectedPaymentId,
                'amount'            => $total,
                'status'            => 'pending',
            ]);

            Shipment::create([
                'invoice_id'         => $invoice->id,
                'shipment_method_id' => $this->selectedShipmentId,
                'shipping_fee'       => $shippingFee,
                'status'             => 'preparing',
            ]);

            if ($this->saveAddress && !$this->selectedAddressId) {
                Address::create([
                    'user_id'        => auth()->id(),
                    'receiver_name'  => $this->receiverName,
                    'receiver_phone' => $this->receiverPhone,
                    'address_line'   => $this->addressLine,
                    'province'       => $this->province,
                    'ward'           => $this->ward,
                    'is_default'     => false,
                ]);
            }

            Cart::destroy();
            try { Cart::erase(auth()->id()); } catch (\Exception) {}

            return $invoice;
        });

        // ★ Sau transaction mới dispatch + redirect
        $this->dispatch('cart-updated');

        $paymentCode = PaymentMethod::find($this->selectedPaymentId)?->code;

        match($paymentCode) {
            'vnpay'   => $this->redirectToVNPay($invoice),
            'sandbox' => $this->redirect(route('payment.sandbox', $invoice->invoice_number)),
            default   => $this->redirect(route('order.success', $invoice->invoice_number)),
        };
    }

    private function redirectToVNPay(Invoice $invoice): void
    {
        $vnpay = app(VNPayService::class);

        $url = $vnpay->createPaymentUrl(
            orderRef:  $invoice->invoice_number,
            amount:    (int) $invoice->total_amount,
            orderInfo: 'Thanh toan don hang ' . $invoice->invoice_number,
            ipAddr:    request()->ip(),
        );

        $this->redirect($url);
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.checkout', [
            'cartItems'       => Cart::content(),
            'paymentMethods'  => PaymentMethod::active()->get(),
            'shipmentMethods' => ShipmentMethod::active()->get(),
            'addresses'       => auth()->user()->addresses()->get(),
        ]);
    }
}