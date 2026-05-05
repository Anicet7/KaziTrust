<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Mon Équipe';
    protected static ?string $navigationGroup = 'Administration';

  

    // ✅ FIX #1 : seulement les admins voient et gèrent les utilisateurs
    public static function canViewAny(): bool
    {
        return Auth::user()->role === 'admin';
    }

    // ✅ FIX #2 : isolation tenant stricte
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tenant_id', Auth::user()->tenant_id);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Nom complet')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            TextInput::make('password')
                ->password()
                ->revealable()
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $context) => $context === 'create')
                ->placeholder(fn (string $context) => $context === 'edit'
                    ? 'Laisser vide pour ne pas changer'
                    : null)
                ->minLength(8),

            Select::make('role')
                ->label('Rôle')
                ->options([
                    'admin'     => 'Administrateur',
                    'developer' => 'Développeur API',
                    'viewer'    => 'Lecteur (Logs uniquement)',
                ])
                ->default('developer')
                ->required()
                // ✅ FIX #3 : empêcher l'admin de changer son propre rôle
                ->disabled(fn ($record) => $record?->id === Auth::id())
                ->helperText(fn ($record) => $record?->id === Auth::id()
                    ? 'Vous ne pouvez pas modifier votre propre rôle.'
                    : null),
        ]);
    }

    // ✅ FIX #3 : injecter tenant_id à la création
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = Auth::user()->tenant_id;
        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()->copyable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Rôle')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'admin'     => 'danger',
                        'developer' => 'info',
                        'viewer'    => 'gray',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'admin'     => 'Administrateur',
                        'developer' => 'Développeur',
                        'viewer'    => 'Lecteur',
                        default     => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Membre depuis')->dateTime('d/m/Y')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // ✅ Empêcher la suppression de son propre compte
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (User $record) => $record->id === Auth::id()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading("Aucun membre dans l'équipe")
            ->emptyStateDescription("Invitez des collaborateurs pour partager l'accès à KaziTrust.")
            ->emptyStateIcon('heroicon-o-users');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }

       /// Gestion Role      
        public static function canCreate(): bool
        {
            return Auth::user()->role === 'admin';
        }

        public static function canEdit($record): bool
        {
            return Auth::user()->role === 'admin';
        }

        public static function canDelete($record): bool
        {
            return Auth::user()->role === 'admin'
                && $record->id !== Auth::id(); // ne peut pas se supprimer lui-même
        }


}