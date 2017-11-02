<?php

return [
    'status' => 'on',                       // 状态，on 或者 off
    'roles' => [
        '{access_key}' => [
            'name' => '{role_name}',        // 角色名字，例如 android
            'secret_key' => '{secret_key}',
        ],
    ],
    'timeout' => 30,                        // 签名失效时间，单位: 秒
    'encrypting' => function ($secret_key, $echostr, $timestamp) {
        return md5($secret_key . $echostr . $timestamp);
    },
];