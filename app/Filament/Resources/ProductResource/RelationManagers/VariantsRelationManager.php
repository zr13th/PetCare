<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductVariant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'Biến thể';

    /*
    |--------------------------------------------------------------------------
    | FORM
    |--------------------------------------------------------------------------
    */
    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin SKU')
                ->schema([
                    Forms\Components\TextInput::make('sku')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->disabled()
                        ->dehydrated(),

                    Forms\Components\TextInput::make('price')
                        ->numeric()
                        ->required()
                        ->prefix('đ'),

                    Forms\Components\TextInput::make('compare_price')
                        ->numeric()
                        ->prefix('đ'),

                    Forms\Components\TextInput::make('stock_alert')
                        ->numeric()
                        ->default(10),

                    Forms\Components\Toggle::make('is_active')
                        ->default(true),
                ])
                ->columns(2),

            Forms\Components\Section::make('Thuộc tính biến thể')
                ->schema([
                    Forms\Components\Repeater::make('attributeValues')
                        // Lưu ý: relationship này phải là belongsToMany trong Model ProductVariant
                        ->relationship()
                        ->schema([
                            Forms\Components\Select::make('attribute_id')
                                ->label('Thuộc tính')
                                ->options(Attribute::pluck('name', 'id'))
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(fn ($set) => $set('attribute_value_id', null)),

                            Forms\Components\Select::make('attribute_value_id')
                                ->label('Giá trị')
                                ->required()
                                ->options(fn ($get) =>
                                    AttributeValue::where('attribute_id', $get('attribute_id'))
                                        ->pluck('value', 'id')
                                )
                                ->reactive()
                                ->afterStateUpdated(function ($get, $set, $livewire) {
                                    $this->updateSku($get, $set, $livewire);
                                }),
                        ])
                        ->columns(2)
                        ->defaultItems(1),
                ])
        ]);
    }

    // Tách hàm cập nhật SKU để code sạch hơn
    protected function updateSku($get, $set, $livewire)
    {
        $items = collect($get('../../attributeValues'))
            ->filter(fn ($i) => !empty($i['attribute_value_id']));

        if ($items->isEmpty()) return;

        $items = $items->sortBy('attribute_id');
        $values = AttributeValue::whereIn('id', $items->pluck('attribute_value_id'))->pluck('value', 'id');

        $codes = $items->map(function ($item) use ($values) {
            $val = $values[$item['attribute_value_id']] ?? null;
            return $val ? strtoupper(substr(Str::slug($val), 0, 3)) : null;
        })->filter()->values();

        $product = $livewire->getOwnerRecord();
        if ($product && $codes->isNotEmpty()) {
            $set('../../sku', strtoupper(
                ($product->code ?? Str::slug($product->name)) . '-' . $codes->join('-')
            ));
        }
    }

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')->searchable(),
                Tables\Columns\TextColumn::make('price')->money('VND'),
                Tables\Columns\TextColumn::make('attribute_values_info')
                    ->label('Thuộc tính')
                    ->getStateUsing(fn ($record) => 
                        $record->attributeValues
                            ->map(fn ($item) => "{$item->attribute->name}: {$item->value}")
                            ->join(', ')
                    ),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Tạo biến thể'),
                Tables\Actions\Action::make('generateVariants')
                    ->label('Tạo hàng loạt')
                    ->form([
                        Forms\Components\Repeater::make('attributes')
                            ->schema([
                                Forms\Components\Select::make('attribute_id')
                                    ->options(Attribute::pluck('name', 'id'))
                                    ->required()
                                    ->reactive(),
                                Forms\Components\Select::make('value_ids')
                                    ->multiple()
                                    ->required()
                                    ->options(fn ($get) =>
                                        AttributeValue::where('attribute_id', $get('attribute_id'))
                                            ->pluck('value', 'id')
                                    ),
                            ]),
                        Forms\Components\TextInput::make('bulk_price')->numeric()->label('Giá chung'),
                        Forms\Components\TextInput::make('bulk_stock')->numeric()->label('Cảnh báo kho'),
                    ])
                    ->action(function (array $data) {
                        $this->generateVariantsFromSelection($data['attributes'], $data['bulk_price'] ?? 0, $data['bulk_stock'] ?? 0);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIC XỬ LÝ
    |--------------------------------------------------------------------------
    */
    protected function generateVariantsFromSelection(array $attributes, $bulkPrice = 0, $bulkStock = 0)
    {
        $product = $this->getOwnerRecord();
        $matrix = [];

        foreach ($attributes as $attr) {
            if (!empty($attr['attribute_id']) && !empty($attr['value_ids'])) {
                $matrix[$attr['attribute_id']] = $attr['value_ids'];
            }
        }

        if (empty($matrix)) return;
        ksort($matrix);
        $combinations = $this->cartesian($matrix);
        $valueCache = AttributeValue::whereIn('id', collect($matrix)->flatten())->get()->keyBy('id');

        foreach ($combinations as $combo) {
            if ($this->variantExists($product->id, $combo)) continue;

            $sku = $this->ensureUniqueSku($this->generateSku($product, $combo, $valueCache));

            $variant = $product->variants()->create([
                'price' => $bulkPrice,
                'stock_alert' => $bulkStock,
                'sku' => $sku,
                'is_active' => true,
            ]);

            // Sync vào bảng trung gian
            foreach ($combo as $attributeId => $valueId) {
                $variant->attributeValues()->attach($valueId, ['attribute_id' => $attributeId]);
            }
        }
    }

    protected function variantExists($productId, $combo)
    {
        $query = ProductVariant::where('product_id', $productId);

        foreach ($combo as $attributeId => $valueId) {
            // Sử dụng whereHas với tiền tố bảng để tránh lỗi ambiguous
            $query->whereHas('attributeValues', function ($q) use ($attributeId, $valueId) {
                $q->where('variant_attribute_values.attribute_id', $attributeId)
                  ->where('variant_attribute_values.attribute_value_id', $valueId);
            });
        }

        // Đảm bảo số lượng thuộc tính khớp đúng (tránh trường hợp combo là con của một biến thể khác)
        return $query->has('attributeValues', '=', count($combo))->exists();
    }

    protected function generateSku($product, $combo, $cache)
    {
        $prefix = $product->code ?? strtoupper(Str::slug($product->name));
        $values = collect($combo)->sortKeys()
            ->map(fn ($id) => strtoupper(substr(Str::slug($cache[$id]->value ?? ''), 0, 3)))
            ->filter()->values();

        return $prefix . '-' . $values->join('-');
    }

    protected function ensureUniqueSku($sku)
    {
        $original = $sku; $i = 1;
        while (ProductVariant::where('sku', $sku)->exists()) {
            $sku = $original . '-' . $i++;
        }
        return $sku;
    }

    protected function cartesian(array $arrays)
    {
        $result = [[]];
        foreach ($arrays as $attributeId => $values) {
            $tmp = [];
            foreach ($result as $item) {
                foreach ($values as $value) {
                    $tmp[] = $item + [$attributeId => $value];
                }
            }
            $result = $tmp;
        }
        return $result;
    }
}