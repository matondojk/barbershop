# Barbearia Barcelona - Sistema de Gestão

Este é um sistema completo de gestão para a Barbearia Barcelona. Foi desenvolvido com o ecossistema Laravel, utilizando o pacote **Laravel Boost**, **Livewire**, e **Tailwind CSS**. A aplicação atende às necessidades de clientes, barbeiros, e administração (caixa).

## Tecnologias e Stack
- **PHP 8.2+**
- **Laravel 11+**
- **Livewire 3** (Componentes reativos sem necessidade de escrever JS puro)
- **Tailwind CSS** (Estilização responsiva e UI moderna)
- **Laravel Sail** (Ambiente de desenvolvimento via Docker)
- **MySQL** (Banco de dados)

## Arquitetura e Papéis (Roles)

O sistema conta com um sistema robusto de papéis, onde cada usuário logado visualiza telas específicas com base em suas permissões:

1. **Cliente (Customer):**
   - **Agendamento Inteligente:** Interface para selecionar barbeiro, corte (serviço), data, e um horário disponível.
   - **Regra de Agendamento:** O agendamento leva em consideração as horas de trabalho *específicas* de cada barbeiro (início e fim do expediente). Se um barbeiro trabalha apenas à tarde, a agenda reflete isso.
   - **Minhas Reservas:** Acesso ao histórico de seus cortes, podendo reagendar ou cancelar.

2. **Barbeiro (Barber):**
   - **Painel 'Minha Agenda':** Dashboard restrito ao barbeiro logado. Mostra apenas os atendimentos *dele* no dia atual.
   - **Controle de Fluxo:** O barbeiro altera o status dos clientes:
     - `Agendado` -> `Em Andamento` (na cadeira) -> `Aguardando Pagamento` (enviado ao caixa).
   - **Dashboard Diário:** Exibe estatísticas de "Total de Clientes", "Finalizados", "Na Fila" e uma estimativa em tempo real da **Comissão Diária** gerada até o momento.
   - Restrição: O barbeiro não tem acesso às páginas de configuração, edição de equipes, serviços ou painel geral de faturamento.

3. **Administrador/Financeiro (Admin):**
   - **Visão Geral (Dashboard):** Tela para a recepção que mostra a agenda e fluxo de *todos* os barbeiros do dia, apenas dos agendamentos ativos.
   - **Frente de Caixa (PDV):** Tela reativa que puxa automaticamente qualquer cliente que esteja "Em Andamento" (na cadeira) ou "Aguardando Pagamento". O caixa finaliza o pagamento, registra o método, calcula o troco automaticamente, e lança a **comissão do barbeiro** no sistema, alterando o status da consulta para `Concluído` (`completed`).
   - **Gestão de Equipe (Collaborators):** CRUD de barbeiros. Define dados de acesso, horários de expediente (início e fim) e **regras de comissão** (percentual ou valor fixo).
   - **Gestão de Serviços (Cortes):** CRUD de serviços, configurando nome, tempo estimado, e preço.
   - **Controle de Acessos:** Apenas a conta de administrador acessa configurações e frente de caixa.

## Regras de Negócio e Cálculos

- **Disponibilidade de Horários:** A disponibilidade para marcar corte verifica o `work_start_time` e `work_end_time` do barbeiro selecionado. Feriados ou tempo já reservado, bloqueiam horários futuros. O sistema leva em consideração o Timezone local (ex: `America/Sao_Paulo`) para desativar os horários de hoje que já passaram na vida real.
- **Limitação de Datas:** O agendamento exibe *apenas* os dias da semana vigente (até sábado) para evitar agendas abertas ad infinitum. No domingo, ele libera os dias da próxima semana.
- **Máscaras e Conversões de Moeda:** Entradas monetárias na UI utilizam JavaScript Vanilla (`keyup` / `input`) para formatar visualmente (`R$ 0,00`). Antes de salvar no banco, o PHP limpa as strings (`str_replace`) convertendo para formato decimal, gravando nativamente como double/decimal no DB.
- **Comissionamento Automático:** 
  - Quando o barbeiro é cadastrado, ele recebe um `commission_type` (`percentage` ou `fixed`) e um `commission_value`.
  - Quando o Admin/Caixa registra o pagamento, o sistema calcula na hora a comissão no backend com base no valor do serviço selecionado. Esse valor é cravado na tabela `appointments` (`commission_amount`) para histórico imutável (mesmo se a regra do barbeiro mudar no futuro).

## Instalação e Comandos de Rotina

Como o projeto utiliza **Laravel Sail**, **todo** comando deve ser executado pelo container Docker Sail:

```bash
# Subir os containers (Banco, Redis, Mailpit, etc)
vendor/bin/sail up -d

# Instalar as dependências do PHP
vendor/bin/sail composer install

# Instalar e buildar o Frontend (Tailwind/Vite)
vendor/bin/sail npm install
vendor/bin/sail npm run build

# Executar migrações do banco de dados (e seeders se houver)
vendor/bin/sail artisan migrate --seed

# Limpar caches gerais (útil se modificar timezone ou configs cruciais)
vendor/bin/sail artisan optimize:clear
```

## Estrutura Chave de Diretórios

- `app/Livewire/`: Contém os controladores reativos (Ex: `SchedulingWizard`, `CashierTerminal`, `BarberAgenda`, `CollaboratorForm`, etc).
- `resources/views/livewire/`: Contém as interfaces (HTML/Tailwind) conectadas com cada controlador Livewire.
- `app/Models/`: Modelos Eloquent (`User`, `Appointment`, `Service`).
- `database/migrations/`: Arquitetura do banco de dados relacional.
- `routes/web.php`: Regras de roteamento e Middlewares (proteção e ACL baseada em papéis).

---

> **Desenvolvido com IA para a Barbearia Barcelona.**
> Caso outra IA precise manter este sistema, observe o ecossistema Livewire 3 e o gerenciamento de estados no arquivo `.blade.php` via `wire:model`, além das proteções em `mount()` nas classes para restringir acesso.
