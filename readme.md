# laravel-api-auth
laravel API 鉴权

这是一个 laravel 的 API 鉴权包， `laravel-api-auth` 采用的是密钥加密，只要客户端不被反编译从而泄露密钥，该鉴权方式理论上来说是安全的(不考虑量子计算机出现的可能性)。

## 安装  
```bash
composer require 96qbhy/laravel-api-auth
```

## 配置
1. 注册 `ServiceProvider`: 
    ```php
    Qbhy\LaravelApiAuth\ServiceProvider::class,
    ```
    > laravel 5.5+ 版本不需要手动注册

2. 发布配置文件
    ```php
    php artisan vendor:publish --provider="Qbhy\LaravelApiAuth\ServiceProvider"
    ```

3. 在 `App\Http\Kernal` 中注册中间件 
    ```php
    protected $routeMiddleware = [
        'api_auth' => Qbhy\LaravelApiAuth\LaravelApiAuthMiddleware::class,
    ];
    ```
    
4. 添加 `role` 
    ```php
    php artisan api_auth
    ```
    然后按照格式把 `access_key` 和 `secret_key` 添加到, `config/api_auth.php` 里面的 `roles` 中。

5. 自定义签名方法  
    `config/api_auth.php` 中的 `encrypting` 可以修改为自定义的签名函数，该函数将传入三个参数: 密钥: `$secret_key`、随机字符串: `$echostr`、时间戳: `$timestamp`，返回签名后的字符串。该函数默认为: 
    ```php
    /**
     * @param $secret_key
     * @param $echostr
     * @param $timestamp
     * @return string
     */
    function encrypting($secret_key, $echostr, $timestamp) {
        return md5($secret_key . $echostr . $timestamp);
    }
    ```
6. 自定义签名校验规则
    `config/api_auth.php` 中的 `rule` 可以修改为自定义的校验函数，该函数将传入三个参数: 密钥: `$secret_key`、客户端签名: `$signature`、服务端签名: `$serverSignature`，必须返回布尔值。该函数默认为: 
     ```php
     /**
      * @param $secret_key
      * @param $signature
      * @param $serverSignature
      * @return bool
      */
     function rule($secret_key, $signature, $serverSignature)
     {
         return $signature === $serverSignature;
     }
     ```
7. 自定义错误处理
    `config/api_auth.php` 中的 `error_handler` 可以修改为自定义的错误处理函数，该函数将传入两个参数: 请求: `$request`、错误码: `$code`。该函数默认为: 
     ```php
     /**
      * @param Request $request
      * @param int $code
      * @return \Illuminate\Http\JsonResponse
      */
     function error_handler($request, $code)
     {
         return response()->json([
             'msg' => 'Forbidden',
             'code' => $code
         ], 403);
     }  
     ```
     `$code` 可能是以下几个值中的一个:
     * `LaravelApiAuthMiddleware::LACK_HEADER` -> 缺少请求头。
     * `LaravelApiAuthMiddleware::ACCESS_KEY_ERROR` -> `access_key` 错误。
     * `LaravelApiAuthMiddleware::SIGNATURE_ERROR` -> 签名错误。
     * `LaravelApiAuthMiddleware::SIGNATURE_LAPSE` -> 签名失效，客户端签名时间和服务端签名时间差超过设置的 `timeout` 值。
     * `LaravelApiAuthMiddleware::SIGNATURE_REPETITION` -> 签名重复，规定时间内出现两次或以上相同的签名。
     
     
## 使用  
### 路由中
```php
Route::group(['middleware'=>'api_auth'], function(){
    // routes...
});

\\ or

Route::get('api/example', function(){
    // todo...
})->middleware(['api_auth']);
```

### 前端
```javascript 1.8

const access_key = '{access_key}';  // 服务端生成的 access_key
const secret_key = '{secret_key}';  // 服务端生成的 secret_key

const timestamp = Date.parse(new Date()) / 1000;    // 取时间戳
const echostr = 'asldjaksdjlkjgqpojg64131321';      // 随机字符串自行生成

function encrypting(secret_key, echostr, timestamp){
    return md5(secret_key + echostr + timestamp);    // md5 库自行引入
}

const requestConfig = {
    headers: {
        "api-signature": encrypting(secret_key, echostr, timestamp),
        "api-echostr": echostr,
        "api-timestamp": timestamp,
        "api-access-key": access_key
    }
};
axios.post('/api/example',{},requestConfig).then(res=>{
    // todo
});
```
> 本例子为 `web` 前端的例子，其他客户端同理，生成签名并且带上指定参数即可正常请求。
> 通过自定义签名方法和自定义校验方法，可以使用其他加密方法进行签名，例如 `哈希` 等其他加密算法。更多自定义可以直接复制 `Qbhy\LaravelApiAuth\LaravelApiAuthMiddleware` 中间件后自行修改 。有问题请开 `issue` 。


[96qbhy.com](https://96qbhy.com)  
96qbhy@gmail.com
