<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\CustomResetPassword;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail, HasMedia
{

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasRoles, HasApiTokens, HasFactory, Notifiable, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'google_id',
        'name',
        'username',
        'info',
        'email',
        'password',
        'email_verified_at',
        'last_activity',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'media',
    ];

    public $appends = [
        'role',
        'image',
        'is_blocked',
    ];

//    protected $with = ['settings'];

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

    public function chat(): HasOne
    {
        return $this->hasOne(UserBot::class);
    }

    public function mergeCode(): HasOne
    {
        return $this->hasOne(BotMergeCode::class);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function getRoleAttribute(): ?string
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles->pluck('name')->first();
        }

        return null;
    }

    public function links(): MorphMany
    {
        return $this->morphMany(Links::class, 'model');
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new CustomVerifyEmail($this));
    }

    public function verification(): HasMany
    {
        return $this->hasMany(UserVerify::class);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new CustomResetPassword($token));
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class);
    }

    public function bands(): HasMany
    {
        return $this->hasMany(Band::class);
    }

    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class);
    }

    public function settings(): HasOne
    {
        return $this->hasOne(UserSettings::class);
    }

    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_user_favorites')
            ->withTimestamps();
    }

    public function blockedRecord(): HasOne
    {
        return $this->hasOne(BlockedUser::class);
    }

    public function getIsBlockedAttribute(): bool
    {
        return $this->blockedRecord()->exists();
    }

    public function getImageAttribute(): ?array
    {
        $media = $this->getMedia('images')->first();

        if (!$media) {
            return null;
        }

        return [
            'id' => $media->id,
            'thumb' => $media->getUrl('thumb'),
        ];
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->nonQueued();

        $this->addMediaConversion('large')
            ->width(1920)
            ->quality(90);
    }
}
