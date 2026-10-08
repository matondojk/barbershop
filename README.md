# Barbearia Barcelona - AI Documentation & System Overview

Este projeto é um sistema completo de gestão para a Barbearia Barcelona, desenvolvido inteiramente com o ecossistema Laravel. Este documento foi atualizado especificamente para fornecer contexto técnico e arquitetural para assistentes de Inteligência Artificial (AIs) que precisem dar manutenção, corrigir bugs ou expandir as funcionalidades deste projeto no futuro.

---

## 🛠 Stack Tecnológica

- **Backend:** PHP 8.2+ / Laravel 11+
- **Autenticação:** Laravel Fortify (gerenciamento de sessão, redirecionamento pós-logout ajustado para `/login`).
- **Frontend / Reatividade:** Livewire 3 (renderização SSR com reatividade sem necessidade de Alpine pesado ou Vue/React).
- **Estilização:** Tailwind CSS (versão 4)
- **Banco de Dados:** MySQL
- **Ambiente Dev:** Laravel Sail (Docker)

---

## 🏗 Arquitetura e Papéis (Roles)

O sistema utiliza um array JSON na coluna `roles` do modelo `User` para controle de acesso (ACL).

1. **Cliente / Público (Guest):**
   - Rota standalone: `/agendar` (renderiza a view `scheduling.blade.php` com o componente `SchedulingWizard`).
   - O `SchedulingWizard` cria automaticamente o registro na tabela `customers` caso o CPF seja novo, ou atualiza se já existir, simplificando o onboarding (sem necessidade de criação de conta manual pelo cliente).
   
2. **Barbeiro (Barber):**
   - **Rota:** `/painel/agenda` (Componente `BarberAgenda`).
   - Tem acesso apenas aos seus próprios agendamentos do dia atual.
   - Fluxo de trabalho (Status do Appointment):
     - `scheduled` (Agendado)
     - `in_progress` (Em atendimento/Na cadeira)
     - `awaiting_payment` (Atendimento finalizado pelo barbeiro, enviado para o caixa)
   - *Nota de UI:* Todos os agendamentos (incluindo `completed`) são visíveis em sua tela diária para histórico, utilizando fundos sólidos (`bg-zinc-900`, `#142217`, `#2a2a2a`) sem transparência para garantir alta visibilidade.

3. **Administrador/Recepcionista (Admin):**
   - **Painel Geral:** `/painel` (Dashboard de visão geral).
   - **Frente de Caixa (PDV):** `/painel/caixa` (Componente `CashierTerminal`).
     - Puxa todos os atendimentos do dia em status `in_progress` e `awaiting_payment`.
     - Finaliza o pagamento e calcula o troco.
     - **Crítico:** Ao finalizar, o sistema altera o status para `completed` e grava o valor real da comissão na coluna `commission_amount` da tabela `appointments` baseando-se no `commission_type` (fixed/percentage) do barbeiro.
   - **Gestão de Cadastros:**
     - `/painel/equipe` (Barbeiros e Admins) - Configura horários de expediente (`work_start_time`, `work_end_time`) essenciais para a geração de horários no Wizard.
     - `/painel/servicos` (Serviços e preços).
     - `/painel/clientes` (Visualização do banco de clientes).

---

## 🎨 UI / UX e Design System

- **Tema:** Dark mode por padrão. Usa tons de `zinc` e preto absoluto (`#0a0a0a`).
- **Accent Color:** Dourado/Amarelo customizado configurado no Tailwind como `barber-primary`.
- **Efeitos (Glassmorphism vs Sólido):**
  - Paineis maiores, containers e navbars utilizam *Glassmorphism* (`bg-[#111]/70 backdrop-blur-md`).
  - Cards de conteúdo interno (ex: cards da agenda) utilizam **cores sólidas** (ex: `bg-zinc-900`, `bg-[#1c1c1c]`) sem `backdrop-blur` para garantir legibilidade e não ficar "transparente demais".
- **Navegação:**
  - Desktop: Sidebar e Topbar clássicos.
  - Mobile: **Bottom Tab Navigation** (fixa na parte inferior) para acesso rápido. Menus de usuário/notificação abrem como sidebars deslizantes (`x-show` via Alpine.js integrado ao Blade) ao invés de dropdowns nativos.

---

## 🗄 Modelo de Dados Principal

- **users:** Sistema híbrido. Pode ser Admin, Barbeiro ou ambos. Possui horários de trabalho (`work_start_time`, etc) e configs de comissão.
- **customers:** Clientes públicos criados pelo Wizard (Nome, CPF, WhatsApp). Não possuem login no painel administrativo.
- **services:** Serviços com preço e duração (`duration_minutes`), os quais impactam diretamente o gerador de horários vagos no `SchedulingWizard`.
- **appointments:** Elo central.
  - Guarda referências: `user_id` (Barbeiro), `customer_id`, `service_id`.
  - Campos de horário: `appointment_date`, `start_time`, `end_time`.
  - Controle financeiro: `status`, `payment_method`, `commission_amount`.

---

## ⚙️ Diretrizes para IAs (Regras de Manutenção)

Quando for modificar ou estender este projeto, siga estritamente estas diretrizes:

1. **Rotas e URLs:**
   - As URLs visíveis ao usuário estão traduzidas para português (ex: `/painel/equipe`), mas **os nomes das rotas (`route('collaborators.index')`) foram mantidos em inglês**. Se adicionar rotas, mantenha o padrão: URL em PT-BR, name route em EN.
2. **Componentes Livewire:**
   - Evite injetar lógica pesada no Blade. Use os métodos do PHP no Livewire.
   - Lembre-se de usar `wire:navigate` em links de menu para transições SPA suaves.
3. **Estilização e Tailwind v4:**
   - Em Tailwind v4, classes com opacidade arbitrária hex (`bg-[#111]/70`) funcionam, mas caso haja bugs visuais de transparência, prefira cores literais (`bg-zinc-900/90`) ou hex sem canal alfa associado a opacidade do próprio elemento.
   - Sempre que alterar Views/Blade, **execute o build do Vite**: `vendor/bin/sail npm run build`. Sem isso, as novas classes Tailwind não refletirão em produção/visualização.
4. **Comandos Docker (Sail):**
   - **NUNCA** execute comandos puros de PHP, Composer ou Artisan. Sempre os anteceda com `vendor/bin/sail`. Exemplo: `vendor/bin/sail artisan route:list`.
5. **Autenticação Fortify:**
   - As respostas de login/logout estão customizadas nos Responses do Fortify em `app/Providers/FortifyServiceProvider.php`.
6. **População de Banco de Dados:**
   - Se a estrutura mudar, atualize o `DatabaseSeeder.php` e os Factories correspondentes. O sistema possui seeders robustos com dados de barbeiros reais, clientes simulados, e appointments retroativos e futuros, com variação de status lógicas.

---
*Gerado por Antigravity AI - Google DeepMind*
