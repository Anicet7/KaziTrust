<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\BelongsToTenant; 

use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

use Filament\Models\Contracts\FilamentUser;

/**
 * @method static \Illuminate\Database\Eloquent\Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder whereIn($column, $values, $boolean = 'and', $not = false)
 */

class User extends Authenticatable  implements HasTenants , FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',

        'tenant_id', // ✅ ajouté
        'role',      // ✅ ajouté
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // Méthode requise par Filament pour lister les tenants de l'utilisateur
    public function getTenants(Panel $panel): array|Collection
    {

       // Superadmin n'a pas de tenant
        if ($this->role === 'superadmin') {
            return [];
        }

        // Si l'utilisateur a un tenant assigné, on le retourne dans un tableau
        return $this->tenant ? [$this->tenant] : [];
    }

    // Méthode requise par Filament pour vérifier l'accès
    public function canAccessTenant(Model $tenant): bool
    {

      // Superadmin accède à tout, client seulement à son tenant
        if ($this->role === 'superadmin') {
            return true;
        }

        return $this->tenant_id === $tenant->id;

        
    }


    // 2. Ajouter la méthode requise par l'interface
    /*
    public function canAccessPanel(Panel $panel): bool
    {
        // Ici, vous définissez qui peut entrer. 
        // Exemple : seulement les emails finissant par @votre-domaine.com
        // Ou plus simplement pour le moment :
        return true; 
    }
    */
    

    // app/Models/User.php

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'supramanager' => $this->role === 'superadmin',
            'management'   => in_array($this->role, ['admin', 'developer', 'viewer']),
            default        => false,
        };
    }



    ///
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDeveloper(): bool
    {
        return $this->role === 'developer';
    }

    public function isViewer(): bool
    {
        return $this->role === 'viewer';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function canManageApps(): bool
    {
        return in_array($this->role, ['admin', 'developer']);
    }

    public function canManageTeam(): bool
    {
        return $this->role === 'admin';
    }



}
