<?php

namespace Local\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use SoftDeletes;

    // Path to save
    const PATH_TO_STORAGE = "storage/files/";

    protected $fillable = [
        "name", "original_name", "extension", "mime", "size", "path", "ip_address", "sequence", "status","permission", "low_resolution", "type", "high_resolution","zone"
    ];

    protected $appends = [
        "full_path", "low_resolution_full_path", "high_resolution_full_path"
    ];



    /**
     * RELATIONSHIPS
     */
    public function entitlable()
    {
        return $this->morphedByMany(EntityFile::class, 'entity_files');
    }

    /**
     * Scope
     */

     public function scopeOnlyPublic($query)
     {
        return $query->where('permission','public');
     }


    /**
     * ATTRIBUTES
     */
    function getFullPathAttribute()
    {
        return asset($this->path . $this->name . '.' . $this->extension);
    }

    function getLowResolutionFullPathAttribute()
    {
        return asset($this->path . $this->low_resolution . '.' . $this->extension);
    }

    function getHighResolutionFullPathAttribute()
    {
        return asset($this->path . $this->high_resolution . '.' . $this->extension);
    }
}
