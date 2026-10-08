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

        $barbers = [];
        $barbersData = [
            ['name' => 'Marcos "Navalha" Silva', 'cpf' => '22222222222'],
            ['name' => 'João Fade', 'cpf' => '33333333333'],
            ['name' => 'Pedro Tesoura', 'cpf' => '44444444444'],
            ['name' => 'Lucas Barbeiro', 'cpf' => '55555555555'],
        ];

        foreach ($barbersData as $b) {
            $barbers[] = User::create([
                'name' => $b['name'],
                'cpf' => $b['cpf'],
                'whatsapp' => '37998220372',
                'password' => Hash::make(substr($b['cpf'], 0, 6)),
                'roles' => ['barber'],
                'commission_type' => 'percentage',
                'commission_value' => 50,
                'is_active' => true,
                'work_start_time' => '09:00:00',
                'work_end_time' => '19:00:00',
            ]);
        }
        $allBarbers = array_merge([$admin], $barbers);

        $this->command->info('Semeando Serviços...');

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

        $this->command->info('Semeando Produtos...');

        // 3. Criar Produtos
        $productsData = [
            ['name' => 'Pomada Modeladora Matte', 'price' => 45.00, 'stock' => 20],
            ['name' => 'Pomada Teia (Brilho)', 'price' => 40.00, 'stock' => 15],
            ['name' => 'Óleo para Barba (Blend)', 'price' => 35.00, 'stock' => 10],
            ['name' => 'Minoxidil 5% (Crescimento)', 'price' => 85.00, 'stock' => 8],
            ['name' => 'Gel Fixador Cola', 'price' => 20.00, 'stock' => 30],
            ['name' => 'Pós Barba Refrescante', 'price' => 28.00, 'stock' => 12],
        ];

        $products = [];
        foreach ($productsData as $p) {
            $products[] = Product::create([
                'name' => $p['name'],
                'price' => $p['price'],
                'stock_quantity' => $p['stock'],
                'is_active' => true,
            ]);
        }

        $this->command->info('Semeando Clientes...');

        // 4. Criar Clientes (Nomes Reais BR)
        $nomes = ['Matheus', 'Lucas', 'Gabriel', 'Enzo', 'Miguel', 'Arthur', 'Davi', 'Pedro', 'Bernardo', 'Heitor', 'Rafael', 'João', 'Felipe', 'Nicolas', 'Samuel', 'Diego', 'Bruno', 'Thiago', 'Carlos', 'Eduardo', 'Marcelo', 'Leandro', 'Rodrigo', 'Fernando', 'Ricardo', 'Vinicius', 'Gustavo', 'Henrique', 'Caio', 'Igor'];
        $sobrenomes = ['Silva', 'Santos', 'Oliveira', 'Souza', 'Rodrigues', 'Ferreira', 'Alves', 'Pereira', 'Lima', 'Gomes', 'Costa', 'Ribeiro', 'Martins', 'Carvalho', 'Almeida', 'Lopes', 'Soares', 'Fernandes', 'Vieira', 'Barbosa', 'Rocha', 'Dias'];

        $customers = [];
        for ($i = 0; $i < 30; $i++) {
            $nome = $nomes[array_rand($nomes)];
            $sobrenome = $sobrenomes[array_rand($sobrenomes)];
            $cpf = sprintf('%03d%03d%03d%02d', rand(100, 999), rand(100, 999), rand(100, 999), rand(10, 99));
            $whatsapp = '37998220372';

            $customers[] = Customer::create([
                'name' => "$nome $sobrenome",
                'cpf' => $cpf,
                'whatsapp' => $whatsapp,
            ]);
        }

        $this->command->info('Semeando Agendamentos...');

        // 5. Criar Agendamentos
        // Vamos gerar para Ontem, Hoje e Amanhã
        $dates = [
            Carbon::yesterday(),
            Carbon::today(),
            Carbon::tomorrow(),
        ];

        foreach ($dates as $date) {
            $isPast = $date->isPast();
            $isToday = $date->isToday();
            
            // 10-15 agendamentos por dia
            $numAppointments = rand(10, 15);
            
            for ($i = 0; $i < $numAppointments; $i++) {
                $service = $services[array_rand($services)];
                $barber = $allBarbers[array_rand($allBarbers)];
                $customer = $customers[array_rand($customers)];
                
                // Horário aleatório entre 09:00 e 18:00
                $startHour = rand(9, 18);
                $startMin = (rand(0, 1) == 0) ? '00' : '30';
                
                $startTimeStr = sprintf('%02d:%s:00', $startHour, $startMin);
                $startTime = Carbon::parse($date->format('Y-m-d') . ' ' . $startTimeStr);
                $endTime = $startTime->copy()->addMinutes($service->duration_minutes);

                // Determinar o status
                $status = 'scheduled';
                if ($isPast) {
                    $status = rand(1, 10) > 2 ? 'completed' : 'canceled'; // 80% completado, 20% cancelado
                } elseif ($isToday) {
                    $now = Carbon::now();
                    if ($endTime->isBefore($now)) {
                        $status = rand(1, 10) > 1 ? 'completed' : 'canceled';
                    } elseif ($startTime->isBefore($now) && $endTime->isAfter($now)) {
                        $status = 'in_progress';
                    } else {
                        // futuro de hoje
                        $status = rand(1, 10) > 9 ? 'canceled' : 'scheduled';
                    }
                }

                $commission = ($service->price * $barber->commission_value) / 100;
                
                $paymentMethod = null;
                $amountPaid = null;
                if ($status === 'completed') {
                    $methods = ['pix', 'credit_card', 'debit_card', 'cash'];
                    $paymentMethod = $methods[array_rand($methods)];
                    $amountPaid = $service->price;
                }

                $appt = Appointment::create([
                    'customer_id' => $customer->id,
                    'user_id' => $barber->id,
                    'service_id' => $service->id,
                    'appointment_date' => $date->format('Y-m-d'),
                    'start_time' => $startTime->format('H:i:s'),
                    'end_time' => $endTime->format('H:i:s'),
                    'status' => $status,
                    'payment_method' => $paymentMethod,
                    'amount_paid' => $amountPaid,
                    'discount_amount' => 0,
                    'change_amount' => 0,
                    'commission_amount' => $commission,
                ]);

                // Adicionar produtos aleatoriamente (apenas para concluídos, 30% de chance)
                if ($status === 'completed' && rand(1, 10) > 7) {
                    $product = $products[array_rand($products)];
                    $appt->products()->attach($product->id, [
                        'quantity' => 1,
                        'unit_price' => $product->price,
                    ]);
                    // Atualiza o valor total pago
                    $appt->update([
                        'amount_paid' => $appt->amount_paid + $product->price
                    ]);
                }
            }
        }

        $this->command->info('Banco de dados semeado com sucesso! Dados reais simulados foram inseridos.');
        $this->command->info('--- LOGIN DE ACESSO ---');
        $this->command->info('Admin: CPF 11111111111 | Senha: 111111');
        $this->command->info('Barbeiro: CPF 22222222222 | Senha: 222222');
    }
}
