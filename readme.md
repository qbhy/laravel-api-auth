# laravel-api-auth
laravel API 鉴权

这是一个 laravel 的 API 鉴权包， `laravel-api-auth` 采用 `jwt token` 的鉴权方式，只要客户端不被反编译从而泄露密钥，该鉴权方式理论上来说是安全的。
PS: web 前端 API 没有绝对的安全，该项目的本意是给不暴露源码的客户端提供一种鉴权方案(如 service、APP客户端)。

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
        'api_auth' => \Qbhy\LaravelApiAuth\Middleware::class,
        // other ...
    ];
    ```
    
4. 添加 `role` 
    ```php
    php artisan api_auth
    ```
    然后按照格式把 `access_key` 和 `secret_key` 添加到, `config/api_auth.php` 里面的 `roles` 数组中。
    ```php
    'roles' => [
        '{access_key}' => [
            'name' => '{role_name}',        // 角色名字，例如 android
            'secret_key' => '{secret_key}',
        ],
    ],
    ```

5. 自定义签名方法 (可选)
    `config/api_auth.php` 中的 `signature_methods` 可以添加自定义的签名类，该类需要继承自 `Qbhy\LaravelApiAuth\Signatures\SignatureInterface` 接口 
    ```php
   <?php
    /**
     * User: 96qbhy
     * Date: 2018/4/16
     * Time: 下午3:22
     */
    
    namespace Qbhy\LaravelApiAuth\Signatures;
    
    
    class Md5 implements SignatureInterface
    {
        public static function sign(string $string, string $secret): string
        {
            return md5($string . $secret);
        }
    
        public static function check(string $string, string $secret, string $signature): bool
        {
            return static::sign($string, $secret) === $signature;
        }
    
    }
    ```
7. 自定义错误处理
    token 校验不通过的情况下会抛异常，请在 `Handler` 捕获后自行处理。
    目前有三种异常 ： 
    1. AccessKeyException
    2. InvalidTokenException
    3. SignatureMethodException
     
## 使用  
### 路由中
```php
Route::get('api/example', function(Request $request){
    // $request->get('client_role');
    // todo...
})->middleware(['api_auth']);

\\ or

Route::group(['middleware'=>'api_auth'], function(){
    // routes...
});
```
> 通过验证后 `$request` 会添加一个 `client_role` 字段，该字段为客户端的角色名称。

### 前端
```javascript
import axios from 'axios';
import { Base64 } from 'js-base64';

const access_key = '{access_key}';  // 服务端生成的 access_key
const secret_key = '{secret_key}';  // 服务端生成的 secret_key

const timestamp = Date.parse(new Date()) / 1000;    // 取时间戳
const echostr = 'asldjaksdjlkjgqpojg64131321';      // 随机字符串自行生成

const header = Base64.encode(JSON.stringify({
                   "alg": "md5",
                   "type": "jwt"
               }));
const payload = Base64.encode(JSON.stringify({
                 "timestamp": timestamp,
                 "echostr": echostr,
                 "ak": access_key
             }));
const signature_string = header  + '.' + payload;

function md5Sign(string, secret){
    return md5(string + secret);    // md5 库自行引入
}

const api_token = signature_string + '.' + md5Sign(signature_string,secret_key);

const requestConfig = {
    headers: {
        "api-token": api_token
    }
};

axios.post('/api/example',{},requestConfig).then(res=>{
    // todo
});
```
> 本例子为 `web` 前端的例子，其他客户端同理，生成签名并且带上指定参数即可正常请求。
> 通过自定义签名方法和自定义校验方法，可以使用其他加密方法进行签名，例如 `哈希` 等其他加密算法。



[96qbhy.com](https://96qbhy.com)    
96qbhy@gmail.com
