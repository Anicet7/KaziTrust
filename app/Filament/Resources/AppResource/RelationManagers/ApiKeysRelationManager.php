<?php

namespace App\Filament\Resources\AppResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ApiKeysRelationManager extends RelationManager
{
    protected static string $relationship = 'apiKeys';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nom de la clé')
                ->placeholder('Ex: Clé Production, Clé Test')
                ->required(),
            // La clé 'key' est générée automatiquement par le modèle au boot()
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nom'),
                Tables\Columns\TextColumn::make('key')
                    ->label('Clé API (Token)')
                    ->fontFamily('mono')
                    ->copyable()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('last_used_at')
                    ->label('Dernière utilisation')
                    ->dateTime()
                    ->placeholder('Jamais utilisée'),
                Tables\Columns\ToggleColumn::make('is_active')->label('Active'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Générer une clé')
                    ->modalHeading('Nouvelle clé d\'accès'),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()->label('Révoquer'),
            ]);
    }
}
