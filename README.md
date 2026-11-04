# 💈 Sistema de Gestão para Barbearia — PDV, Agendamento Online e Bot de WhatsApp

Sistema completo de gerenciamento para barbearia desenvolvido com o ecossistema **Laravel**, **Livewire** e **Tailwind CSS**, integrado à **Evolution API** para atendimento automatizado, agendamento, remarcação, cancelamento e notificações em tempo real via **WhatsApp**.

---

## 📌 O que é e o que faz o projeto?

O sistema centraliza toda a operação da barbearia em uma única plataforma, atendendo três frentes principais:

1. **Autoatendimento do Cliente (Web e WhatsApp):**
   - O cliente pode agendar seu horário pelo site (`/agendar`) ou diretamente conversando com o **Bot de WhatsApp**.
   - Recebe confirmações instantâneas no WhatsApp com um link exclusivo (`/agendamento/{hash}`) para alterar ou cancelar seu horário quando precisar.
   - Também pode gerenciar, remarcar (horário, data, barbeiro ou serviço) ou cancelar seus agendamentos diretamente pelo WhatsApp.

2. **Operação do Barbeiro (Agenda Diária):**
   - Cada barbeiro possui acesso à sua agenda do dia (`/painel/agenda`), acompanhando seus atendimentos, comissões estimadas e faturamento diário.
   - Controle de status em tempo real: **Agendado (`scheduled`)** $\rightarrow$ **Em Atendimento (`in_progress`)** $\rightarrow$ **Aguardando Pagamento (`awaiting_payment`)**.
   - Recebe notificações no painel (sino em tempo real) e alertas no seu próprio WhatsApp sempre que um cliente agenda, altera, transfere ou cancela um horário.

3. **Administração, Frente de Caixa (PDV) e Financeiro:**
   - **Frente de Caixa (`/painel/caixa`):** Recebe os clientes que finalizaram o corte, permite adicionar produtos consumidos (bebidas, pomadas, etc.) com baixa automática de estoque, aplicar descontos, escolher a forma de pagamento (PIX, Dinheiro, Cartão), calcular o troco e lançar automaticamente a comissão do barbeiro.
   - **Dashboard Financeiro (`/painel/financeiro`):** Relatórios gerenciais por período (Hoje, Semana, Mês, Ano ou Tudo) com faturamento bruto, total de comissões, descontos concedidos, lucro líquido da barbearia, separação entre receita de serviços vs. produtos e ranking dos serviços/produtos mais vendidos.
   - **Gestão Completa:** Cadastro de equipe/colaboradores (com horários de expediente individuais e regras de comissão fixa ou percentual), serviços, estoque de produtos, histórico de clientes e gerenciamento de instâncias do WhatsApp via QR Code.

---

## 🛠️ Stack Tecnológica

- **Linguagem & Framework:** PHP 8.5 + Laravel 13
- **Autenticação:** Laravel Fortify (Login por CPF e Senha)
- **Frontend Reativo:** Livewire 4 + Alpine.js
- **Estilização:** Tailwind CSS v4 (Design Dark Mode com identidade visual dourada `barber-primary`)
- **Banco de Dados:** MySQL 8.4 (Principal) + PostgreSQL (Consulta auxiliar da Evolution API para resolução de `@lid`)
- **Ambiente de Desenvolvimento:** Docker via Laravel Sail
- **Integração WhatsApp:** Evolution API (Integração `WHATSAPP-BAILEYS`) + Filas assíncronas (`SendWhatsAppMessage`)

---

## 🏗️ Arquitetura e Módulos do Sistema

### 1. Papéis e Controle de Acesso (`roles`)
O modelo `User` utiliza uma coluna JSON `roles` que permite múltiplos papéis por colaborador (`['admin', 'barber']`):
- **Admin:** Acesso total ao painel, Frente de Caixa (PDV), Dashboard Financeiro, Equipe, Serviços, Produtos, Clientes e Conexão do WhatsApp.
- **Barbeiro (`barber`):** Acesso à Visão Geral, sua Agenda do Dia (`/painel/agenda`), Lista de Clientes e edição do próprio Perfil.
- **Cliente Público:** Não precisa criar conta ou senha. É identificado automaticamente pelo CPF ou número de WhatsApp na tabela `customers`.

---

### 2. Fluxo de Agendamento e Cálculo Inteligente de Horários
Tanto no Wizard Web (`SchedulingWizard`) quanto no Bot de WhatsApp (`WhatsAppBotService`), o motor de disponibilidade funciona da seguinte forma:
- Considera os dias úteis da semana (Segunda a Sábado). Se hoje for Domingo, exibe automaticamente a próxima semana.
- Lê o expediente individual do barbeiro selecionado (`work_start_time` até `work_end_time`).
- No WhatsApp, permite escolher **"Qualquer Barbeiro Disponível"**, unificando os horários livres de toda a equipe ativa.
- Gera slots de **15 em 15 minutos** e verifica se a **duração total do serviço escolhido (`duration_minutes`)** cabe no intervalo sem colidir com outros agendamentos ativos (`scheduled`, `in_progress`, `awaiting_payment`) e sem ultrapassar o horário de fechamento.
- Filtra horários que já passaram no dia atual e revalida a disponibilidade no exato momento da confirmação para evitar agendamento duplo (*race condition*).

---

### 3. Ciclo de Vida do Agendamento e Frente de Caixa (PDV)
1. **`scheduled` (Agendado):** Criado pelo cliente via site ou WhatsApp.
2. **`in_progress` (Em Atendimento):** O barbeiro inicia o atendimento na sua agenda.
3. **`awaiting_payment` (Aguardando Pagamento):** O barbeiro conclui o serviço e envia o cliente para o caixa.
4. **`completed` (Concluído/Pago):** No módulo **Frente de Caixa (`CashierTerminal`)**, o administrador:
   - Seleciona o atendimento pendente (podendo filtrar por CPF).
   - Adiciona produtos extras vendidos ao cliente (validando e abatendo do `stock_quantity`).
   - Informa eventuais descontos (`discount_amount`) e o valor recebido (`amount_paid`), calculando o troco (`change_amount`).
   - O sistema calcula e grava a comissão do barbeiro (`commission_amount`) **exclusivamente sobre o valor do serviço**, respeitando se o barbeiro recebe por porcentagem (`percentage`) ou valor fixo (`fixed`).
5. **`canceled` / `rejected`:** Agendamentos cancelados pelo cliente ou recusados.

---

## 🤖 Funcionamento Detalhado do Bot de WhatsApp

A arquitetura do WhatsApp foi dividida em **4 camadas** para garantir estabilidade, prevenção de loops e uma experiência fluida:

### Camada 1: Gerenciamento de Instâncias (`EvolutionApiService`, `WhatsappManager`, `WhatsappCreate`)
- Pelo menu `/painel/whatsapp`, o administrador cria instâncias na Evolution API, gera o **QR Code** na tela para leitura com o celular da barbearia, acompanha o status da conexão em tempo real e define qual instância está ativa no banco (`whatsapp_instances`).

### Camada 2: Envio e Normalização (`WhatsAppService` e Job `SendWhatsAppMessage`)
- **Envio Assíncrono e Síncrono:** Notificações do sistema usam o Job `SendWhatsAppMessage` na fila, enquanto as respostas interativas do Bot usam envio direto via `WhatsAppService`.
- **Normalização do 9º Dígito Brasileiro:** Limpa caracteres não numéricos, adiciona o DDI `55` quando necessário e, caso o número possua 12 dígitos (`55` + DDD + 8 dígitos móveis iniciando em `6, 7, 8 ou 9`), insere automaticamente o `9` na posição correta para evitar falhas de entrega no WhatsApp.
- **Trava Anti-Loop no Cache:** Ao enviar qualquer mensagem, o serviço salva no Cache por alguns minutos o hash MD5 do texto (`bot_sent_hash_{md5}`) e o ID da mensagem retornada pela Evolution API (`bot_sent_msg_id_{id}`).

### Camada 3: Recebimento e Tratamento no Webhook (`WhatsAppWebhookController`)
- Rota: `POST /api/webhook/whatsapp`
- **Filtros de Segurança:**
  1. Processa apenas eventos `messages.upsert` / `messages_upsert`.
  2. Ignora mensagens de grupos (`@g.us`) e listas de transmissão/status (`status@broadcast`).
  3. Ignora mensagens cujo ID ou Hash de texto estejam marcados no Cache como enviadas pelo próprio bot (impedindo que o bot converse consigo mesmo).
  4. Permite que o próprio dono do número conectado (`fromMe = true`) teste o bot enviando mensagens para si mesmo sem causar loop infinito.
- **Resolução de Identificador Oculto (`@lid`):**
  - Em versões recentes do WhatsApp/Baileys, alguns contatos chegam com o identificador `@lid` em vez do número `@s.whatsapp.net`.
  - O método `resolveLidToPhone()` conecta-se ao banco PostgreSQL da Evolution API e cruza os registros da tabela `"Contact"` (via `profilePicUrl` ou `pushName`) para descobrir o número de telefone real do cliente.

### Camada 4: Máquina de Estados Conversacional (`WhatsAppBotService`)
O estado da conversa de cada cliente é armazenado no Cache (`whatsapp_bot_session_{telefone}`) com validade de **2 horas**.

#### 🔄 Comando Global de Reinício
A qualquer momento da conversa, se o cliente enviar saudações ou comandos como `oi`, `olá`, `bom dia`, `boa tarde`, `boa noite`, `menu`, `início`, `começar`, `ajuda`, `voltar`, `cancelar` ou `sair`, a sessão é limpa e o **Menu Principal** é exibido.

#### 📋 Opções do Menu Principal (`MENU`)
1. **`1️⃣ Agendar Horário`**
   - **`CHOOSING_SERVICE`:** Lista os serviços ativos com preço e duração em minutos.
   - **`CHOOSING_BARBER`:** Permite escolher `"1 - Qualquer Barbeiro Disponível"` ou um barbeiro específico da equipe.
   - **`CHOOSING_DATE`:** Lista as datas disponíveis da semana (indicando *"Hoje"*, *"Amanhã"*, etc.).
   - **`CHOOSING_TIME`:** Exibe todos os horários livres para a data/barbeiro/serviço escolhidos.
   - **Reconhecimento Automático de Cliente:** O bot busca no banco pelos últimos 9 dígitos do telefone.
     - Se o cliente **já possui cadastro** com Nome e CPF, pula as perguntas de cadastro e vai direto para a confirmação!
     - Se for um **novo cliente**, passa pelas etapas `ASKING_NAME` (oferecendo a opção de digitar `1` para usar o nome do perfil do WhatsApp) e `ASKING_CPF` (validando os 11 dígitos).
   - **`CONFIRMING`:** Mostra o resumo completo do agendamento. Ao digitar `1`, verifica novamente se a vaga continua livre, salva o agendamento, gera o link `/agendamento/{hash}`, envia a confirmação ao cliente e notifica o barbeiro no WhatsApp e no painel.

2. **`2️⃣ Meus Agendamentos (Ver, Alterar ou Cancelar)`**
   - Localiza todos os agendamentos futuros (`scheduled`) vinculados ao WhatsApp do cliente.
   - Se houver mais de um, exibe uma lista numerada (`SELECTING_APPT`) para o cliente escolher qual deseja gerenciar; se houver apenas um, abre-o diretamente (`MANAGING_APPT`).
   - Dentro do gerenciamento do agendamento, o cliente tem **5 opções interativas**:
     - **`1` - Trocar Horário:** Mantém a mesma data, barbeiro e serviço, listando os demais horários livres do dia.
     - **`2` - Trocar Data:** Escolhe um novo dia e, em seguida, o novo horário.
     - **`3` - Trocar Barbeiro:** Escolhe outro profissional (mantendo a data atual caso ele tenha horários livres, ou pedindo nova data).
     - **`4` - Trocar Serviço:** Escolhe outro serviço e recalcula os horários livres de acordo com a nova duração.
     - **`5` - Cancelar Agendamento (`CONFIRM_CANCEL_APPT`):** Solicita confirmação e, ao cancelar, avisa automaticamente o barbeiro responsável via WhatsApp.
   - *(Em todas as alterações de horário, data, serviço ou transferência de barbeiro, os profissionais envolvidos são notificados automaticamente via WhatsApp).*

3. **`3️⃣ Ver Serviços e Valores`**
   - Exibe o catálogo completo de serviços ativos com valores formatados, tempo de duração e descrição.

4. **`4️⃣ Endereço e Contato`**
   - Informa o endereço da barbearia, horário de funcionamento e comodidades do espaço.

---

## 🗄️ Estrutura do Banco de Dados

| Tabela | Modelo | Descrição |
| :--- | :--- | :--- |
| `users` | `User` | Colaboradores (Admins e Barbeiros), CPF, WhatsApp, expediente (`work_start_time`, `work_end_time`) e comissão (`commission_type`, `commission_value`). |
| `customers` | `Customer` | Clientes da barbearia (`name`, `cpf`, `whatsapp`). |
| `services` | `Service` | Serviços oferecidos (`name`, `description`, `price`, `duration_minutes`, `is_active`). |
| `products` | `Product` | Produtos para venda no PDV (`name`, `price`, `stock_quantity`, `is_active`). |
| `appointments` | `Appointment` | Agendamentos, status, valores pagos, descontos, troco, comissão calculada e `manage_hash`. |
| `appointment_product` | *(Pivô)* | Produtos consumidos em cada atendimento (`quantity`, `unit_price`). |
| `whatsapp_instances` | `WhatsappInstance` | Instâncias criadas na Evolution API e controle da instância ativa (`is_active`). |
| `notifications` | *(Nativo)* | Notificações internas exibidas no sino do painel administrativo/barbeiro. |

---

## 🌐 Mapa de Rotas Principais

### Rotas Públicas (`routes/web.php`)
- `GET /` — Landing Page da barbearia com catálogo de serviços.
- `GET /agendar` — Wizard interativo de agendamento online (`SchedulingWizard`).
- `GET /agendamento/{hash}` — Página exclusiva do cliente para remarcar ou cancelar agendamento (`ManageAppointment`).

### Rotas do Painel Autenticado (`/painel/*`)
- `GET /painel` — Visão Geral dos agendamentos do dia (`DashboardOverview`).
- `GET /painel/agenda` — Agenda exclusiva do barbeiro logado (`BarberAgenda`).
- `GET /painel/caixa` — Terminal de Frente de Caixa / PDV (`CashierTerminal`).
- `GET /painel/financeiro` — Dashboard e relatórios financeiros (`FinancialDashboard`).
- `GET /painel/equipe` — Listagem, criação e edição de colaboradores (`CollaboratorList` / `CollaboratorForm`).
- `GET /painel/servicos` — Gestão de serviços (`ServiceList` / `ServiceForm`).
- `GET /painel/produtos` — Gestão de produtos e estoque (`ProductList` / `ProductForm`).
- `GET /painel/clientes` — Base de clientes e histórico de atendimentos concluídos (`CustomerList`).
- `GET /painel/whatsapp` — Gerenciador de instâncias e QR Code do WhatsApp (`WhatsappManager` / `WhatsappCreate`).
- `GET /painel/perfil` — Edição de dados e senha do usuário logado (`ProfileForm`).

### Rota de API (`routes/api.php`)
- `POST /api/webhook/whatsapp` — Endpoint que recebe os eventos da Evolution API (`WhatsAppWebhookController@handle`).

---

## 🚀 Como Executar o Projeto Localmente

O projeto utiliza **Laravel Sail (Docker)**. Todos os comandos `php`, `artisan`, `composer` e `npm` devem ser executados através do `vendor/bin/sail`.

### 1. Subir os containers Docker
```bash
./vendor/bin/sail up -d
```

### 2. Instalar dependências e compilar os assets
```bash
./vendor/bin/sail composer install
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

### 3. Configurar variáveis de ambiente (`.env`)
Certifique-se de configurar as credenciais do banco de dados e da **Evolution API** no arquivo `.env`:
```env
EVOLUTION_API_URL=http://host.docker.internal:8090
EVOLUTION_API_KEY=sua_chave_da_evolution_api
EVOLUTION_INSTANCE=wp_barber
```

### 4. Executar as Migrations e Seeders
```bash
./vendor/bin/sail artisan migrate --seed
```
> **Credenciais padrão criadas pelo `DatabaseSeeder`:**
> - **CPF:** `111.111.111-11` (ou `11111111111`)
> - **Senha:** `111111`
> - **Papéis:** Administrador e Barbeiro (`['admin', 'barber']`)

### 5. Processar Filas (Envio de Notificações WhatsApp)
Para que os disparos assíncronos de WhatsApp (`SendWhatsAppMessage`) sejam processados em segundo plano:
```bash
./vendor/bin/sail artisan queue:work
```
*(Ou utilize `./vendor/bin/sail composer run dev` para rodar servidor, fila, logs e Vite simultaneamente).*
