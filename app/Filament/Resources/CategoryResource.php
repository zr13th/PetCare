<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Danh mục';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin danh mục')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('Tên danh mục')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(string $state, Forms\Set $set) => $set('slug', Str::slug($state))),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->label('Đường dẫn (Slug)')
                            ->unique(ignoreRecord: true),

                        Forms\Components\Select::make('parent_id')
                            ->label('Danh mục cha')
                            ->relationship(
                                'parent', 'name',
                                fn (Builder $query, $record) => $query
                                    ->select('id', 'name', 'parent_id')
                                    ->when($record, fn($query) => $query->where('id', '!=', $record->id))
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->formatted_name}")
                            ->searchable()
                            ->preload()
                            ->placeholder('Để trống nếu là danh mục gốc'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Trạng thái')
                            ->default(true)
                            ->inline(false),

                        Forms\Components\Textarea::make('description')
                            ->label('Mô tả')
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên danh mục')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Danh mục cha')
                    ->placeholder('Gốc')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Hoạt động')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->groups([
                Tables\Grouping\Group::make('parent.name')
                    ->label('Danh mục cha')
                    ->collapsible()
            ])
            ->defaultGroup('parent.name')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Trạng thái hoạt động'),
                
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Danh mục cha')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['parent:id,name']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}