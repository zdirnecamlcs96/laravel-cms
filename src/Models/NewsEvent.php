<?php

namespace Local\CMS\Models;

use App\Traits\Sequenceable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Packages\ProductSlug\HasSlug;
use Carbon\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class NewsEvent extends Model
{
    use HasFactory, SoftDeletes, HasSlug,Sequenceable,LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
        // Chain fluent methods for configuration options
    }

    const SLUGNAME = 'title';

    protected static $sequenceable = 'position';

    protected $fillable = [
        "title", "description", "categories" , "position","status","display_date","last_modified"
    ];

    public function files()
    {
        return $this->morphToMany(File::class, EntityFile::MORPHABLE, 'entity_files');
    }

    public function thumbnail_file()
    {
        return $this->files()->where('zone', 'thumbnail')->first();
    }

    public function banner_file()
    {
        return $this->files()->where('zone', 'banner')->first();
    }

    /**
     * ATTRIBUTES
     */
    public function getThumbnailAttribute()
    {
        return optional($this->files()->where('zone', 'thumbnail')->first())->full_path;
    }

    public function getDisplayDateAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }
    public function getBannerAttribute()
    {
        return optional($this->files()->where('zone', 'banner')->first())->full_path;
    }

    public function getStatusDisplayAttribute()
    {
        if($this->status == 1){
            return 'Active';
        }else{
            return 'Inactive';
        }

    }

    public function getLastModifiedAttribute()
    {
        return $this->activities()->with('causer')->latest()->first();
    }

    public static function updatePosition($original,$newsEvent,$new){
        //position cannot set too far
        $max = NewsEvent::where('id','!=',$newsEvent->id)->orderBy('position','desc')->value('position') ?? 0;

        if($new && $max > $new){
            $newsEvent->position = $new;
            $newsEvent->save();

        }
        elseif($max < $newsEvent->position){
            $newsEvent->position = $max+1;
            $newsEvent->save();
        }

        $new = $newsEvent->position;
        //move biggest number to right
        if($new && ($original == null || $original > $new) && ($new != $original)){
            $move_right = NewsEvent::where('position','>=',$new)
            ->when($original, fn ($query) => $query->where('position','<',$original))
            ->where('id','!=',$newsEvent->id)->increment('position',1);
        }

        //if the position is edit , need to adjust for affected position
        if($original && ($original < $new) && ($original != $new)){
            $move_left = NewsEvent::where('position','>',$original)->where('position','<=',$new)->where('id','!=',$newsEvent->id)->decrement('position',1);
        }



        return true;
    }

}
