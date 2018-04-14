<?php

use Qbhy\LaravelApiAuth\Middleware;

return [
    'status' => Middleware::STATUS_ON, // 状态，LaravelApiAuth::STATUS_ON  或者 LaravelApiAuth::STATUS_OFF

    'roles' => [
        //        '{access_key}' => [
        //            'name' => '{role_name}',        // 角色名字，例如 android
        //            'secret_key' => '{secret_key}',
        //        ],
    ],

    'auth_info_getter' => [Middleware::class, 'auth_info_getter'],

    'excludes' => [
        'handler' => [Middleware::class, 'excludes_handler'],
        'urls' => [],
    ],

    'timeout' => 60, // 签名失效时间，单位: 秒

    'encrypting' => [Middleware::class, 'encrypting'], // 自定义签名方法

    'rule' => [Middleware::class, 'rule'], // 判断签名正确的规则，默认是相等

    'error_handler' => [Middleware::class, 'error_handler'], // 签名错误处理方法。
];