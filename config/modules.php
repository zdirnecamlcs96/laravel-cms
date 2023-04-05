<?php

return [
    "admin_url" => env('ADMIN_URL', 'http://localhost'),

    "admin_management" => env('ADMIN_MANAGEMENT', true),
    "role_management" => env('ROLE_MANAGEMENT', true),

    "permissions" => [
        "role" => [
            "create",
            "read",
            "update",
            "delete"
        ],
        "admin" => [
            "create",
            "read",
            "update",
            "delete"
        ],
        "user" => [
            "create",
            "read",
            "update",
            "delete"
        ],
        "file" => [
            "create",
            "read",
            "update",
            "delete"
        ],
        "banner" => [
            "create",
            "read",
            "update",
            "delete"
        ],
        "news_event" => [
            "create",
            "read",
            "update",
            "delete"
        ],
        "product" => [
            "create",
            "read",
            "update",
            "delete"
        ],
        "product_category" => [
            "create",
            "read",
            "update",
            "delete"
        ],
        "coupon" => [
            "create",
            "read",
            "update",
            "delete"
        ],
        "order" => [
            "create",
            "read",
            "update",
            "delete"
        ],
        "outlet" => [
            "create",
            "read",
            "update",
            "delete"
        ],
        "booking" => [
            "create",
            "read",
            "update",
            "delete"
        ],
        "tit_tar_tour" => [
            "create",
            "read",
            "update",
            "delete"
        ]
    ]
];
