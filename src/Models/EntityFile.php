<?php

namespace Local\CMS\Models;

use App\Traits\Sequenceable;
use Illuminate\Database\Eloquent\Model;

class EntityFile extends Model
{
    use Sequenceable;
    const MORPHABLE = "entitlable";
    protected static $sequenceable = 'sequence';

    protected $guarded = [];
}
