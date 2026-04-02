# 🛠️ AutoPro OS - Sistema de Gestão Automotiva

O **AutoPro** é uma solução Full-Stack desenvolvida para modernizar o fluxo de trabalho em oficinas mecânicas. O projeto substitui processos manuais por uma interface inteligente que prioriza o que é urgente e automatiza cálculos financeiros complexos.

---

## 🎯 O Diferencial do Projeto
Diferente de sistemas básicos de CRUD, o AutoPro foca na **experiência do usuário (UX)** e na **gestão de prazos**:
- **Inteligência de Prazos:** O sistema monitora a data de entrega e sinaliza visualmente (via animações e cores) ordens de serviço críticas.
- **Dinamismo em Tela:** Utilização de *Event Delegation* em JavaScript para manipular tabelas de itens (peças/serviços) sem necessidade de refresh ou chamadas constantes ao servidor.
- **Relatórios Visuais:** Dashboard com KPIs (Indicadores Chave de Desempenho) como faturamento mensal e taxa de pendências.

---

## 🚀 Funcionalidades Principais

| Recurso | Descrição |
| :--- | :--- |
| **Dashboard** | Visão geral de faturamento, clientes ativos e alertas de OS atrasadas. |
| **Editor Dinâmico** | Adição de múltiplos itens com cálculo de subtotal e total em tempo real. |
| **Gestão de Status** | Fluxo de trabalho completo: Em Aberto -> Em Andamento -> Concluída -> Cancelada. |
| **Gerador de PDF/Print** | Layout otimizado para impressão de orçamentos e comprovantes profissionais. |
| **Banco Relacional** | Estrutura de dados robusta ligando Clientes, Veículos e Itens de Serviço. |

---

## 💻 Stack Técnica

* **Back-end:** PHP 8.2 (Arquitetura limpa e proteção contra SQL Injection básica)
* **Front-end:** Tailwind CSS 3.0 (Design responsivo e Dark Mode friendly)
* **Database:** MySQL (Relacionamentos com integridade referencial - *Foreign Keys*)
* **Interatividade:** JavaScript Vanilla (Cálculos de DOM e máscaras de dados)

---

## 📸 Demonstração Visual

![Captura de tela 2026-04-02 124049](https://github.com/user-attachments/assets/6f5fc6bf-0c29-417c-8b51-ecf655b371aa)
![Captura de tela 2026-04-02 124118](https://github.com/user-attachments/assets/21d6b3ce-a6a2-49bf-b29e-d6fa5c769b18)
![Captura de tela 2026-04-02 124148](https://github.com/user-attachments/assets/3c2577b3-384e-4517-9760-163705408ea2)
![Captura de tela 2026-04-02 124221](https://github.com/user-attachments/assets/70d09c10-552e-4087-be5b-fa7d0caece5a)
![Captura de tela 2026-04-02 124238](https://github.com/user-attachments/assets/f876952f-09dd-4494-81ab-8e6a6e0700d2)
![Captura de tela 2026-04-02 124408](https://github.com/user-attachments/assets/5c9d1abc-5df4-430f-ba94-8d9c3927f818)
![Captura de tela 2026-04-02 124327](https://github.com/user-attachments/assets/42793867-56dd-4267-8180-7a9ebbe6aa8f)




---

## 🛠️ Como Instalar e Rodar

1. **Clonar o repositório:**
   ```bash
   git clone [https://github.com/awaldige/autopro-os.git]

   O Gemini disse
Para elevar o nível do seu portfólio, o README precisa ser "escaneável" e mostrar que você pensou no problema de negócio que o código resolve. Recrutadores adoram ver tópicos sobre instalação e diferenciais técnicos.

Aqui está uma versão otimizada, com uma estrutura mais profissional e organizada:

Markdown
# 🛠️ AutoPro OS - Sistema de Gestão Automotiva

O **AutoPro** é uma solução Full-Stack desenvolvida para modernizar o fluxo de trabalho em oficinas mecânicas. O projeto substitui processos manuais por uma interface inteligente que prioriza o que é urgente e automatiza cálculos financeiros complexos.

---

## 🎯 O Diferencial do Projeto
Diferente de sistemas básicos de CRUD, o AutoPro foca na **experiência do usuário (UX)** e na **gestão de prazos**:
- **Inteligência de Prazos:** O sistema monitora a data de entrega e sinaliza visualmente (via animações e cores) ordens de serviço críticas.
- **Dinamismo em Tela:** Utilização de *Event Delegation* em JavaScript para manipular tabelas de itens (peças/serviços) sem necessidade de refresh ou chamadas constantes ao servidor.
- **Relatórios Visuais:** Dashboard com KPIs (Indicadores Chave de Desempenho) como faturamento mensal e taxa de pendências.

---

## 🚀 Funcionalidades Principais

| Recurso | Descrição |
| :--- | :--- |
| **Dashboard** | Visão geral de faturamento, clientes ativos e alertas de OS atrasadas. |
| **Editor Dinâmico** | Adição de múltiplos itens com cálculo de subtotal e total em tempo real. |
| **Gestão de Status** | Fluxo de trabalho completo: Em Aberto -> Em Andamento -> Concluída -> Cancelada. |
| **Gerador de PDF/Print** | Layout otimizado para impressão de orçamentos e comprovantes profissionais. |
| **Banco Relacional** | Estrutura de dados robusta ligando Clientes, Veículos e Itens de Serviço. |

---

## 💻 Stack Técnica

* **Back-end:** PHP 8.2 (Arquitetura limpa e proteção contra SQL Injection básica)
* **Front-end:** Tailwind CSS 3.0 (Design responsivo e Dark Mode friendly)
* **Database:** MySQL (Relacionamentos com integridade referencial - *Foreign Keys*)
* **Interatividade:** JavaScript Vanilla (Cálculos de DOM e máscaras de dados)

---

## 📸 Demonstração Visual

### Painel Principal (Dashboard)
> *Destaque para o sistema de alertas em vermelho para serviços fora do prazo.*
[Link ou Imagem do seu Dashboard]

### Editor de Itens
> *Interface interativa para gerenciamento de peças e mão de obra.*
[Link ou Imagem do Editor de Itens]

---

## 🛠️ Como Instalar e Rodar

1. **Clonar o repositório:**
   ```bash
   git clone [https://github.com/seu-usuario/autopro-os.git](https://github.com/seu-usuario/autopro-os.git)
Configurar o Banco de Dados:

2.Importe o arquivo database.sql no seu servidor MySQL (XAMPP, WAMP, Docker, etc).

3.Configurar Conexão:

Renomeie o arquivo conexao.example.php para conexao.php.

Edite as credenciais de host, user, pass e dbname.

4.Acessar o sistema:

Abra no navegador via (http://awaldige.infinityfree.me/autopro/)

📄 Licença
Distribuído sob a licença MIT. Veja LICENSE para mais informações.

Desenvolvido com ☕ e PHP por André Waldige
