<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Limpando dados antigos...');
        
        // Disable foreign keys to allow truncating
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('appointment_product')->truncate();
        Appointment::truncate();
        Product::truncate();
        Service::truncate();
        Customer::truncate();
        User::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Semeando Equipe (Barbeiros e Admins)...');

        // 1. Criar Equipe (Usuários)
        $admin = User::create([
            'name' => 'Admin Barcelona',
            'cpf' => '11111111111',
            'whatsapp' => '37998220372',
            'password' => Hash::make('111111'),
            'roles' => ['admin', 'barber'],
            'commission_type' => 'percentage',
            'commission_value' => 50,
            'is_active' => true,
            'work_start_time' => '09:00:00',
            'work_end_time' => '19:00:00',
        ]);

        // 2. Criar Serviços
        $servicesData = [
            ['name' => 'Corte Degradê (Fade)', 'price' => 45.00, 'duration' => 45, 'desc' => 'Corte moderno com transição perfeita.'],
            ['name' => 'Corte Social', 'price' => 35.00, 'duration' => 30, 'desc' => 'Corte tradicional à tesoura e máquina.'],
            ['name' => 'Barba Terapia', 'price' => 30.00, 'duration' => 30, 'desc' => 'Alinhamento com toalha quente e balm.'],
            ['name' => 'Combo: Corte + Barba', 'price' => 70.00, 'duration' => 75, 'desc' => 'Tratamento completo.'],
            ['name' => 'Sobrancelha (Navalha)', 'price' => 15.00, 'duration' => 15, 'desc' => 'Limpeza e desenho na navalha.'],
            ['name' => 'Platinado / Luzes', 'price' => 90.00, 'duration' => 120, 'desc' => 'Descoloração completa.'],
            ['name' => 'Corte Infantil (até 10 anos)', 'price' => 40.00, 'duration' => 45, 'desc' => 'Corte com paciência e diversão.'],
        ];

        $services = [];
        foreach ($servicesData as $s) {
            $services[] = Service::create([
                'name' => $s['name'],
                'description' => $s['desc'],
                'price' => $s['price'],
                'duration_minutes' => $s['duration'],
                'is_active' => true,
            ]);
        }
    }
}
