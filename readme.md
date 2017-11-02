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
    `config/api_auth.php` 中的 `encrypting` 可以修改为自定义的签名函数，该函数将传入三个参数: 密钥、随机字符串、时间戳。该函数默认为: 
    ```php
        'encrypting' => function ($secret_key, $echostr, $timestamp) {
            return md5($secret_key . $echostr . $timestamp);
        },
    ```
    
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
> 更多自定义可以直接复制 `Qbhy\LaravelApiAuth\LaravelApiAuthMiddleware` 中间件后自行修改 。有问题请开 `issue` 。


[96qbhy.com](https://96qbhy.com)  
96qbhy@gmail.com
