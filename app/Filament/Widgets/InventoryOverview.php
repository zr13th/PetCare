<?php

namespace App\Filament\Widgets;

use App\Models\Batch;
use App\Models\Inventory; 
use App\Models\ProductVariant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventoryOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected static ?int $sort = 1;

    protected function getStats(): array
    {

        $lowStockCount = ProductVariant::query()
            ->join('batches', 'batches.variant_id', '=', 'product_variants.id')
            ->join('inventories', 'inventories.batch_id', '=', 'batches.id')
            ->select('product_variants.id')
            ->groupBy('product_variants.id', 'product_variants.stock_alert')
            ->havingRaw('SUM(inventories.quantity) <= product_variants.stock_alert')
            ->get()
            ->count();

        $expiredCount = Batch::query()
            ->join('inventories', 'inventories.batch_id', '=', 'batches.id')
            ->where('batches.expiry_date', '<', Carbon::now())
            ->where('inventories.quantity', '>', 0)
            ->distinct('batches.id')
            ->count();

        $nearExpiryCount = Batch::query()
            ->join('inventories', 'inventories.batch_id', '=', 'batches.id')
            ->whereBetween('batches.expiry_date', [Carbon::now(), Carbon::now()->addDays(30)])
            ->where('inventories.quantity', '>', 0)
            ->distinct('batches.id')
            ->count();

        return [
            Stat::make('Sản phẩm sắp hết hàng', $lowStockCount . ' loại')
                ->description('Tổng tồn kho chạm mức cảnh báo')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Lô hàng đã hết hạn', $expiredCount . ' lô')
                ->description('Cần tiêu hủy hoặc thu hồi')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Sắp hết hạn (30 ngày)', $nearExpiryCount . ' lô')
                ->description('Ưu tiên bán hoặc xả kho')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ,
        ];
    }
}