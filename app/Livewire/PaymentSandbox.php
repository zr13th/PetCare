<?php
// app/Livewire/PaymentSandbox.php

namespace App\Livewire;

use App\Models\Invoice;
use App\Models\Payment;
use Livewire\Attributes\Layout;
use Livewire\Component;

class PaymentSandbox extends Component
{
    public Invoice $invoice;

    public function mount(string $invoiceNumber): void
    {
        $this->invoice = Invoice::with(['payment.paymentMethod', 'items'])
            ->where('invoice_number', $invoiceNumber)
            ->where('user_id', auth()->id()) // Bảo mật: chỉ xem invoice của mình
            ->firstOrFail();

        // Nếu đã thanh toán rồi thì redirect luôn
        if ($this->invoice->payment?->status === 'completed') {
            $this->redirect(route('order.success', $invoiceNumber));
        }
    }

    public function processPayment(bool $success): void
    {
        $payment = $this->invoice->payment;

        if ($success) {
            $payment->update([
                'status'         => 'completed',
                'transaction_id' => 'SANDBOX-' . strtoupper(\Str::random(10)),
                'paid_at'        => now(),
            ]);

            $this->invoice->update(['status' => 'confirmed']);

            $this->redirect(route('order.success', $this->invoice->invoice_number));
        } else {
            $payment->update(['status' => 'failed']);

            $this->invoice->update(['status' => 'cancelled']);

            $this->redirect(route('order.failed', $this->invoice->invoice_number));
        }
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.payment-sandbox');
    }
}