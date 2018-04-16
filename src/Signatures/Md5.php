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