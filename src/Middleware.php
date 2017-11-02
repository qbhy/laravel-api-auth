<?php

namespace Qbhy\LaravelApiAuth;

use Closure;
use RuntimeException;

class Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (config('api_auth.status') === 'on') {
            $accessKey = $request->header('api-access-key');
            $timestamp = $request->header('api-timestamp');
            $echostr = $request->header('api-echostr');
            $signature = $request->header('api-signature');

            if (empty($timestamp) || empty($accessKey) || empty($echostr) || empty($signature)) {
                return $this->error();      // 缺少请求头
            }

            $roles = config('api_auth.roles');
            if (!isset($roles[$accessKey])) {
                return $this->error();      // access_key 不存在
            }

            $encrypting = config('api_auth.encrypting');
            if (!is_callable($encrypting)) {
                throw new RuntimeException('config("api_auth.encrypting") is not function !');
            }

            $serveSign = call_user_func_array($encrypting, [$roles[$accessKey]['secret_key'], $echostr, $timestamp]);

            if ($serveSign !== $signature) {
                return $this->error();  // 签名不一致
            }

            $timeout = config('api_auth.timeout', 30);
            if (time() - $timestamp > $timeout) {
                return $this->error();      // 签名失效
            }

            if (!is_null(cache()->pull('api_auth:' . $signature))) {
                return $this->error();      // 签名失效
            } else {
                cache()->put('api_auth:' . $signature, $request->getClientIp(), $timeout / 60);
            }

        }
        return $next($request);
    }

    public function error($msg = 'Forbidden', $code = 403)
    {
        return response()->json(compact('msg', 'code'), $code);
    }
}