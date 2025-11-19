<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's default users for each role.
     */
    public function run(): void
    {
        $users = [
            [
                'role' => 'Administrador',
                'name' => 'Samuel Supervisor',
                'email' => 'admin@pil.com',
                'username' => 'samuel.admin',
                'password' => 'PilAdmin!1',
            ],
            [
                'role' => 'Vendedor',
                'name' => 'Valeria Ventas',
                'email' => 'ventas@pil.com',
                'username' => 'valeria.ventas',
                'password' => 'PilVendedor!1',
            ],
            [
                'role' => 'Comprador',
                'name' => 'Camila Cliente',
                'email' => 'comprador@pil.com',
                'username' => 'camila.cliente',
                'password' => 'PilComprador!1',
            ],
            [
                'role' => 'Almacén',
                'name' => 'Armando Almacén',
                'email' => 'almacen@pil.com',
                'username' => 'armando.almacen',
                'password' => 'PilAlmacen!1',
            ],
        ];

        foreach ($users as $data) {
            $role = Role::where('name', $data['role'])->first();

            if (! $role) {
                continue;
            }

            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'username' => $data['username'],
                    'password' => Hash::make($data['password']),
                    'role_id' => $role->id,
                ]
            );
        }
    }
}
