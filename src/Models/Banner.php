<?php

namespace Local\CMS\Models;

use App\Traits\Sequenceable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Banner extends Model
{
    use SoftDeletes, Sequenceable, LogsActivity;


    const DISPLAY_IN_MOBILE = 'mobile';
    const DISPLAY_IN_WEB = 'web';
    const DISPLAY_IN_BOTH = 'both';


    protected $fillable = [
        "title", "desc", "link", "sequence", "active","display_in"
    ];

    protected $appends = [ 'thumbnail_file'];

    protected static $sequenceable = 'sequence';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
        // Chain fluent methods for configuration options
    }

    /**
     * RELATIONSHIPS
     */
    public function files()
    {
        return $this->morphToMany(File::class, EntityFile::MORPHABLE, 'entity_files');
    }

    public function thumbnail_file()
    {
        return $this->files()->where('zone', 'banner-cover')->first();
    }


    /**
     * ATTRIBUTES
     */

    public function getFilePathAttribute()
    {
        return optional($this->files()->first())->full_path;
    }

    public function getThumbnailFileAttribute()
    {
        return $this->thumbnail_file();
    }

    public function getThumbnailAttribute()
    {
        return optional($this->files()->where('zone', 'banner-cover')->first())->full_path;
    }

    /**
     * Scope
     */

     public function scopeMobileOnly($query)
     {
        return $query->where('display_in',self::DISPLAY_IN_MOBILE)->orWhere('display_in',self::DISPLAY_IN_BOTH);
     }

     public function scopeWebOnly($query)
     {
        return $query->where('display_in',self::DISPLAY_IN_WEB)->orWhere('display_in',self::DISPLAY_IN_BOTH);
     }
}
