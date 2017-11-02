<?php

namespace Qbhy\LaravelApiAuth;

use Illuminate\Console\Command as LaravelCommand;

class Command extends LaravelCommand
{
    /**
     * 控制台命令 signature 的名称。
     *
     * @var string
     */
    protected $signature = 'api_auth';

    /**
     * 控制台命令说明。
     *
     * @var string
     */
    protected $description = '生成随机 access_key 和 secret_key 。';

    /**
     * 执行控制台命令。
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('access_key: ' . str_rand());
        $this->info('secret_key: ' . str_rand());
    }
}