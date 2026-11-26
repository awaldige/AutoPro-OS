# 🚗 AutoPro OS — Sistema de Gestão para Oficinas Mecânicas

O **AutoPro OS** é um sistema web desenvolvido para auxiliar na administração de oficinas mecânicas, centralizando o gerenciamento de clientes, veículos e ordens de serviço em uma interface moderna e intuitiva.

O projeto passou por uma atualização com foco em organização, experiência do usuário, segurança das operações e facilidade de utilização.

Desenvolvido por **AW TECHNOLOGY**, o AutoPro OS faz parte do portfólio de projetos voltados ao desenvolvimento de soluções web e sistemas administrativos.

---

## 🎯 Objetivo do Projeto

Demonstrar a aplicação prática de tecnologias web na construção de um sistema de gestão, oferecendo recursos para organizar as informações da oficina e acompanhar os serviços realizados.

## ✨ Funcionalidades

### 📊 Dashboard
- Painel inicial com indicadores e informações gerais.
- Resumo das operações da oficina.
- Acesso rápido às principais funcionalidades.
- Interface organizada e responsiva.

### 👥 Gestão de Clientes
- Cadastro de clientes.
- Listagem e consulta de registros.
- Pesquisa de clientes.
- Edição de informações cadastradas.
- Validação e tratamento dos dados.

### 🚘 Gestão de Veículos
- Cadastro de veículos.
- Associação de veículos aos respectivos clientes.
- Listagem e pesquisa de veículos.
- Validação de placas e prevenção de duplicidades.

### 🛠️ Ordens de Serviço
- Abertura de novas ordens de serviço.
- Associação da OS a um veículo.
- Registro de número, descrição, valor e datas.
- Controle de status.
- Consulta e filtragem das ordens cadastradas.
- Edição das informações e dos itens da OS.
- Exclusão com validação de segurança.
- Visualização e impressão da ordem de serviço.

---

## 🛡️ Segurança e Boas Práticas

Durante o desenvolvimento foram aplicadas práticas para melhorar a segurança e a confiabilidade das operações:

- Consultas SQL preparadas com prepared statements.
- Validação de dados recebidos pelos formulários.
- Tratamento de dados exibidos na interface para reduzir riscos de XSS.
- Proteção CSRF nas operações que exigem essa validação.
- Restrição de métodos HTTP nas operações de escrita.
- Tratamento de erros e transações nas operações de edição de ordens de serviço.
- Separação das configurações privadas do banco de dados dos arquivos versionados.

---

## 🛠️ Tecnologias Utilizadas

| Tecnologia | Aplicação |
|---|---|
| PHP | Backend e regras de negócio |
| MySQL / MariaDB | Banco de dados relacional |
| HTML5 | Estrutura das páginas |
| CSS3 | Estilização das interfaces |
| Tailwind CSS | Construção da interface visual |
| JavaScript | Interações no frontend |
| Git e GitHub | Versionamento do projeto |

---

## 📂 Estrutura do Projeto

O projeto possui uma estrutura simplificada, com os arquivos PHP organizados na raiz do repositório.

| Arquivo | Finalidade |
|---|---|
| `index.php` | Dashboard principal |
| `conexao.php` | Gerenciamento da conexão com o banco |
| `config.local.php` | Configuração privada do banco (não versionada) |
| `.env.example` | Exemplo de variáveis de ambiente |
| `database-estrutura.sql` | Estrutura do banco de dados |
| `favicon.svg` | Ícone vetorial do sistema |
| `favicon.ico` | Ícone compatível com navegadores tradicionais |
| `cadastrar_cliente.php` | Cadastro de clientes |
| `listar_clientes.php` | Listagem e pesquisa de clientes |
| `editar_cliente.php` | Formulário de edição de clientes |
| `atualizar_cliente.php` | Processamento da atualização de clientes |
| `cadastrar_veiculo.php` | Cadastro de veículos |
| `processar_veiculo.php` | Processamento do cadastro de veículos |
| `listar_veiculos.php` | Listagem de veículos |
| `nova_os.php` | Abertura de ordem de serviço |
| `processar_os.php` | Processamento de novas ordens |
| `listar_os.php` | Consulta e gerenciamento de ordens |
| `editar_os.php` | Edição de ordens de serviço |
| `excluir_os.php` | Exclusão protegida de ordens |
| `imprimir_os.php` | Visualização para impressão |

---

## 📸 Demonstração Visual

As imagens abaixo apresentam telas da versão anterior do projeto. A interface foi posteriormente modernizada.

![Dashboard](https://github.com/user-attachments/assets/6f5fc6bf-0c29-417c-8b51-ecf655b371aa)

![Tela 2](https://github.com/user-attachments/assets/21d6b3ce-a6a2-49bf-b29e-d6fa5c769b18)

![Tela 3](https://github.com/user-attachments/assets/3c2577b3-384e-4517-9760-163705408ea2)

![Tela 4](https://github.com/user-attachments/assets/70d09c10-552e-4087-be5b-fa7d0caece5a)

![Tela 5](https://github.com/user-attachments/assets/f876952f-09dd-4494-81ab-8e6a6e0700d2)

![Tela 6](https://github.com/user-attachments/assets/5c9d1abc-5df4-430f-ba94-8d9c3927f818)

![Tela 7](https://github.com/user-attachments/assets/42793867-56dd-4267-8180-7a9ebbe6aa8f)

---

## 🚀 Como Executar Localmente

### 1. Pré-requisitos

- PHP 8.0 ou superior.
- MySQL ou MariaDB.
- Extensão PHP MySQLi habilitada.
- XAMPP, Laragon ou ambiente equivalente.
- Git (opcional, para clonar o repositório).

### 2. Clonar o repositório

```bash
git clone https://github.com/awaldige/AutoPro-OS.git
```

Entre na pasta:

```bash
cd AutoPro-OS
```

### 3. Configurar o banco de dados

Crie um banco de dados vazio no MySQL ou MariaDB.

Importe o arquivo:

```text
database-estrutura.sql
```

Esse arquivo contém a estrutura do banco para preparar o ambiente. Ele não deve ser tratado como uma cópia de dados reais de uma oficina.

### 4. Configurar a conexão

O arquivo `conexao.php` carrega automaticamente as configurações privadas de `config.local.php`.

Crie o arquivo `config.local.php` na raiz do projeto, utilizando o modelo abaixo e substituindo os valores pelos dados do seu ambiente:

```php
<?php

return [
    'host' => '127.0.0.1',
    'user' => 'seu_usuario',
    'pass' => 'sua_senha',
    'database' => 'nome_do_banco',
    'port' => 3306
];
```

Ajuste o endereço, usuário, senha, nome do banco e porta conforme a configuração do seu MySQL ou MariaDB.

**Importante:**
- O arquivo `config.local.php` é privado e não deve ser enviado ao GitHub.
- Não coloque senhas reais em arquivos versionados.
- O `.gitignore` já possui regras para proteger esse arquivo.
- O arquivo `.env.example` é apenas uma referência de variáveis; a conexão atual utiliza `config.local.php`.

### 5. Executar a aplicação

Com o servidor web e o banco de dados iniciados, acesse o projeto pelo navegador.

Uma alternativa é utilizar o servidor embutido do PHP, executando na pasta do projeto:

```bash
php -S 127.0.0.1:8000
```

Depois, acesse:

```text
http://127.0.0.1:8000
```

---

## 🌐 Configuração em Hospedagem

Para utilizar o sistema em uma hospedagem, crie um `config.local.php` próprio no ambiente de produção.

Utilize os dados de conexão MySQL fornecidos pelo painel da hospedagem, incluindo host, usuário, senha, banco de dados e porta, quando aplicável.

O arquivo deve permanecer privado e fora do versionamento do Git.

A configuração local e a configuração de produção devem ser independentes.

---

## 🔮 Possíveis Melhorias Futuras

- Cadastro de usuários e níveis de acesso.
- Histórico completo de serviços por veículo.
- Gestão de peças e estoque.
- Relatórios gerenciais por período.
- Exportação de relatórios.
- Notificações de prazos e entregas.
- Aprimoramentos adicionais para dispositivos móveis.

---

## 👨‍💻 Autor

**André Waldige**
Desenvolvedor Full Stack — **AW TECHNOLOGY**

- GitHub: https://github.com/awaldige
- Portfólio: https://andre-waldige.vercel.app/

---

## 📄 Licença

Consulte o arquivo de licença do repositório para conhecer as condições de uso e distribuição do projeto.
