<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrustLogResource\Pages;
use App\Filament\Resources\TrustLogResource\RelationManagers;
use App\Models\TrustLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Infolists; // Pour Infolists\Components\...
use Filament\Infolists\Infolist; // Pour le type de l'argument $infolist


class TrustLogResource extends Resource
{
    protected static ?string $model = TrustLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }
 


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date/Heure')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('app.name')->label('Application'),
                Tables\Columns\TextColumn::make('phone_number')->label('Numéro')->searchable(),
                Tables\Columns\TextColumn::make('ai_response.decision') // Accès direct au JSON
                    ->label('Décision')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'approve' => 'success',
                        'reject' => 'danger',
                        'manual_review' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('ai_response.score')
                    ->label('Confiance %')
                    ->numeric()
                    ->suffix('%'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make()->label('Détails techniques'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Analyse de l\'Agent IA')
                    ->schema([
                        Infolists\Components\TextEntry::make('ai_response.reasoning')
                            ->label('Raisonnement de l\'IA')
                            ->columnSpanFull(),
                    ]),
                Infolists\Components\Section::make('Données Brutes Réseau (Nokia NaC)')
                    ->collapsed()
                    ->schema([
                        Infolists\Components\KeyValueEntry::make('nokia_payload')
                            ->label('Payload CAMARA'),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrustLogs::route('/'),
            'create' => Pages\CreateTrustLog::route('/create'),
            'view' => Pages\ViewTrustLog::route('/{record}'),
            'edit' => Pages\EditTrustLog::route('/{record}/edit'),
        ];
    }
}
