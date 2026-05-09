<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppResource\Pages;
use App\Filament\Resources\AppResource\RelationManagers;
use App\Models\App;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AppResource extends Resource
{
    protected static ?string $model           = App::class;
    protected static ?string $navigationIcon  = 'heroicon-o-squares-plus';
    protected static ?string $navigationLabel = 'Mes Services API';
    protected static ?string $modelLabel      = 'Service API';
    protected static ?string $navigationGroup = 'API & Intégrations';

    // ── Modèles disponibles par provider ────────────────────────────────────────
    // Centralisé ici pour être utilisé dans le form ET dans la table.
    private static function modelsByProvider(): array
    {
        return [
            // ── Providers payants ──────────────────────────────────────
            'openai' => [
                'gpt-4o-mini'    => 'GPT-4o Mini — rapide & économique ✅ Recommandé',
                'gpt-4o'         => 'GPT-4o — haute précision',
                'gpt-4.1'        => 'GPT-4.1 — dernière génération',
                'gpt-4.1-mini'   => 'GPT-4.1 Mini — équilibré',
                'o1-mini'        => 'o1 Mini — raisonnement',
            ],
            'gemini' => [
                'gemini-2.0-flash'               => 'Gemini 2.0 Flash — rapide ✅ Recommandé',
                'gemini-2.5-flash-preview-05-20' => 'Gemini 2.5 Flash Preview — dernière génération',
                'gemini-1.5-flash'               => 'Gemini 1.5 Flash — stable',
                'gemini-1.5-pro'                 => 'Gemini 1.5 Pro — haute précision',
            ],
            'claude' => [
                'claude-haiku-4-5-20251001' => 'Claude Haiku 4.5 — rapide & économique ✅ Recommandé',
                'claude-sonnet-4-6'         => 'Claude Sonnet 4.6 — équilibré',
                'claude-opus-4-6'           => 'Claude Opus 4.6 — haute précision',
            ],

            // ── [NEW-P1] Groq — Gratuit, ultra-rapide (~200ms) ────────
            // 1 000 req/jour (Llama 3.3 70B) — JSON mode natif
            // Clé : console.groq.com → API Keys (gsk_...)
            'groq' => [
                'llama-3.3-70b-versatile'        => 'Llama 3.3 70B — polyvalent ✅ Recommandé',
                'llama-3.1-8b-instant'           => 'Llama 3.1 8B Instant — ultra-rapide',
                'deepseek-r1-distill-llama-70b'  => 'DeepSeek R1 70B — raisonnement avancé',
                'qwen/qwen3-32b'                 => 'Qwen3 32B — multilingue & précis',
                'openai/gpt-oss-120b'            => 'GPT-OSS 120B — très haute capacité',
            ],

            // ── [NEW-P2] Mistral — Gratuit (Experiment plan) ──────────
            // 1 req/s, 500 000 tokens/min — JSON mode natif
            // Clé : console.mistral.ai → API Keys
            'mistral' => [
                'open-mistral-nemo'     => 'Mistral Nemo 12B — léger & gratuit ✅ Recommandé',
                'mistral-small-latest'  => 'Mistral Small — équilibré',
                'open-mixtral-8x7b'     => 'Mixtral 8x7B — haute capacité',
                'open-mistral-7b'       => 'Mistral 7B — léger & rapide',
            ],

            // ── [NEW-P3] OpenRouter — Gateway 50+ modèles :free ───────
            // 50 req/jour (free) / 1 000 req/jour (avec topup $10)
            // Clé : openrouter.ai/settings → API Keys (sk-or-...)
            'openrouter' => [
                'meta-llama/llama-3.3-70b-instruct:free' => 'Llama 3.3 70B — gratuit ✅ Recommandé',
                'google/gemma-3-27b-it:free'             => 'Gemma 3 27B — gratuit (Google)',
                'qwen/qwen3-235b-a22b:free'              => 'Qwen3 235B — gratuit & puissant',
                'mistralai/mistral-7b-instruct:free'     => 'Mistral 7B — gratuit',
                'google/gemma-4-26b-a4b-it:free'         => 'Gemma 4 26B — gratuit (Google)',
            ],

            // ── [NEW-P4] Cerebras — Gratuit, ultra-rapide (wafer-scale)
            // 14 400 req/jour, 1M tokens/jour — JSON mode natif
            // Clé : inference.cerebras.ai → API Keys (csk-...)
            'cerebras' => [
                'llama-3.3-70b' => 'Llama 3.3 70B — équilibré ✅ Recommandé',
                'llama3.1-8b'   => 'Llama 3.1 8B — ultra-rapide & léger',
            ],
        ];
    }

    private static function defaultModelFor(string $provider): string
    {
        return match ($provider) {
            'openai'     => 'gpt-4o-mini',
            'gemini'     => 'gemini-2.0-flash',
            'claude'     => 'claude-haiku-4-5-20251001',
            'groq'       => 'llama-3.3-70b-versatile',
            'mistral'    => 'open-mistral-nemo',
            'openrouter' => 'meta-llama/llama-3.3-70b-instruct:free',
            'cerebras'   => 'llama-3.3-70b',
            default      => 'gpt-4o-mini',
        };
    }

    // ── Isolation tenant ─────────────────────────────────────────────────────────
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tenant_id', Auth::user()->tenant_id);
    }

    // ── Contrôle quota plan ──────────────────────────────────────────────────────
    public static function canCreate(): bool
    {
        if (!in_array(Auth::user()->role, ['admin', 'developer'])) {
            return false;
        }

        $tenant  = Auth::user()->tenant;
        $plan    = $tenant->currentPlan();
        $maxApps = $plan?->max_apps ?? 1;

        if ($maxApps === -1) return true;

        $current = \App\Models\App::query()->where('tenant_id', $tenant->id)->count();

        if ($current >= $maxApps) {
            Notification::make()
                ->warning()
                ->title('Limite atteinte')
                ->body("Votre plan {$plan->name} autorise {$maxApps} application(s). Passez à un plan supérieur.")
                ->send();
            return false;
        }

        return true;
    }

    // ── Formulaire ───────────────────────────────────────────────────────────────
    public static function form(Form $form): Form
    {
        return $form->schema([

            // ── Informations générales ──────────────────────────────────────────
            Forms\Components\Section::make('Informations générales')
                ->description('Définissez le nom du service qui consommera KaziTrust.')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label("Nom de l'application")
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Ex: ERP Comptabilité, App Mobile...'),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Service actif')
                        ->default(true)
                        ->inline(false),
                ]),

            // ── Intelligence artificielle ───────────────────────────────────────
            Forms\Components\Section::make('Intelligence artificielle (BYO-AI)')
                ->description('Configurez votre propre clé API. Elle est chiffrée AES-256 en base de données.')
                ->icon('heroicon-o-sparkles')
                ->schema([

                    // Provider — live() pour mettre à jour le Select des modèles
                    Forms\Components\Select::make('llm_provider')
                        ->label("Fournisseur d'IA")
                        ->options([
                            // ── Providers payants ──────────────────────────────
                            'openai' => '🟢 OpenAI (payant)',
                            'gemini' => '🔵 Google Gemini (payant / gratuit limité)',
                            'claude' => '🟠 Anthropic Claude (payant)',

                            // ── [NEW] Providers 100% gratuits ─────────────────
                            'groq'       => '⚡ Groq — Gratuit · ~200ms · 1 000 req/jour',
                            'mistral'    => '🌊 Mistral — Gratuit · JSON natif · 500k tokens/min',
                            'openrouter' => '🔀 OpenRouter — Gratuit · 50+ modèles · 50 req/jour',
                            'cerebras'   => '🧠 Cerebras — Gratuit · Ultra-rapide · 14 400 req/jour',
                        ])
                        ->default('groq') // Groq par défaut : gratuit + ultra-rapide
                        ->required()
                        ->live()
                        // Quand le provider change : réinitialiser le modèle et la température
                        ->afterStateUpdated(function (Set $set, ?string $state) {
                            $set('ai_settings.model',       self::defaultModelFor($state ?? 'groq'));
                            $set('ai_settings.temperature', '0.1');
                        }),

                    // Clé API
                    Forms\Components\TextInput::make('llm_api_key')
                        ->label('Clé API sécurisée')
                        ->password()
                        ->revealable()
                        ->required(fn (string $context) => $context === 'create')
                        ->placeholder(fn (Get $get) => match ($get('llm_provider')) {
                            'openai'     => 'sk-proj-...',
                            'gemini'     => 'AIzaSy...',
                            'claude'     => 'sk-ant-...',
                            'groq'       => 'gsk_...',
                            'mistral'    => 'Votre clé Mistral...',
                            'openrouter' => 'sk-or-v1-...',
                            'cerebras'   => 'csk-...',
                            default      => 'Votre clé API...',
                        })
                        ->dehydrated(fn ($state) => filled($state))
                        ->helperText(function (Get $get) {
                            return match ($get('llm_provider')) {
                                'openai'     => '🔗 platform.openai.com/api-keys — Chiffrée AES-256 en base.',
                                'gemini'     => '🔗 aistudio.google.com/app/apikey — Chiffrée AES-256 en base.',
                                'claude'     => '🔗 console.anthropic.com/settings/keys — Chiffrée AES-256 en base.',
                                'groq'       => '🔗 console.groq.com → API Keys · Gratuit, ~200ms. Chiffrée AES-256.',
                                'mistral'    => '🔗 console.mistral.ai → API Keys · Gratuit (Experiment plan). Chiffrée AES-256.',
                                'openrouter' => '🔗 openrouter.ai/settings → API Keys · Modèles :free disponibles. Chiffrée AES-256.',
                                'cerebras'   => '🔗 inference.cerebras.ai → API Keys · Gratuit, 14 400 req/jour. Chiffrée AES-256.',
                                default      => 'Chiffrée AES-256. Jamais affichée en clair après sauvegarde.',
                            };
                        }),

                    // ── Paramètres du modèle en 2 colonnes ──────────────────────
                    Forms\Components\Grid::make(2)->schema([

                        // Modèle — options dynamiques selon le provider
                        Forms\Components\Select::make('ai_settings.model')
                            ->label('Modèle')
                            ->options(fn (Get $get): array =>
                                self::modelsByProvider()[$get('llm_provider') ?? 'groq'] ?? []
                            )
                            ->default('llama-3.3-70b-versatile')
                            ->required()
                            ->live()
                            ->helperText(fn (Get $get) => match ($get('llm_provider')) {
                                'openai'     => 'gpt-4o-mini = meilleur rapport qualité/prix.',
                                'gemini'     => 'gemini-2.0-flash = rapide et gratuit jusqu\'à 1 500 req/jour.',
                                'claude'     => 'claude-haiku = le plus rapide et économique d\'Anthropic.',
                                'groq'       => 'llama-3.3-70b-versatile = optimal anti-fraude. Ultra-rapide (~200ms).',
                                'mistral'    => 'open-mistral-nemo = gratuit, JSON natif, excellent pour du JSON structuré.',
                                'openrouter' => 'Suffixe :free = 100% gratuit. Llama 3.3 70B recommandé.',
                                'cerebras'   => 'llama-3.3-70b = équilibre performance / vitesse. Concurrent de Groq.',
                                default      => '',
                            }),

                        // Température
                        Forms\Components\TextInput::make('ai_settings.temperature')
                            ->label('Température (0.0 → 1.0)')
                            ->default('0.1')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(1)
                            ->step(0.05)
                            ->helperText('Bas (0.1) = réponses déterministes. Recommandé pour la détection de fraude.'),
                    ]),
                ]),

            // ── Webhook ─────────────────────────────────────────────────────────
            Forms\Components\Section::make('Configuration Webhook')
                ->description('Recevez des alertes temps réel quand une fraude est détectée.')
                ->icon('heroicon-o-globe-alt')
                ->collapsed()
                ->schema([
                    Forms\Components\TextInput::make('webhook_url')
                        ->label('URL du Webhook')
                        ->url()
                        ->placeholder('https://votre-serveur.com/api/kazitrust-alerts'),

                    Forms\Components\TextInput::make('webhook_secret')
                        ->label('Secret de signature (HMAC)')
                        ->password()
                        ->revealable()
                        ->dehydrated(fn ($state) => filled($state))
                        ->placeholder(fn (string $context) => $context === 'edit'
                            ? 'Laisser vide pour conserver le secret actuel'
                            : 'Généré automatiquement si vide')
                        ->helperText('Permet de vérifier que les requêtes viennent bien de KaziTrust.'),
                ]),
        ]);
    }

    // ── Table ─────────────────────────────────────────────────────────────────────
    public static function table(Table $table): Table
    {
        // Aplatir la liste des modèles pour le badge de la table
        $allModels = collect(self::modelsByProvider())
            ->flatMap(fn ($models) => $models)
            ->toArray();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Application')
                    ->searchable()->sortable()->weight('bold'),

                Tables\Columns\TextColumn::make('uuid')
                    ->label('UUID')
                    ->copyable()->copyMessage('UUID copié !')
                    ->icon('heroicon-o-clipboard-document')
                    ->color('gray')
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('llm_provider')
                    ->label('Provider')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'openai'     => '🟢 OpenAI',
                        'gemini'     => '🔵 Gemini',
                        'claude'     => '🟠 Claude',
                        'groq'       => '⚡ Groq',
                        'mistral'    => '🌊 Mistral',
                        'openrouter' => '🔀 OpenRouter',
                        'cerebras'   => '🧠 Cerebras',
                        default      => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'openai'     => 'success',
                        'gemini'     => 'info',
                        'claude'     => 'warning',
                        'groq'       => 'danger',   // rouge vif = rapidité
                        'mistral'    => 'primary',
                        'openrouter' => 'gray',
                        'cerebras'   => 'success',
                        default      => 'gray',
                    }),

                // Affiche le modèle configuré (extrait de ai_settings JSON)
                Tables\Columns\TextColumn::make('ai_settings')
                    ->label('Modèle')
                    ->formatStateUsing(function ($state) use ($allModels) {
                        $model = is_array($state) ? ($state['model'] ?? '—') : '—';
                        // Libellé court : on retire la partie "— ... ✅ Recommandé"
                        $label = $allModels[$model] ?? $model;
                        return strtok($label, ' —') ?: $model;
                    })
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('apiKeys_count')
                    ->label('Clés API')
                    ->counts('apiKeys')
                    ->badge()->color('gray'),

                Tables\Columns\TextColumn::make('trustLogs_count')
                    ->label('Appels ce mois')
                    ->counts('trustLogs')
                    ->badge()->color('info'),

                Tables\Columns\ToggleColumn::make('is_active')->label('Actif'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')->dateTime('d/m/Y')
                    ->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Statut'),
                Tables\Filters\SelectFilter::make('llm_provider')
                    ->label('Provider IA')
                    ->options([
                        // Payants
                        'openai' => '🟢 OpenAI',
                        'gemini' => '🔵 Gemini',
                        'claude' => '🟠 Claude',
                        // Gratuits
                        'groq'       => '⚡ Groq (gratuit)',
                        'mistral'    => '🌊 Mistral (gratuit)',
                        'openrouter' => '🔀 OpenRouter (gratuit)',
                        'cerebras'   => '🧠 Cerebras (gratuit)',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Aucun service API')
            ->emptyStateDescription('Créez votre première application pour obtenir un UUID et commencer à intégrer KaziTrust.')
            ->emptyStateIcon('heroicon-o-squares-plus');
    }

    // ── Permissions ───────────────────────────────────────────────────────────────
    public static function canViewAny(): bool
    {
        return in_array(Auth::user()->role, ['admin', 'developer']);
    }

    public static function canEdit($record): bool
    {
        return in_array(Auth::user()->role, ['admin', 'developer']);
    }

    public static function canDelete($record): bool
    {
        return in_array(Auth::user()->role, ['admin', 'developer']);
    }

    // ── Relations ─────────────────────────────────────────────────────────────────
    public static function getRelations(): array
    {
        return [
            RelationManagers\ApiKeysRelationManager::class,
        ];
    }

    // ── Pages ─────────────────────────────────────────────────────────────────────
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListApps::route('/'),
            'create' => Pages\CreateApp::route('/create'),
            'edit'   => Pages\EditApp::route('/{record}/edit'),
        ];
    }
}