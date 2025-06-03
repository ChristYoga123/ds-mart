<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class InitUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        User::firstOrCreate([
            'name' => 'admin'
        ], [
            'password' => 'password'
        ])->assignRole('super_admin');

        User::firstOrCreate([
            'name' => 'gudang'
        ], [
            'password' => 'password'
        ])->assignRole('gudang');

        User::firstOrCreate([
            'name' => 'kasir'
        ], [
            'password' => 'password'
        ])->assignRole('kasir');
    }
}
