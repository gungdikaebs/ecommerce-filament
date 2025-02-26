<?php

namespace App\Filament\Resources;

use Filament\Tables\Table;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\UserResource\Pages\CreateUser as CreateRecord;
use App\Filament\Resources\UserResource\RelationManagers\OrdersRelationManager;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required(),

                Forms\Components\TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required(),

                Forms\Components\DateTimePicker::make('email_verified_at')
                    ->label('Email Verified At')
                    ->default(now()),

                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(Page $livewire): bool => $livewire instanceof CreateRecord),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            OrdersRelationManager::class,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            // 'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
}
