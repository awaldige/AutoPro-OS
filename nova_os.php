
<?php

require_once 'conexao.php';

function escapar($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$erro = trim($_GET['erro'] ?? '');
$veiculos = [];
$erroConsulta = false;

try {
    $resultado = $conn->query(
        "SELECT
            V.idVeiculos,
            V.Marca,
            V.Modelo,
            V.Placa,
            C.NomeCliente
         FROM Veiculos V
         LEFT JOIN Clientes C
            ON V.Clientes_idClientes = C.idClientes
         ORDER BY V.Placa ASC"
    );

    while ($veiculo = $resultado->fetch_assoc()) {
        $veiculos[] = $veiculo;
    }

} catch (mysqli_sql_exception $e) {
    error_log('Erro ao carregar veículos para OS: ' . $e->getMessage());
    $erroConsulta = true;
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=2">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Nova Ordem de Serviço | AutoPro OS</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            font-family: Inter, ui-sans-serif, system-ui, sans-serif;
            background: #f1f5f9;
        }

        .campo {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 13px 14px;
            outline: none;
            background: #fff;
            transition: .2s;
            color: #334155;
        }

        .campo:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
        }

        .campo-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 9px;
            color: #cbd5e1;
            transition: .2s;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: #1e40af;
            color: #fff;
        }
    </style>
</head>

<body class="min-h-screen">

<div class="min-h-screen flex">

    <!-- Menu lateral -->
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-950 text-white
                  transform -translate-x-full transition-transform duration-300
                  lg:translate-x-0 lg:static lg:flex-shrink-0">

        <div class="p-6 border-b border-slate-800">
            <div class="flex items-center gap-3">

                <div class="w-11 h-11 rounded-xl bg-blue-600
                            flex items-center justify-center">
                    <i class="fas fa-car text-xl"></i>
                </div>

                <div>
                    <h1 class="text-xl font-bold tracking-wide">
                        AutoPro <span class="text-blue-400">OS</span>
                    </h1>
                    <p class="text-xs text-slate-400">Gestão de Oficina</p>
                </div>

            </div>
        </div>

        <nav class="p-4 space-y-2">

            <p class="text-xs uppercase tracking-wider text-slate-500 px-3 mb-3">
                Principal
            </p>

            <a href="index.php" class="sidebar-link">
                <i class="fas fa-chart-line w-5"></i>
                Painel
            </a>

            <a href="listar_clientes.php" class="sidebar-link">
                <i class="fas fa-users w-5"></i>
                Clientes
            </a>

            <a href="listar_veiculos.php" class="sidebar-link">
                <i class="fas fa-car w-5"></i>
                Veículos
            </a>

            <a href="listar_os.php" class="sidebar-link active">
                <i class="fas fa-clipboard-list w-5"></i>
                Ordens de Serviço
            </a>

        </nav>

        <div class="absolute bottom-0 left-0 right-0 p-5 border-t border-slate-800">
            <p class="text-xs text-slate-500">AutoPro OS</p>
            <p class="text-sm text-slate-400 mt-1">
                Sistema de Gestão Automotiva
            </p>
        </div>

    </aside>

    <!-- Overlay mobile -->
    <div id="overlay"
         class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden"
         onclick="alternarMenu()"></div>

    <!-- Conteúdo principal -->
    <main class="flex-1 min-w-0">

        <header class="bg-white border-b border-slate-200 px-5 md:px-8 py-5">

            <div class="flex items-center justify-between gap-4">

                <div class="flex items-center gap-3">

                    <button onclick="alternarMenu()"
                            class="lg:hidden text-slate-600 text-xl"
                            aria-label="Abrir menu">
                        <i class="fas fa-bars"></i>
                    </button>

                    <div>
                        <h2 class="text-xl md:text-2xl font-bold text-slate-800">
                            Nova Ordem de Serviço
                        </h2>

                        <p class="text-sm text-slate-500 mt-1">
                            Registre um novo atendimento da oficina.
                        </p>
                    </div>

                </div>

                <a href="listar_os.php"
                   class="hidden sm:inline-flex items-center gap-2
                          text-sm font-semibold text-blue-700 hover:text-blue-900">
                    <i class="fas fa-arrow-left"></i>
                    Voltar às OS
                </a>

            </div>

        </header>

        <section class="p-5 md:p-8 max-w-5xl mx-auto">

            <?php if ($erro !== ''): ?>
                <div class="mb-6 flex items-start gap-3 rounded-xl
                            border border-red-200 bg-red-50 p-4 text-red-700">

                    <i class="fas fa-circle-exclamation mt-1"></i>

                    <p class="text-sm font-medium">
                        <?= escapar($erro) ?>
                    </p>

                </div>
            <?php endif; ?>

            <?php if ($erroConsulta): ?>
                <div class="mb-6 rounded-xl border border-red-200
                            bg-red-50 p-4 text-red-700">
                    Não foi possível carregar os veículos. Tente novamente.
                </div>
            <?php endif; ?>

            <!-- Formulário -->
            <div class="bg-white rounded-2xl border border-slate-200
                        shadow-sm overflow-hidden">

                <div class="p-6 md:p-8 border-b border-slate-100">

                    <div class="flex items-center gap-4">

                        <div class="w-12 h-12 rounded-xl bg-blue-50
                                    text-blue-600 flex items-center
                                    justify-center text-xl">
                            <i class="fas fa-file-circle-plus"></i>
                        </div>

                        <div>
                            <h3 class="font-bold text-lg text-slate-800">
                                Dados da Ordem de Serviço
                            </h3>

                            <p class="text-sm text-slate-500">
                                Preencha as informações do novo atendimento.
                            </p>
                        </div>

                    </div>

                </div>

                <form action="processar_os.php"
                      method="POST"
                      class="p-6 md:p-8 space-y-6">

                    <!-- Veículo -->
                    <div>

                        <label for="veiculo_id" class="campo-label">
                            <i class="fas fa-car text-blue-600 mr-1"></i>
                            Veículo *
                        </label>

                        <select name="veiculo_id"
                                id="veiculo_id"
                                class="campo"
                                required
                                <?= empty($veiculos) || $erroConsulta ? 'disabled' : '' ?>>

                            <option value="">
                                Selecione o veículo...
                            </option>

                            <?php foreach ($veiculos as $veiculo): ?>

                                <option value="<?= (int) $veiculo['idVeiculos'] ?>">
                                    <?= escapar($veiculo['Placa']) ?>
                                    -
                                    <?= escapar($veiculo['Marca']) ?>
                                    <?= escapar($veiculo['Modelo']) ?>
                                    (<?= escapar($veiculo['NomeCliente'] ?? 'Sem proprietário vinculado') ?>)
                                </option>

                            <?php endforeach; ?>

                        </select>

                        <?php if (empty($veiculos) && !$erroConsulta): ?>
                            <p class="text-xs text-amber-600 mt-2">
                                Nenhum veículo cadastrado. Cadastre um veículo antes de abrir uma OS.
                            </p>
                        <?php endif; ?>

                    </div>

                    <!-- Número e data -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>

                            <label for="numero_os" class="campo-label">
                                <i class="fas fa-hashtag text-blue-600 mr-1"></i>
                                Número da OS *
                            </label>

                            <input type="number"
                                   name="numero_os"
                                   id="numero_os"
                                   class="campo font-bold text-blue-700"
                                   placeholder="Ex.: 1001"
                                   min="1"
                                   required>

                        </div>

                        <div>

                            <label for="data_entrega" class="campo-label">
                                <i class="fas fa-calendar-check text-blue-600 mr-1"></i>
                                Previsão de entrega
                            </label>

                            <input type="date"
                                   name="data_entrega"
                                   id="data_entrega"
                                   value="<?= escapar(date('Y-m-d', strtotime('+1 day'))) ?>"
                                   class="campo">

                        </div>

                    </div>

                    <!-- Valor -->
                    <div>

                        <label for="valor" class="campo-label">
                            <i class="fas fa-dollar-sign text-blue-600 mr-1"></i>
                            Valor inicial da OS *
                        </label>

                        <input type="number"
                               name="valor"
                               id="valor"
                               class="campo"
                               placeholder="0,00"
                               min="0"
                               max="99999999.99"
                               step="0.01"
                               value="0.00"
                               required>

                        <p class="text-xs text-slate-400 mt-2">
                            Você poderá atualizar o valor conforme os serviços e peças forem adicionados.
                        </p>

                    </div>

                    <!-- Descrição -->
                    <div>

                        <label for="descricao" class="campo-label">
                            <i class="fas fa-align-left text-blue-600 mr-1"></i>
                            Descrição / Reclamação do cliente
                        </label>

                        <textarea name="descricao"
                                  id="descricao"
                                  rows="5"
                                  maxlength="10000"
                                  class="campo resize-y"
                                  placeholder="Descreva o problema relatado pelo cliente ou o serviço solicitado..."></textarea>

                        <p class="text-xs text-slate-400 mt-2">
                            Registre os detalhes iniciais do atendimento.
                        </p>

                    </div>

                    <!-- Status inicial -->
                    <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">

                        <div class="flex items-center gap-3">

                            <div class="w-10 h-10 rounded-lg bg-white
                                        text-blue-600 flex items-center
                                        justify-center">
                                <i class="fas fa-clock"></i>
                            </div>

                            <div>
                                <p class="text-sm font-semibold text-slate-800">
                                    Status inicial
                                </p>

                                <p class="text-sm text-blue-700 font-medium">
                                    Em Aberto
                                </p>
                            </div>

                        </div>

                        <p class="text-xs text-slate-500 mt-3">
                            O sistema definirá automaticamente esse status ao criar a OS.
                        </p>

                    </div>

                    <!-- Ações -->
                    <div class="border-t border-slate-100 pt-6
                                flex flex-col-reverse sm:flex-row
                                sm:justify-between gap-3">

                        <a href="listar_os.php"
                           class="inline-flex justify-center items-center gap-2
                                  px-5 py-3 rounded-lg border border-slate-300
                                  text-slate-600 font-semibold hover:bg-slate-50 transition">

                            <i class="fas fa-arrow-left"></i>
                            Cancelar

                        </a>

                        <button type="submit"
                                <?= empty($veiculos) || $erroConsulta ? 'disabled' : '' ?>
                                class="inline-flex justify-center items-center gap-2
                                       px-6 py-3 rounded-lg bg-blue-600 text-white
                                       font-semibold hover:bg-blue-700 transition
                                       disabled:opacity-50 disabled:cursor-not-allowed">

                            <i class="fas fa-check-circle"></i>
                            Criar Ordem de Serviço

                        </button>

                    </div>

                    <p class="text-xs text-slate-400">
                        * Campos obrigatórios.
                    </p>

                </form>

            </div>

        </section>

    </main>

</div>

<script>
function alternarMenu() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}
</script>

</body>
</html>
