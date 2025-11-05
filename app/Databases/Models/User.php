<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'phone',
        'avatar',
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

    /**
     * Relacionamento com Jogador
     */
    public function jogador()
    {
        return $this->hasOne(Jogador::class);
    }

    /**
     * Relacionamento com Professor
     */
    public function professor()
    {
        return $this->hasOne(Professor::class);
    }

    /**
     * Relacionamento com Arena
     */
    public function arena()
    {
        return $this->hasOne(Arena::class);
    }

    /**
     * Verifica se o usuário é jogador
     */
    public function isJogador(): bool
    {
        return $this->user_type === 'jogador';
    }

    /**
     * Verifica se o usuário é professor
     */
    public function isProfessor(): bool
    {
        return $this->user_type === 'professor';
    }

    /**
     * Verifica se o usuário é arena
     */
    public function isArena(): bool
    {
        return $this->user_type === 'arena';
    }
}
