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
   git clone [https://github.com/awaldige/autopro-os.git]
