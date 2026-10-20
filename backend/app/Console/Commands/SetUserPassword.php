<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SetUserPassword extends Command
{
    protected $signature = 'btqx:set-password {username}';

    protected $description = '通过用户名安全重置后台密码';

    public function handle(): int
    {
        $user = User::where('username', $this->argument('username'))->first();
        if (! $user) {
            $this->error('找不到用户：'.$this->argument('username'));

            return self::FAILURE;
        }
        $password = $this->secret('新密码（至少 12 位）');
        if (! is_string($password) || strlen($password) < 12) {
            $this->error('密码至少需要 12 位。');

            return self::FAILURE;
        }
        $user->password = $password;
        $user->save();
        $this->info('密码已更新：'.$user->username);

        return self::SUCCESS;
    }
}
