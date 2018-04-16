<?php

namespace Qbhy\LaravelApiAuth\Middleware;

use Closure;
use Illuminate\Http\Request;
use Qbhy\LaravelApiAuth\Exceptions\AccessKeyException;
use Qbhy\LaravelApiAuth\Exceptions\InvalidTokenException;
use Qbhy\LaravelApiAuth\Signatures\SignatureInterface;

class Middleware
{
    const STATUS_ON  = 'on';
    const STATUS_OFF = 'off';

    public function __construct()
    {
        $this->config = config('api_auth');
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->config['status'] === static::STATUS_ON) {

            // 得到 api token
            $token = $request->hasHeader('api-token') ? $request->header('api-token') : $request->get('api-token');

            // 得到 header 、 payload signature 三段字符串
            list($header_string, $payload_string, $signature) = explode(".", $token);

            $roles = $this->config['roles'];

            // 检查参数 --begin
            $header  = @json_decode($header_string, true);
            $payload = @json_decode($payload_string, true);
            if (!is_array($header) ||
                !isset($header['alg']) ||
                !is_array($payload) ||
                !isset($payload['timestamp']) ||
                !isset($payload['echostr']) ||
                !isset($payload['ak'])
            ) {
                throw new InvalidTokenException('invalid token !');
            }

            if (!isset($roles[$payload['ak']])) {
                throw new AccessKeyException('access key invalid !');
            }

            if (!isset($this->config['signature_methods'][$header['alg']])) {
                throw new AccessKeyException($header['alg'] . ' signatures are not supported !');
            }
            // 检查参数 --end

            $role = $roles[$payload['ak']];

            /** @var SignatureInterface $signature_method */
            $signature_method = $this->config['signature_methods'][$header['alg']];
            // 检查签名是否正确
            if ($signature_method::check($header_string . $payload_string, $role['secret_key'], $signature)) {
                throw new InvalidTokenException('invalid token !');
            }

            // 添加 role_name 到 $request 中
            if ($request->has('client_role')) {
                $request->offsetSet('_client_role', $request->get('client_role'));
            }
            $request->offsetSet('client_role', $role['name']);

            // 添加 api_payload 到 $request 中
            if ($request->has('api_payload')) {
                $request->offsetSet('_api_payload', $request->get('api_payload'));
            }
            $request->offsetSet('api_payload', $payload);
        }

        return $next($request);
    }


    /**
     * @param Request $request
     * @param array   $urls
     *
     * @return bool
     */
    public static function excludes_handler(Request $request, array $urls = [])
    {
        if (in_array($request->url(), $urls)) {
            return true;
        }

        return false;
    }

}