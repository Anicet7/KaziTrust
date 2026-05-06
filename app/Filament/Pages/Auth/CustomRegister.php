<?php

namespace App\Filament\Pages\Auth;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeKaziTrust; // Assure-toi de créer ce Mailable



use Illuminate\Support\Facades\App;

class CustomRegister extends BaseRegister
{
    protected static string $view = 'filament.pages.auth.custom-register';

    public string $currentLocale = 'fr';

    public function mount(): void
    {
        $locale = request('lang', session('kz_locale', 'fr'));
        if (in_array($locale, ['fr', 'en'])) {
            session(['kz_locale' => $locale]);
            App::setLocale($locale);
        }
        $this->currentLocale = session('kz_locale', 'fr');

        parent::mount();
    }

    public function form(Form $form): Form
    {
        $locale = $this->currentLocale;

        return $form
            ->schema([
                TextInput::make('company_name')
                    ->label($locale === 'fr' ? 'Nom de votre Entreprise / PME' : 'Company / Business Name')
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),

                $this->getNameFormComponent()
                    ->label($locale === 'fr' ? 'Votre Prénom & Nom' : 'Your Full Name'),

                $this->getEmailFormComponent()
                    ->label($locale === 'fr' ? 'Adresse Email' : 'Email Address'),

                $this->getPasswordFormComponent()
                    ->label($locale === 'fr' ? 'Mot de passe' : 'Password'),

                $this->getPasswordConfirmationFormComponent()
                    ->label($locale === 'fr' ? 'Confirmer le mot de passe' : 'Confirm Password'),
            ]);
    }

    protected function handleRegistration(array $data): User
    {
        $user = DB::transaction(function () use ($data) {

            // 1. Créer le Tenant
            $tenant = Tenant::create([
                'name'      => $data['company_name'],
                'email'     => $data['email'],
                'slug'      => Str::slug($data['company_name']) . '-' . Str::random(4),
                'is_active' => true,
            ]);

            // 2. Démarrer le trial (récupère le plan "trial" depuis la BDD)
            //  $trialPlan = Plan::where('slug', 'trial')->firstOrFail();

            $trialPlan = \App\Models\Plan::query()
                ->where('slug', 'trial')
                ->firstOrFail();
            $tenant->startTrial($trialPlan);

            // 3. Créer l'utilisateur propriétaire
            $user = User::create([
                'name'      => $data['name'],
                'email'     => $data['email'],
               // 'password'  => bcrypt($data['password']),
                'password'  => $data['password'], 
                'tenant_id' => $tenant->id,
                'role'      => 'admin',
            ]);

            return $user;
        });


        // 4. Déclencher l'envoi de l'email de bienvenue
       /// Mail::to($user->email)->send(new WelcomeKaziTrust($user));

        return $user;
    }
}

/*
class CustomRegister extends BaseRegister
{
   // protected static string $view = 'filament.pages.auth.custom-register';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('company_name')
                ->label('Nom de votre entreprise / PME')
                ->required()
                ->maxLength(255)
                ->autofocus(),
            $this->getNameFormComponent(),
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),
        ]);
    }

    protected function handleRegistration(array $data): User
    {
          $user = DB::transaction(function () use ($data) {

            // 1. Créer le Tenant
            $tenant = Tenant::create([
                'name'      => $data['company_name'],
                'email'     => $data['email'],
                'slug'      => Str::slug($data['company_name']) . '-' . Str::random(4),
                'is_active' => true,
            ]);

            // 2. Démarrer le trial (récupère le plan "trial" depuis la BDD)
            //  $trialPlan = Plan::where('slug', 'trial')->firstOrFail();

            $trialPlan = \App\Models\Plan::query()
                ->where('slug', 'trial')
                ->firstOrFail();
            $tenant->startTrial($trialPlan);

            // 3. Créer l'utilisateur propriétaire
            $user = User::create([
                'name'      => $data['name'],
                'email'     => $data['email'],
               // 'password'  => bcrypt($data['password']),
                'password'  => $data['password'], 
                'tenant_id' => $tenant->id,
                'role'      => 'admin',
            ]);

            return $user;
        });


        // 4. Déclencher l'envoi de l'email de bienvenue
        Mail::to($user->email)->send(new WelcomeKaziTrust($user));

        return $user;

    }

    
    

     protected function getRedirectUrl(): string
    {
        return $this->panel->getUrl();
    }


}

*/