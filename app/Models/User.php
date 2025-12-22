<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Rôles disponibles dans l'application
     */
    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';

    /**
     * Liste de tous les rôles valides
     */
    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_USER,
    ];

    /**
     * Rôle par défaut pour les nouveaux utilisateurs
     */
    public const DEFAULT_ROLE = self::ROLE_USER;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Vérifier si l'utilisateur a un ou plusieurs rôles
     *
     * @param array|string $roles Rôle(s) à vérifier
     * @return bool
     */
    public function hasRole(array|string $roles): bool
    {
        $roles = collect(Arr::wrap($roles))
            ->map(fn ($role) => strtolower($role))
            ->all();

        $currentRole = strtolower($this->role ?? self::DEFAULT_ROLE);

        return in_array($currentRole, $roles, true);
    }

    /**
     * Vérifier si l'utilisateur est administrateur
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * Vérifier si l'utilisateur est un utilisateur standard
     *
     * @return bool
     */
    public function isUser(): bool
    {
        return $this->hasRole(self::ROLE_USER);
    }

    /**
     * Vérifier si le rôle est valide
     *
     * @param string $role
     * @return bool
     */
    public static function isValidRole(string $role): bool
    {
        return in_array(strtolower($role), self::ROLES, true);
    }

    /**
     * Scope pour filtrer les administrateurs
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', self::ROLE_ADMIN);
    }

    /**
     * Scope pour filtrer les utilisateurs standards
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsers($query)
    {
        return $query->where('role', self::ROLE_USER);
    }
}
