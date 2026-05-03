<?php
// app/Livewire/OrderFailed.php

namespace App\Livewire;

use App\Models\Invoice;
use Livewire\Attributes\Layout;
use Livewire\Component;

class OrderFailed extends Component
{
    public Invoice $invoice;

    public function mount(string $invoiceNumber): void
    {
        $this->invoice = Invoice::where('invoice_number', $invoiceNumber)
            ->where('user_id', auth()->id())
            ->firstOrFail();
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.order-failed');
    }
}