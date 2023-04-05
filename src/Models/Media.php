<?php

namespace Local\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use SoftDeletes;

    protected $guarded = [
        "id", "created_at", "updated_at"
    ];

    /**
     * RELATIONSHIPS
     */
    public function file()
    {
        return $this->morphToMany(File::class, EntityFile::MORPHABLE, 'entity_files')->latestOfMany();
    }

    /**
     * ATTRIBUTES
     */
    public function getFilePathAttribute()
    {
        return $this->file?->full_path;
    }
}
