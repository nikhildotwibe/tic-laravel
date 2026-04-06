<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BaseModel extends Model implements HasMedia
{
    use InteractsWithMedia;
    use SoftDeletes;
    use HasFactory;
    use HasUuids;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid()->toString();
            // $model->created_by = auth()->user()->id;
            // $model->created_by = Auth::user()->id;
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                // $model->updated_by = auth()->user()->id;
            }
        });

        static::deleting(function ($model) {
            if (auth()->check()) {
                   // $model->deleted_by = auth()->user()->id;
            }
        });
    }

    public function getMediaUrls(string $mediaCollectionName = null): array
    {
        $media = self::getMedia($mediaCollectionName);
        $urls = [];
        foreach ($media as $row) {
            $urls[] = $row->getFullUrl();
        }
        if (empty($urls)) {
            $urls[] = self::getFirstMediaUrl($mediaCollectionName);
        }

        return $urls;
    }
}
