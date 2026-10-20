<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    protected $signature = 'btqx:create-admin {email} {--name=admin}';

    protected $description = '创建平台管理员，密码通过隐藏提示输入';

    public function handle(): int
    {
        $password = $this->secret('管理员密码（至少 12 位）');
        $data = ['email' => $this->argument('email'), 'password' => $password];
        $validator = Validator::make($data, ['email' => 'required|email|unique:users,email', 'password' => 'required|string|min:12']);
        if ($validator->fails()) {
            $this->error($validator->errors()->first());

            return self::FAILURE;
        }
        $user = new User;
        $user->name = $this->option('name');
        $user->email = $data['email'];
        $user->username = $this->option('name');
        $user->password = $password;
        $user->is_admin = true;
        $user->role = 'super_admin';
        $user->save();
        $this->info('管理员已创建：'.$user->email);

        return self::SUCCESS;
    }
}
