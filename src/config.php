<?php

use Qbhy\LaravelApiAuth\Middleware\Middleware;

return [
    'status' => Middleware::STATUS_ON, // 状态，LaravelApiAuth::STATUS_ON  或者 LaravelApiAuth::STATUS_OFF

    'roles' => [
        //        '{access_key}' => [
        //            'name' => '{role_name}',        // 角色名字，例如 android
        //            'secret_key' => '{secret_key}',
        //        ],
    ],

    'signature_methods' => [
        'md5' => \Qbhy\LaravelApiAuth\Signatures\Md5::class,
    ],

    'excludes' => [
        'handler' => [Middleware::class, 'excludes_handler'],
        'urls'    => [],
    ],

    'timeout' => 60, // 签名失效时间，单位: 秒
];