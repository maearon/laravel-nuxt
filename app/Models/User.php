<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    // Virtual attributes
    protected $activation_token, $reset_token;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'password',
        'activation_token', // Include activation_token in fillable array
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
        'password',
        'remember_token',
        'email_verified_at',
    ];

    /**
     * Set the activation token attribute and hash it into activation_digest.
     *
     * @param string $value
     */
    public function setActivationTokenAttribute($value)
    {
        $this->attributes['activation_token'] = $value;
        $this->attributes['activation_digest'] = Hash::make($value);
    }

    /**
     * Get the activation token attribute.
     *
     * @return string|null
     */
    public function getActivationTokenAttribute()
    {
        return $this->activation_token;
    }

    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (User $user) {
            $user->generateActivationToken();
        });
    }

    /**
     * Generate activation token and digest.
     */
    public function generateActivationToken()
    {
        $this->activation_token = Str::random(40);
        $this->activation_digest = Hash::make($this->activation_token);
    }

    /**
     * Activate the user account.
     */
    public function activate()
    {
        $this->update([
            'activated' => true,
            'activated_at' => now(),
        ]);
    }

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
            'has_password' => 'boolean',
        ];
    }

    /**
     * Define a relationship with user providers.
     *
     * @return HasMany
     */
    public function userProviders(): HasMany
    {
        return $this->hasMany(UserProvider::class);
    }

    /**
     * Determine if the user must verify their email.
     *
     * @return bool
     */
    public function mustVerifyEmail(): bool
    {
        return $this instanceof MustVerifyEmail && !$this->hasVerifiedEmail();
    }
}
