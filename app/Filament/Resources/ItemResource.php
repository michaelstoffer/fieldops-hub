<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Models\Item;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $navigationLabel = 'Catalog Items';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('sku')
                ->label('SKU')
                ->maxLength(100),
            TextInput::make('unit_price')
                ->label('Unit Price')
                ->numeric()
                ->prefix('$')
                ->minValue(0)
                ->step(0.01)
                ->required(),
            Select::make('unit')
                ->options([
                    'each'  => 'Each',
                    'hr'    => 'Hour',
                    'ft'    => 'Foot',
                    'sqft'  => 'Sq Ft',
                    'lb'    => 'Pound',
                    'gal'   => 'Gallon',
                ])
                ->default('each')
                ->required(),
            Textarea::make('description')->rows(3)->columnSpanFull(),
            Toggle::make('is_taxable')->label('Taxable')->default(true),
            Toggle::make('is_active')->label('Active')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('unit_price')
                    ->label('Price')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('unit')->sortable(),
                IconColumn::make('is_taxable')->label('Taxable')->boolean(),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Active'),
                TrashedFilter::make(),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit'   => Pages\EditItem::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->where('organization_id', auth()->user()?->organization_id);
    }
}
