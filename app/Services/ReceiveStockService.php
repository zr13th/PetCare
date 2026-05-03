<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Inventory;
use App\Models\StockMovement;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReceiveStockService
{
    public function handle(PurchaseOrder $purchaseOrder): void
    {
        if ($purchaseOrder->isReceived()) {
            return;
        }

        DB::transaction(function () use ($purchaseOrder) {

            foreach ($purchaseOrder->items as $item) {

                // 1. Tạo batch
                $batch = Batch::create([
                    'variant_id' => $item->variant_id,
                    'batch_number' => $this->generateBatchNumber(),
                    'manufacture_date' => null,
                    'expiry_date' => null,
                    'cost_price' => $item->cost_price,
                ]);

                // 2. Inventory
                $inventory = Inventory::firstOrCreate(
                    [
                        'warehouse_id' => $item->warehouse_id,
                        'batch_id' => $batch->id,
                    ],
                    [
                        'quantity' => 0
                    ]
                );

                $inventory->increment('quantity', $item->quantity);

                // 3. Stock movement
                StockMovement::create([
                    'batch_id' => $batch->id,
                    'warehouse_id' => $item->warehouse_id,
                    'type' => StockMovement::TYPE_IN,
                    'quantity' => $item->quantity,
                    'reference_type' => PurchaseOrder::class,
                    'reference_id' => $purchaseOrder->id,
                ]);
            }

            // 4. Update total
            $purchaseOrder->calculateTotal();

            // 5. Update status
            $purchaseOrder->update([
                'status' => PurchaseOrder::STATUS_RECEIVED
            ]);
        });
    }

    protected function generateBatchNumber(): string
    {
        return 'BATCH-' . now()->format('Ymd-His') . '-' . strtoupper(Str::random(4));
    }
}