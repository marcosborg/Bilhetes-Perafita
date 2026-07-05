<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-user
        {--name=Admin}
        {--email=admin@example.com}
        {--password=password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria ou atualiza um utilizador administrador do Filament';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::updateOrCreate(
            ['email' => (string) $this->option('email')],
            [
                'name' => (string) $this->option('name'),
                'responsibility' => 'Administrador',
                'password' => Hash::make((string) $this->option('password')),
            ],
        );

        $this->info("Administrador pronto: {$user->email}");

        return self::SUCCESS;
    }
}
