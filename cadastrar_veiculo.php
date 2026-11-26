
<?php
require_once 'conexao.php';

function escapar($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$erro = trim($_GET['erro'] ?? '');
$clientes = [];
$erroConsulta = false;

try {
    $resultado = $conn->query(
        "SELECT idClientes, NomeCliente
         FROM Clientes
         ORDER BY NomeCliente ASC"
    );

    while ($cliente = $resultado->fetch_assoc()) {
        $clientes[] = $cliente;
    }

} catch (mysqli_sql_exception $e) {
    error_log('Erro ao carregar clientes: ' . $e->getMessage());
    $erroConsulta = true;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=2">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Veículo | AutoPro OS</title>

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
            padding: 12px 14px;
            outline: none;
            transition: .2s;
            background: #fff;
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
            margin-bottom: 7px;
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
                <div class="w-11 h-11 rounded-xl bg-blue-600 flex items-center justify-center">
                    <i class="fas fa-car text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-wide">AutoPro <span class="text-blue-400">OS</span></h1>
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

            <a href="listar_os.php" class="sidebar-link">
                <i class="fas fa-clipboard-list w-5"></i>
                Ordens de Serviço
            </a>
        </nav>

        <div class="absolute bottom-0 left-0 right-0 p-5 border-t border-slate-800">
            <p class="text-xs text-slate-500">AutoPro OS</p>
            <p class="text-sm text-slate-400 mt-1">Sistema de Gestão Automotiva</p>
        </div>
    </aside>

    <!-- Fundo do menu mobile -->
    <div id="overlay"
         class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden"
         onclick="alternarMenu()"></div>

    <!-- Conteúdo -->
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
                            Cadastrar Veículo
                        </h2>
                        <p class="text-sm text-slate-500 mt-1">
                            Cadastre um veículo e vincule-o a um cliente.
                        </p>
                    </div>
                </div>

                <a href="listar_veiculos.php"
                   class="hidden sm:inline-flex items-center gap-2 text-sm font-semibold
                          text-blue-700 hover:text-blue-900">
                    <i class="fas fa-arrow-left"></i>
                    Voltar à frota
                </a>
            </div>
        </header>

        <section class="p-5 md:p-8 max-w-5xl mx-auto">

            <?php if ($erro !== ''): ?>
                <div class="mb-6 flex items-start gap-3 rounded-xl border border-red-200
                            bg-red-50 p-4 text-red-700">
                    <i class="fas fa-circle-exclamation mt-1"></i>
                    <p class="text-sm font-medium"><?= escapar($erro) ?></p>
                </div>
            <?php endif; ?>

            <?php if ($erroConsulta): ?>
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
                    Não foi possível carregar a lista de clientes. Tente novamente.
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                <div class="p-6 md:p-8 border-b border-slate-100">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600
                                    flex items-center justify-center text-xl">
                            <i class="fas fa-car-side"></i>
                        </div>

                        <div>
                            <h3 class="font-bold text-lg text-slate-800">
                                Dados do veículo
                            </h3>
                            <p class="text-sm text-slate-500">
                                Preencha as informações abaixo.
                            </p>
                        </div>
                    </div>
                </div>

                <form action="processar_veiculo.php" method="POST"
                      class="p-6 md:p-8 space-y-6">

                    <!-- Proprietário -->
                    <div>
                        <label for="cliente_id" class="campo-label">
                            <i class="fas fa-user text-blue-600 mr-1"></i>
                            Proprietário (cliente) *
                        </label>

                        <select name="cliente_id" id="cliente_id"
                                class="campo" required
                                <?= empty($clientes) || $erroConsulta ? 'disabled' : '' ?>>

                            <option value="">Selecione um cliente...</option>

                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= (int) $cliente['idClientes'] ?>">
                                    <?= escapar($cliente['NomeCliente']) ?>
                                </option>
                            <?php endforeach; ?>

                        </select>

                        <?php if (empty($clientes) && !$erroConsulta): ?>
                            <p class="text-xs text-amber-600 mt-2">
                                Nenhum cliente cadastrado. Cadastre um cliente antes de adicionar um veículo.
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Marca e modelo -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>
                            <label for="marca" class="campo-label">
                                Marca *
                            </label>
                            <input type="text" name="marca" id="marca"
                                   class="campo" placeholder="Ex.: Ford"
                                   maxlength="45" required>
                        </div>

                        <div>
                            <label for="modelo" class="campo-label">
                                Modelo *
                            </label>
                            <input type="text" name="modelo" id="modelo"
                                   class="campo" placeholder="Ex.: Ka"
                                   maxlength="45" required>
                        </div>

                    </div>

                    <!-- Placa e ano -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>
                            <label for="placa" class="campo-label">
                                Placa *
                            </label>
                            <input type="text" name="placa" id="placa"
                                   class="campo uppercase"
                                   placeholder="ABC1D23"
                                   maxlength="8" required
                                   autocomplete="off">
                            <p class="text-xs text-slate-400 mt-2">
                                Aceita placas antigas e padrão Mercosul.
                            </p>
                        </div>

                        <div>
                            <label for="ano" class="campo-label">
                                Ano de fabricação
                            </label>
                            <input type="number" name="ano" id="ano"
                                   class="campo"
                                   placeholder="Ex.: 2022"
                                   min="1900"
                                   max="<?= (int) date('Y') + 1 ?>">
                        </div>

                    </div>

                    <div class="border-t border-slate-100 pt-6 flex flex-col-reverse
                                sm:flex-row sm:justify-between gap-3">

                        <a href="listar_veiculos.php"
                           class="inline-flex justify-center items-center gap-2 px-5 py-3
                                  rounded-lg border border-slate-300 text-slate-600
                                  font-semibold hover:bg-slate-50 transition">
                            <i class="fas fa-arrow-left"></i>
                            Cancelar
                        </a>

                        <button type="submit"
                                <?= empty($clientes) || $erroConsulta ? 'disabled' : '' ?>
                                class="inline-flex justify-center items-center gap-2 px-6 py-3
                                       rounded-lg bg-blue-600 text-white font-semibold
                                       hover:bg-blue-700 transition disabled:opacity-50
                                       disabled:cursor-not-allowed">
                            <i class="fas fa-save"></i>
                            Salvar Veículo
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

document.getElementById('placa').addEventListener('input', function () {
    this.value = this.value
        .toUpperCase()
        .replace(/[^A-Z0-9]/g, '')
        .slice(0, 7);
});
</script>

</body>
</html>
