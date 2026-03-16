<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Traits\UseUuidTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Settings\Entities\City;
use Modules\Settings\Entities\Country;
use Modules\Settings\Entities\Language;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens;
    use HasFactory;
    use UseUuidTrait;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MEDIA_PROFILE_IMAGES)
            ->useFallbackUrl(DEFAULT_PROFILE_IMAGE_PATH)
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg'])
            ->singleFile();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, UsersRole::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'users_permissions');
    }

    public function usersPermissions()
    {
        return $this->hasMany(UsersPermission::class, 'user_id', 'id');
    }

    public function languages(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language', 'id');
    }

    public function countryRelation(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country', 'id');
    }
}
