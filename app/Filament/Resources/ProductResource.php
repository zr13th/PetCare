<?php

namespace App\Filament\Resources;

use Illuminate\Database\Eloquent\Builder;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;
use Filament\Resources\Resource;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Filters\TrashedFilter;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Shop';
    protected static ?string $navigationLabel = 'Sản phẩm';
    protected static ?string $modelLabel = 'Sản phẩm';

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Group::make()->schema([

                Forms\Components\Section::make('Thông tin chung')->schema([

                    Forms\Components\TextInput::make('name')
                        ->label('Tên sản phẩm')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, $set) =>
                            $set('slug', str()->slug($state))
                        ),

                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true),

                    Forms\Components\RichEditor::make('description')
                        ->columnSpanFull(),

                ])->columns(2),

            ])->columnSpan(4),

            Forms\Components\Group::make()->schema([

                Forms\Components\Section::make('Phân loại')->schema([

                    Forms\Components\Select::make('category_id')
                        ->label('Danh mục')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->required(),

                    Forms\Components\Select::make('brand_id')
                        ->label('Thương hiệu')
                        ->relationship('brand', 'name')
                        ->searchable(),

                    Forms\Components\Select::make('pet_type_id')
                        ->label('Loại thú cưng')
                        ->relationship('petType', 'name')
                        ->searchable(),

                    Forms\Components\TextInput::make('unit')
                        ->label('Đơn vị')
                        ->default('Cái'),

                    Forms\Components\Toggle::make('status')
                        ->label('Hiển thị')
                        ->default(true),

                ]),

            ])->columnSpan(2),

            Forms\Components\Section::make('Hình ảnh sản phẩm')
            ->collapsible()
            ->collapsed()
            ->schema([
                SpatieMediaLibraryFileUpload::make('images')
                    ->label('')
                    ->collection('product_images')
                    ->multiple()
                    ->reorderable()
                    ->appendFiles()
                    ->image()
                    ->imageEditor()
                    ->panelLayout('grid')
                    ->conversion('thumb'),
            ])
            ->columnSpanFull(),

        ])->columns(6);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                SpatieMediaLibraryImageColumn::make('product_images')
                    ->label('Ảnh đại diện')
                    ->collection('product_images')
                    ->conversion('thumb')
                    ->limit(1),

                Tables\Columns\TextColumn::make('name')
                    ->label('Tên sản phẩm')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Danh mục')
                    ->sortable(),

                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Thương hiệu'),

                Tables\Columns\TextColumn::make('variants_count')
                    ->counts('variants')
                    ->label('Số SKU'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Hiển thị')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y'),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()->modalWidth('7xl'),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                ])
                ->tooltip('Thao tác'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('variants')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
        ]);;
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\VariantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}