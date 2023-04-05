<?php

namespace Local\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Packages\ProductSlug\HasSlug;

class Blog extends Model
{
    use SoftDeletes, HasSlug;

    protected $guarded = [
        "id", "created_at", "updated_at"
    ];

    /**
     * RELATIONSHIPS
     */
    public function images()
    {
        return $this->morphMany(ImagePortfolio::class, 'imageable');
    }

    public function thumbnail()
    {
        return $this
            ->morphOne(Media::class, 'imageable')
            ->ofMany(['created_at' => 'max'], fn($query) => $query->where('type', Media::PROFILE_ATTACHMENT));
    }
}
