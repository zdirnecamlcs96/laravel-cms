<?php


namespace  Local\CMS\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Response extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
