<?php

namespace App\Filament\Supramanager\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;

class SuperAdminList extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Équipe KaziTrust';
    protected static ?string $title = 'Administrateurs Plateforme';
    protected static ?string $navigationGroup = 'Système';

    protected static string $view = 'filament.supramanager.pages.super-admin-list';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // On suppose ici que tes admins ont un tenant_id = null OU un rôle spécifique 'super_admin'
                User::whereNull('tenant_id')->orWhere('role', 'super_admin')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->weight('bold'),
                    
                TextColumn::make('email')
                    ->label('Email')
                    ->icon('heroicon-m-envelope')
                    ->searchable(),
                    
                IconColumn::make('is_admin')
                    ->label('Accès Supramanager')
                    ->boolean()
                    ->default(true), // Juste pour l'aspect visuel (Badge Vert/Croix Rouge)

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                // Filtres si besoin
            ])
            ->actions([
                // Actions rapides (ex: bloquer un accès)
            ]);
    }
}