<?php

return [
    "blog" => [
        "model" => \Local\CMS\Models\Blog::class,

        "table" => "blogs"
    ],
    "media" => [
        "model" => \Local\CMS\Models\Media::class,

        "table" => "media"
    ],

    "asset_url" => "/vendor/cms",

    "superadmins" => env('CMS_SUPERADMINS')
];
