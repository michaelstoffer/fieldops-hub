<?php

namespace App\Filament\Master\Resources;

use App\Filament\Master\Resources\UserResource\Pages;
use App\Models\Organization;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Users';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('organization_id')
                    ->label('Organization')
                    ->relationship('organization', 'name')
                    ->searchable()
                    ->nullable(),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation) => $operation === 'create')
                    ->maxLength(255),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->label('ID'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('organization.name')
                    ->searchable()
                    ->sortable()
                    ->label('Organization')
                    ->placeholder('— master —'),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->label('Roles'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('organization_id')
                    ->label('Organization')
                    ->relationship('organization', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('reset_password')
                    ->label('Reset password')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->form([
                        Forms\Components\TextInput::make('new_password')
                            ->label('New password')
                            ->password()
                            ->required()
                            ->minLength(8),
                        Forms\Components\TextInput::make('new_password_confirmation')
                            ->label('Confirm new password')
                            ->password()
                            ->same('new_password')
                            ->required(),
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->update(['password' => Hash::make($data['new_password'])]);

                        Notification::make()
                            ->title('Password reset successfully')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(false),
                Tables\Actions\Action::make('generate_password')
                    ->label('Generate & copy password')
                    ->icon('heroicon-o-clipboard-document')
                    ->color('gray')
                    ->action(function (User $record): void {
                        $password = Str::password(16);
                        $record->update(['password' => Hash::make($password)]);

                        Notification::make()
                            ->title('New password: ' . $password)
                            ->body('Copy it now — it will not be shown again.')
                            ->warning()
                            ->persistent()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
