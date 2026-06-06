<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
        'bio',
        // E-E-A-T (Schema.org Person)
        'headline',
        'social_x',
        'social_linkedin',
        'social_github',
        'social_youtube',
        'social_web',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /** URL absoluta de la foto del autor (para JSON-LD Person.image). */
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/'.$this->image) : null;
    }

    /** Slug público del autor (para /autor/{slug}). */
    public function getSlugAttribute(): string
    {
        return \Illuminate\Support\Str::slug($this->name);
    }

    /** Redes sociales que tenga rellenas — para Schema.org Person.sameAs. */
    public function sameAs(): array
    {
        return array_values(array_filter([
            $this->social_x,
            $this->social_linkedin,
            $this->social_github,
            $this->social_youtube,
            $this->social_web,
        ]));
    }
}
