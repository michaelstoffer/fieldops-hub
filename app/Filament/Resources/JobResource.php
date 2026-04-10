<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobResource\Pages;
use App\Models\Job;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JobResource extends Resource
{
    protected static ?string $model = Job::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Jobs';

    protected static ?int $navigationSort = 5;

    // Read-only: no create action
    protected static bool $canCreate = false;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Job Details')->schema([
                TextEntry::make('title'),
                TextEntry::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled'   => 'info',
                        'en_route'    => 'warning',
                        'in_progress' => 'warning',
                        'completed'   => 'success',
                        'cancelled'   => 'danger',
                        'on_hold'     => 'gray',
                        default       => 'gray',
                    }),
                TextEntry::make('customer.full_name')->label('Customer'),
                TextEntry::make('property.address_line1')->label('Property'),
                TextEntry::make('jobType.name')->label('Job Type'),
                TextEntry::make('assignedTechnician.name')->label('Assigned To'),
                TextEntry::make('scheduled_at')->label('Scheduled')->dateTime(),
                TextEntry::make('started_at')->label('Started')->dateTime(),
                TextEntry::make('completed_at')->label('Completed')->dateTime(),
            ])->columns(2),
            Section::make('Notes')->schema([
                TextEntry::make('description')->prose(),
                TextEntry::make('office_notes')->label('Office Notes')->prose(),
                TextEntry::make('technician_notes')->label('Technician Notes')->prose(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('scheduled_at')
                    ->label('Scheduled')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
                TextColumn::make('title')->searchable()->limit(40),
                TextColumn::make('customer.last_name')
                    ->label('Customer')
                    ->formatStateUsing(fn ($record) => $record->customer?->full_name)
                    ->searchable(['customers.last_name', 'customers.first_name']),
                TextColumn::make('assignedTechnician.name')
                    ->label('Technician')
                    ->placeholder('Unassigned'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled'   => 'info',
                        'en_route'    => 'warning',
                        'in_progress' => 'warning',
                        'completed'   => 'success',
                        'cancelled'   => 'danger',
                        'on_hold'     => 'gray',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Job::statuses()[$state] ?? $state),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(Job::statuses()),
                SelectFilter::make('assigned_to')
                    ->label('Technician')
                    ->relationship('assignedTechnician', 'name', fn (Builder $query) =>
                        $query->where('organization_id', auth()->user()?->organization_id)
                    )
                    ->placeholder('All technicians'),
            ])
            ->defaultSort('scheduled_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobs::route('/'),
            'view'  => Pages\ViewJob::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->where('organization_id', auth()->user()?->organization_id)
            ->with(['customer', 'assignedTechnician', 'jobType']);
    }
}
