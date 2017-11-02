# laravel-api-auth
laravel API 鉴权

这是一个 laravel 的 API 鉴权包，不同于 `passport` , `laravel-api-auth` 采用的是密钥加密，只要客户端不被反编译从而泄露密钥，该方式可谓绝对安全(不考虑量子计算机出现的可能性)。

## 安装  
```bash
composer require 96qbhy/laravel-api-auth:dev-master
```

## 配置
1. 注册 `ServiceProvider`: 
    ```php
    Qbhy\LaravelApiAuth\ServiceProvider::class,
    ```
    > laravel 5.5+ 版本不需要手动注册

2. 发布配置文件
    php artisan vendor:publish --provider="Qbhy\LaravelApiAuth\ServiceProvider"

3. 在 `App\Http\Kernal` 中注册中间件 
    ```php
    protected $routeMiddleware = [
        'api_auth' => Qbhy\LaravelApiAuth\Middleware::class,
    ];
    ```
    
4. 添加 `role` 
    ```php
    php artisan api_auth
    ```
    然后按照格式把 access_key 和 secret_key 添加到, `config/api_auth.php` 里面的 `roles` 中。

[96qbhy.com](https://96qbhy.com)  
96qbhy@gmail.com
